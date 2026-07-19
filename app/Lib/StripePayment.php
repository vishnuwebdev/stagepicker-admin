<?php
namespace App\Lib;

use App\Models\User;
//~ use App\Models\Orders;
require_once('vendor/stripe/init.php');
//email : deen@abstarctsoftweb.com
//password : av;3WeMyPfC'^%ye
//https://phppot.com/php/stripe-payment-gateway-integration-using-php/
//https://www.codexworld.com/stripe-payment-gateway-integration-php/
//https://www.phpzag.com/stripe-payment-gateway-integration-in-php/
class StripePayment {

    public $secret_key = '';

    function __construct() {
      	 $this->secret_key = config('app.stripe_secret');
        \Stripe\Stripe::setApiKey($this->secret_key);
    
    }

    /**
     * this function will return user's saved cards 
     * @param  user
     * @return cards array
     */
    public static function getSavedCards($user) {
        $cards = array();
        if (isset($user->id)) {
            if ($user->stripe_token != null) {
                $cards = \Stripe\Customer::retrieve($user->stripe_token)->sources->all()->data;
            }
        }
        return $cards;
    }

    public function addCustomer($customerDetailsAry)
    {
        
      //  $customer = new Customer();
        
        $customerDetails = \Stripe\Customer::create($customerDetailsAry);
        
        return $customerDetails;
    }

    public function chargeAmountFromCard($cardDetails)
    {
        $customerDetailsAry = array(
            'email' => $cardDetails['email'],
            'source' => $cardDetails['stripe_token'],
            'name' => $cardDetails['full_name'],
            "address" => ["city" => 'Alliance', "country" => 'US', "line1" => 'jaipur', "line2" => "", "postal_code" => '69301', "state" => 'NE'],
        );
        $customerResult = $this->addCustomer($customerDetailsAry);
       // $charge = new Charge();
        $cardDetailsAry = array(
            'customer' => $customerResult->id,
            'amount' => $cardDetails['price']*100 ,
            'currency' => 'USD',//$cardDetails['currency_code'],
            'description' => 'Order details by payments',//$cardDetails['user_type'].'_'.$cardDetails['subscription_type'],
            'metadata' => array(
                'order_id' => $cardDetails['order_id']
            )
        );
        $result = \Stripe\Charge::create($cardDetailsAry);

        return $result->jsonSerialize();
    }

    /**
     * this function will create stripe customer and return customer object
     * @param  request object, user
     * @return customer object
     */
    public static function createCustomer($user) 
    {
        $customer = \Stripe\Customer::create(array(
                    "description" => "Customer Name : " . $user['first_name'], 
                    "email" => $user['email']
                  
        ));
        return $customer;
    }
    
    public function createConnectAccount($email=null,$type='custom')
    { 
        try {
            $response = \Stripe\Account::create(array(
                "type" => $type,
                "email" => $email,
            ));
            return $response;
        }catch (\Exception $e) {
            $msg = $e->getMessage();
            if($msg == 'An account with this email already exists.'){
                
            }
            return $msg;
        }
        
    }
    public function createConnectCustomAccount($params=[])
    {
        try {
            
            $response = \Stripe\Account::create(array(
                "type" => "custom",
                "email" => $params['email'],
                "country" => "US",
                "external_account" => array(
                    "object" => "bank_account",
                    "country" => "US",
                    "currency" => "usd",
                    "routing_number" => $params['routing_number'],
                    "account_number" => $params['account_number'],
                ),
                "tos_acceptance" => array(
                    "date" => time(),
                    "ip" => $params['ip']
                ),
                'legal_entity'=> $params['verification']
            ));
            return $response;
        }catch (\Exception $e) {
            $msg = $e->getMessage();
            return $msg;
        }
    }
    
    public function fileUpload($file_path, $purpose)
    {
        $fp = fopen($file_path, 'r');
        $response = \Stripe\FileUpload::create([
            'file' => $fp,
            'purpose' => $purpose
        ]);
        return $response;
    }
    
    
    
    

    /**
     * this function will complete the payment process and return the charge id
     * @param  subscription, request object, user
     * @return object
     */
    public static function makePayment($amount, $customer_key, $card_key, $currency) {
        try {
            $charge = array(
                "amount" => $amount,
                "currency" => $currency,
                "description" => "ok"
            );
            $charge['customer'] = $customer_key;
            $charge['card'] = $card_key;
            $charges = \Stripe\Charge::create($charge);





            return $charges;
        } catch (\Exception $e) {
            return $msg = $e->getMessage();
              
        }
    }
    
    
    /*
    public static function makeHoldPayment($amount, $customer_key, $card_key, $currency) {
        try {
            $charge = array(
                "amount" => $amount,
                "currency" => $currency,
                "description" => "ok",
                'capture' => false
            );
            $charge['customer'] = $customer_key;
            $charge['card'] = $card_key;
            $charges = \Stripe\Charge::create($charge);
            return $charges;
        } catch (\Exception $e) {
            return $msg = $e->getMessage();
              
        }
    }
    */
    public static function createToken($customer_key, $card_key, $connect_account){
        $token = \Stripe\Token::create(array(
            "customer" => $customer_key,
            'card'=>$card_key
        ), array("stripe_account" => $connect_account));
        return $token;   
    }

    public static function makeHoldPayment($amount, $customer_key, $card_key, $currency, $connect_account, $application_fee) {
        try {
            $token = self::createToken($customer_key, $card_key, $connect_account);
            $charge = \Stripe\Charge::create(array(
                        "amount" => $amount,
                        "currency" => $currency,
                        "source" => $token->id,
                        "application_fee" => $application_fee,
                        'capture' => false
                    ), 
                    array(
                        "stripe_account" => $connect_account
                    )
            );
            return $charge;
        } catch (\Exception $e) {
            return $msg = $e->getMessage();
              
        }
    }
    
    
    public function caputureHoldedCharge($charge_id=null,$stripe_account){
		 try {
        $charge = \Stripe\Charge::retrieve($charge_id, ["stripe_account" => $stripe_account]);
        $charge = $charge->capture();
        return $charge;
         } catch (\Exception $e) {
            return $msg = $e->getMessage();
              
        }
    }
    
    
    public function refundCharge($charge_id=null, $stripe_account){
        $refund = \Stripe\Refund::create([
            'charge' => $charge_id,
        ],
        ["stripe_account" => $stripe_account]);
        return $refund;
    }
    
    /**
     * this function will delete user's card
     * @param  card
     * @return card details
     */
    public static function deleteCard($card, $user) {
        if (isset($user->id)) {
            if ($user->stripe_token != null) {
                $cards = \Stripe\Customer::retrieve($user->stripe_token)->sources->retrieve($card);
                if (isset($cards->id)) {
                    $delete = '';
                    if ($delete = $cards->delete())
                        return $delete;
                    else
                        return "unable to delete card";
                } else
                    return "card not available";
            }
        }
    }

    /**
     * this function will add user's card
     * @param  card
     * @return card details
     */
    public static function addCard($token, $user, $cus = 0) {
//        $user = Auth::user();
        if (isset($user->id)) {
            if ($user->stripe_token != null) {
                $customer = \Stripe\Customer::retrieve($user->stripe_token);
                if (isset($customer->id)) {
                    $card = '';
                    if ($card = $customer->sources->create(array("source" => $token)))
                        return $card;
                    else
                        return "unable to add card";
                } else
                    return "customer not available";
            }else {
                if ($cus == 0) {
                    $c = StripePayment::createCustomer('', $user);
                    if ($c) {
                        $user->stripe_token = $c->id;
                        $user->save();
                        $card = StripePayment::addCard($token, $user, 1);
                        return $card;
                    }
                }
            }
        }
    }
    
    public function checkBalance($stripe_account){
        $balance = \Stripe\Balance::retrieve(
            ["stripe_account" => $stripe_account]
        );
        return $balance;
    }
    
    public function payOut($amount,$stripe_account){
        $response = \Stripe\Payout::create([
            "amount" => $amount,
            "currency" => "usd",
        ],
        [
            "stripe_account" => $stripe_account
        ]);
        return $response;
    }
    
    
    
    public function retrieveAccount($stripe_account){
        $response = \Stripe\Account::retrieve($stripe_account);
        return $response;
    }
    public function retrieveCharge($charge_id, $stripe_account){
        $charge = \Stripe\Charge::retrieve($charge_id, ["stripe_account" => $stripe_account]);
        return $charge;
    }
    
}
