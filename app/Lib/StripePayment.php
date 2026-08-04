<?php
namespace App\Lib;

use App\Models\User;
//~ use App\Models\Orders;
// Was a relative path ('vendor/stripe/init.php'), which only resolves if
// PHP's working directory happens to be the project root at request time —
// it doesn't on this server for api/ routes, causing a fatal "Failed
// opening required" error. base_path() anchors this to Laravel's actual
// app root regardless of the web server's CWD.
require_once(base_path('vendor/stripe/init.php'));
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

    /* =====================================================================
     * PaymentIntent + PaymentSheet support (Flutter `flutter_stripe`).
     *
     * Added alongside the older Charge-based methods above rather than
     * replacing them — `chargeAmountFromCard`/`makePayment` back the
     * existing addstripeammount endpoint and are left untouched. Everything
     * below backs the new generic payment_transactions flow
     * (api/PaymentController.php) instead: the mobile app never sends raw
     * card data to this backend at all — it only ever sees a PaymentIntent
     * `client_secret` and confirms directly with Stripe via the native
     * PaymentSheet, which is what keeps this integration PCI-scope-light.
     * ===================================================================== */

    /**
     * Creates a Stripe PaymentIntent for the PaymentSheet to confirm.
     *
     * @param int $amountInSmallestUnit Amount in the currency's smallest
     *   unit (cents for USD) — Stripe always expects an integer here.
     * @param string $currency e.g. 'usd'
     * @param array $metadata Free-form key/value context (transaction_id,
     *   payable_type/id, user_id) — shows up in the Stripe dashboard and is
     *   also what the webhook uses to find the matching local row.
     * @param string|null $idempotencyKey Passed as the Idempotency-Key
     *   header so a retried HTTP request (flaky network, double-tap) can't
     *   create two PaymentIntents for the same attempt.
     * @param array $paymentMethodTypes Defaults to letting Stripe decide
     *   automatically (card, plus Apple Pay/Google Pay when the device and
     *   Stripe account support them) via automatic_payment_methods.
     * @return \Stripe\PaymentIntent
     */
    public function createPaymentIntent($amountInSmallestUnit, $currency, $metadata = [], $idempotencyKey = null, $paymentMethodTypes = null)
    {
        $params = [
            'amount' => (int) $amountInSmallestUnit,
            'currency' => $currency,
            'metadata' => $metadata,
        ];

        if (!empty($paymentMethodTypes)) {
            $params['payment_method_types'] = $paymentMethodTypes;
        } else {
            // Lets Stripe surface card + any enabled wallets (Apple Pay /
            // Google Pay) in the same PaymentSheet without this backend
            // needing to know which wallets are turned on in the dashboard.
            $params['automatic_payment_methods'] = ['enabled' => true];
        }

        $options = [];
        if (!empty($idempotencyKey)) {
            $options['idempotency_key'] = $idempotencyKey;
        }

        return \Stripe\PaymentIntent::create($params, $options);
    }

    /**
     * @return \Stripe\PaymentIntent
     */
    public function retrievePaymentIntent($paymentIntentId)
    {
        return \Stripe\PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Cancels a PaymentIntent that hasn't succeeded yet — used by the
     * stale-payment sweep (ExpirePendingPaymentTransactions) so an
     * abandoned checkout (app killed, network dropped mid-payment) doesn't
     * leave money "in limbo": Stripe never actually captures funds for a
     * PaymentIntent that's canceled instead of confirmed, so there is
     * nothing to refund in this path — cancelling is enough to guarantee
     * the customer was never charged.
     *
     * @return \Stripe\PaymentIntent
     */
    public function cancelPaymentIntent($paymentIntentId)
    {
        $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        return $intent->cancel();
    }

    /**
     * Verifies and decodes a Stripe webhook payload. Throws
     * \UnexpectedValueException (malformed payload) or
     * \Stripe\Error\SignatureVerification (bad/missing signature) — the
     * caller (StripeWebhookController) is expected to catch both and
     * respond with a 400 so Stripe's retry logic kicks in rather than
     * silently accepting a payload we couldn't verify.
     *
     * @return \Stripe\Event
     */
    public static function constructWebhookEvent($payload, $sigHeader, $endpointSecret)
    {
        return \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
    }
}
