<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pages;
use App\Models\Skill;
use App\Models\Career;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Portfolio;
use App\Models\Role;
use App\Models\RecentLogin;
use App\Models\CompanyVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use \Session;
use Socialite;
use App\Models\Mail as Mail;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Lib\StripePayment;
use App\Models\ContactRequest;
use DB;
use App\Models\RoleType;

require_once('vendor/stripe/init.php');

class UserController extends ApiController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $Request;
    protected $User;

    public function __construct(Request $request, User $user)
    {
        parent::__construct();
        // Store All Type Request
        $this->Request = $request;
        $this->User = $user;
    }

    function response($data)
    {
        //return response()->json($this->arrayHandleFun($data),200);
        $data = $this->arrayHandleFun($data);
        // This header pass for allow origin control.
        header("Access-Control-Allow-Origin: *"); 
        header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization, X-CSRF-Token');
        header('Access-Control-Allow-Credentials: true');   
        echo json_encode($data);
        die();
    }

    function validationHandle($validation){
        foreach ($validation->getMessages() as $field_name => $messages){
            if(!isset($firstError)){
                $firstError        =$messages[0];
                $error[$field_name]=$messages[0];
            }
        }
        return $firstError;
    }
	
      
	function arrayHandleFun($array, $isRepeat = false){
        
        foreach ($array as $key => $value) {
            if ($value === null) { 
                $array[$key] =  "";
            }else if (is_array($value)) {
                if (empty($value)){
                    //$array[$key] = "";
                }else{
                    $array[$key] = SELF::arrayHandleFun($value);
                } 
            }
        }
        if (!$isRepeat) {
            $array = SELF::arrayHandleFun($array,true);
        }
        return $array;        
    }  
	
    public function contactRequest(Request $request){
        $data = $request->all();
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'message' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {   $baseurl = url('/public/assets/img/Acthound-logo.png');
            $to = 'abstractsoftwebtesting@gmail.com';
    	    $subject = "Contact request";
            $from = $data['email']; 
              $fromName = $data['name']; 
              $mobile = $data['mobile']; 
            $message = $data['message']; 
            
            $contact = new ContactRequest;
            $contact->name = $fromName;
            $contact->email = $from;
            $contact->mobile = $mobile;
            $contact->message = $message;
            
            $contact->save();
            
            $htmlContent = '<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="MobileOptimized" content="width">
        <meta name="HandheldFriendly" content="true">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon-32x32.png">
        <title>Acthound</title>

    </head>
    <body >
        <div style=" width:700px; margin:0 auto"> 
        <div style="background:#fff; border:#888 solid 1px; border-radius:15px; margin-top:20px; padding:20px; box-shadow:0 0 8px #999">
        <div style="text-align:center;border-bottom:1px solid #ccc; padding-bottom:18px;padding-top:18px; background:#fff none repeat scroll 0 0;"> 
        <img style="width:250px;" src='.$baseurl.' /></div> 
        <div style="font-size:16px; padding-top:20px;">
        
        <br>
Hello admin,
<br>
<br>
<div>A user sent you a contact request, details are below<br>
    
    <br>
    Name : '.$fromName.'<br>
    Email Address : '.$from.'<br>
    Mobile : '.$mobile.'<br>
    Message : '.$message.'<br>
    
</div>
<br>
        

        <br>
        <p>Thank you,</p>
        <p>Acthound Support Team</p>
        </div> </div> </div>
    </body>
</html>'; 
            
             
            
            $headers = "MIME-Version: 1.0" . "\r\n"; 
                      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
                      $headers .= 'From: '.$fromName.'<'.$from.'>' . "\r\n";
                      mail($to, $subject, $htmlContent, $headers);
                      
                      $response['status'] = "true";
                $response['message'] = "Your Request has been sent successfully.";

                return response()->json($response);
            
            
        }
        
    }

    public function signupsendotp(Request $request)
{
    $data = $request->all();
    $rules = [
        'email' => 'required|email',
        'device_type' => 'required',
        'device_token' => 'required',
    ];

    $validator = Validator::make($data, $rules);
    if ($validator->fails()) {
        return response()->json([
            'status' => "false",
            'message' => $this->validationHandle($validator->messages())
        ]);
    }

    $otp = rand(1111, 9999);
    $user = User::where('email', $data['email'])->first();
    if ($user && $user->status == 2) { 
            $response['status'] = "false";
            $response['message'] = "Your account is suspended. Please contact to administrator.";
            return response()->json($response);
    }else if ($user) {
        $user->otp = $otp;
        $user->device_type = $data['device_type'];
        $user->device_token = $data['device_token'];
    } else {
        $user = new User();
        $user->email = $data['email'];
        $user->user_type = 1;
        $user->otp = $otp;
        $user->status = '0';
        $user->device_type = $data['device_type'];
        $user->device_token = $data['device_token'];
    }

    if ($user->save()) {
        // Prepare email data
        $emaildata = [
            "name" => $data['email'],
            "email" => $data['email'],
            "otp" => $otp
        ];
        $emaildata = ["name" => $data['email'], "email" => $data['email'], 'otp' => $otp];
        $mail = Mail::sendMail('admin/mail/verificaton_email_mobile', 'Verification email from Acthound', $emaildata, $user);
       

        return response()->json([
            'status' => "true",
            'message' => "OTP has been sent to the registered email address. Please verify your email.",
            'data' => $otp
        ]);
    }
    return response()->json([
        'status' => "false",
        'message' => "Something went wrong. Please try again."
    ]);
}

    public function signup(Request $request)
    {
        $data = $request->all();
        $rules = [
            'email' => 'required|email|unique:users,email',
            'user_type' => 'required',
            'password' => 'required|min:6|max:20',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $name = null;
            $uniontype = null;
            $companyname = null;
            $stage_name = null;
            if (!empty($data['name']))
            {
                $name = $data['name'];
            }
            if (!empty($data['union_type']))
            {
                $uniontype = $data['union_type'];
            }
            if (!empty($data['company_name']))
            {
                $companyname = $data['company_name'];
            }
            if (!empty($data['stage_name']))
            {
                $stage_name = $data['stage_name'];
                $res= User::where('stage_name',$stage_name)->first();
                if(count((array)$res)>0)
                {
                    $response['status'] = "false";
                    $response['message'] = "Stage Name is already exists";
                    return response()->json($response);
                    exit;
                }
            }
            $otp = rand(1111, 9999);
            $user = new User;
            $user->name = $name;
            $user->email = $data['email'];
            $user->company_name = $companyname;
            $user->user_type = $data['user_type'];
            $user->union_type = $uniontype;
            $user->stage_name = $stage_name;
            $user->password = Hash::make($data['password']);
            $user->otp = $otp;
            $user->status = '0';
            $user->device_type = $data['device_type'];
            $user->device_token = $data['device_token'];
            $user->save();

            $UserObj = User::where(["email" => $data['email']])->first();

            if ($UserObj)
            {
                $emaildata = ["name" => $UserObj->name, "email" => $data['email'], 'otp' => $UserObj['otp']];
                $mail = Mail::sendMail('admin/mail/verificaton_email_mobile', 'Verification email from Acthound', $emaildata, $UserObj);
                
                $response['status'] = "true";
                $response['message'] = (isset($UserObj->email)) ? "You have  been registered successfully, Please verify your email address." : "User has been registered successfully";
                $response['data'] = [
                    "email" => $UserObj->email,
                    "otp" => strval($otp)
                ];
                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "Something went wrong. Please Try again.";
                return response()->json($response);
            }
        }
    }
    public function signupResendotp(Request $request)
    {
        $data = $request->all();
        $rules = [
            'email' => 'required|email',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $UserObj = User::where(["email" => $data['email']])->first();

            if ($UserObj)
            {
                $emaildata = ["name" => $UserObj->name, "email" => $data['email'], 'otp' => $UserObj['otp']];
                $mail = Mail::sendMail('admin/mail/verificaton_email_mobile', 'Verification email from Acthound', $emaildata, $UserObj);

                $response['status'] = "true";
                $response['message'] = "Otp sent successfully, Please check your email.";
                $response['data'] = [
                    "email" => $UserObj->email,
                    "otp" => strval($UserObj['otp'])
                ];
                
                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "Something went wrong. Please Try again.";
                return response()->json($response);
            }
        }
    }
    
    public function verifyAccount(Request $request)
    {
        $data = $request->all();
        $rules = [
            'email' => 'required|email',
            'otp' => 'required|min:4|max:4',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $UserObj = User::where(["email" => $data['email']])->first();
            if ($UserObj)
            {
                if ($UserObj->otp == $data['otp'])
                {
                    $UserObj->status = '1';
                    $UserObj->otp = '';
                    $UserObj->save();
                    $response['status'] = "true";
                    $response['message'] = "Your account activated successfully.";
                    $response['data'] = $UserObj;
                    return response()->json($response);
                }
                else
                {
                    $response['status'] = "false";
                    $response['message'] = "otp mismatch";
                    return response()->json($response);
                }
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "user not exist.";
                return response()->json($response);
            }
        }
    }


    public function login(Request $request)
    {
        $data = $request->all();
        $rules = [
            'email' => 'required',
            'user_type' => 'required',
            'password' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $email = $data['email'];
            $password = bcrypt($data['password']);
            $user = User::where('email', $email)->orWhere('stage_name',$email)->where('user_type', $data['user_type'])
                ->first();
            if (isset($user) && !empty($user))
            {
                if ($user && Hash::check($data['password'], $user->password))
                {
                    if ($user->status == 0)
                    {    
                        $emaildata = ["name" => $user->name, "email" => $user->email, 'otp' => $user->otp];
                        $mail = Mail::sendMail('admin/mail/verificaton_email_mobile', 'Verification email from Acthound', $emaildata, $user);

                        $response['status'] = "false";
                        $response['message'] = "Your account is inactive. Please verify your account. Check your email.";
                        $response['data'] = [
                            "email" => $user->email,
                            "otp" => strval($user->otp)
                        ];
                       return response()->json($response);
                    }else if ($user->status == 2)
                    {  
                        $response['status'] = "false";
                        $response['message'] = "Your account is suspended. Please contact to administrator.";
                       return response()->json($response);
                    }
                    else
                    {
                        User::where('device_token', $data['device_token'])
                            ->where('id', '!=', $user->id)
                            ->update([
                                'device_token' => null,
                            ]);

                        $user->device_type = $data['device_type'];
                        $user->device_token = $data['device_token'];
                        $user->save();
						$recentLogin = new RecentLogin;
						$recentLogin->user_id = $user->id;
						$recentLogin->status = 1;
						$recentLogin->save();
                        $response['status'] = "true";
                        $response['message'] = "Successfully login.";
                        $response['data'] = $user;
						
                        return response()->json($response);
                    }
                }
                else
                {
                    if (!empty($user) && ($user->user_type == 0))
                    {
                        $response['status'] = "false";
                        $response['message'] = "You are not a registered user";
                        return response()->json($response);
                    }
                    else
                    {
                        $response['status'] = "false";
                        $response['message'] = "Please enter the correct password";
                        return response()->json($response);
                    }
                }
            }
            else
            {
                
                $user = User::where('email', $email)->orWhere('stage_name',$email)->first();
                
                $message = '';
                if($user['user_type']== 1){
                    $message = "Please Select the user type Auditioner.";
                }elseif($user['user_type']== 2){
                    $message = "Please Select the user type producer";
                }
                
                if(!empty($user)){
                    $response['status'] = "false";
                    $response['message'] = $message;
                    return response()->json($response);
                }else{
                    $response['status'] = "false";
                $response['message'] = "Please enter the correct detail.";
                return response()->json($response);
                }
            }
        }
    }

    public function ForgotPassword(Request $request)
    {
        $data = $request->all();
        $rules = [
            'email' => 'required|email',
            'device_type' => 'required',
            'device_token' => 'required',
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $UserObj = User::where(["email" => $data['email']])->first();
            if ($UserObj)
            {
                $otp = rand(1111, 9999);
                $UserObj->forgot_otp = $otp;
                $UserObj->save();
                $emailData = ["otp" => $otp];

                $mail = Mail::sendMail('admin/mail/forget_mobile', 'Forgot password request on Acthound', $emailData, $UserObj);
                $response['status'] = "true";
                $response['message'] = "otp send";
                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "user not found";
                return response()->json($response);
            }
        }

    }
      public function forgetResendotp(Request $request)
    {
        $data = $request->all();
        $rules = [
            'email' => 'required|email',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $UserObj = User::where(["email" => $data['email']])->first();

            if ($UserObj)
            {
                $emaildata = ["name" => $UserObj->name, "email" => $data['email'], 'otp' => $UserObj['forgot_otp']];
                $mail = Mail::sendMail('admin/mail/forget_mobile', 'Forgot password request on Acthound', $emaildata, $UserObj);
                $response['status'] = "true";
                $response['message'] = "Otp sent successfully, Please check your email.";

                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "Something went wrong. Please Try again.";
                return response()->json($response);
            }
        }
    }
    
    public function verifyforgototp(Request $request)
    {
        $data = $request->all();

        $rules = [
            'email' => 'required|email',
            'otp' => 'required|min:4|max:4',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $UserObj = User::where('forgot_otp', '=', $data['otp'])->where('email', '=', $data['email'])->first();
            if ($UserObj)
            {
                $UserObj->forgot_otp = null;
                $UserObj->save();
                $response['status'] = "true";
                $response['message'] = "Otp verify successfully";
                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "OTP is wrong";
                return response()->json($response);
            }
        }
    }
    
    public function updateForgotPassword(Request $request)
    {
        $data = $request->all();

        $rules = [
            'email' => 'required|email',
            'new_password' => 'required|min:6|max:20',
            'cnfrmpassword' => 'required|same:new_password',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $UserObj = User::where('email', '=', $data['email'])->first();
            if ($UserObj)
            {
                $UserObj->password = Hash::make($data['new_password']);
                $UserObj->save();
                $response['status'] = "true";
                $response['message'] = "Your password change successfully.";
                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "Something went wrong.";
                return response()->json($response);
            }
        }
    }
    
    
    public function updatepassword(Request $request)
    {
        $data = $request->all();

        $rules = [
            'user_id' => 'required',
            'new_password' => 'required|min:6|max:20',
            'cnfrmpassword' => 'required|same:new_password',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $UserObj = User::where('id', '=', $data['user_id'])->first();
            if ($UserObj)
            {
                $UserObj->password = Hash::make($data['new_password']);
                $UserObj->save();
                $response['status'] = "true";
                $response['message'] = "Your password change successfully.";
                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "Something went wrong.";
                return response()->json($response);
            }
        }
    }


    public function changepassword(Request $request)
    {
        $data = $request->all();

        $rules = [
            'user_id' => 'required',
            'old_password' => 'required',
            'new_password' => 'required|min:8|max:20',
            'cnfrmpassword' => 'required|same:new_password',
            'device_type' => 'required',
            'device_token' => 'required',
        ];
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {

            $UserObj = User::find($data['user_id']);
            if ($UserObj)
            {
                if (Hash::check($data['old_password'] , $UserObj->password))
                {
                    $UserObj->password = Hash::make($data['new_password']);
                    $UserObj->save();
                    
                    $response['status'] = "true";
                $response['message'] = "Your password change successfully.";
                return response()->json($response);
                }
                else
                {
                    $response['status'] = "true";
                    $response['message'] = "old password do not match.";
                    return response()->json($response);
                }
            }
            else
            {
               $response['status'] = "false";
                $response['message'] = "Something went wrong.";
                return response()->json($response);
            }
        }
    }
    public function verificationData(Request $request){
        
        $data = $request->all();
        $rules = [
            'device_type' => 'required',
            'device_token' => 'required',
            'user_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $checkverification = CompanyVerification::where([['company_id', '=', $data['user_id']]])->first();
            if(!empty($checkverification)){
                
                $response['status'] = "true";
                $response['message'] = "Your verification request data.";
                $response['data'] = $checkverification;
                return response()->json($response);
                
            }else{
                $response['status'] = "false";
                $response['message'] = "You did not any request.";
                return response()->json($response);
            }
        }
        
    }
    
    public function companyVerifications(Request $request){
        
        $data = $request->all();
        $rules = [
            'device_type' => 'required',
            'device_token' => 'required',
            'user_id' => 'required',
            'email_phone' => 'required',
            'linked_registered_business' => 'required',
            'website_first' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $checkverification = CompanyVerification::where([['company_id', '=', $data['user_id']]])->first();
            if(!empty($checkverification) && $checkverification->verification == 0){
                
                $response['status'] = "true";
                $response['message'] = "You have already applied for verification. Your status is pending";
                return response()->json($response);
                
            }elseif(!empty($checkverification) && $checkverification->verification == 1){
                $response['status'] = "true";
                $response['message'] = "You have already applied for verification. Your verification is approved.";
                return response()->json($response);
            }elseif(!empty($checkverification) && $checkverification->verification == 2){
                
                $cverification = CompanyVerification::find($checkverification->id);
                $cverification->id = $data['user_id'];
                $cverification->email_phone = $data['email_phone'];
                $cverification->linked_registered_business = $data['linked_registered_business'];
                $cverification->website_first = $data['website_first'];
                $cverification->verification = 0;
                if(!empty($data['website_second'])){
                $cverification->website_second = $data['website_second'];
                }
                if(!empty($data['website_third'])){
                $cverification->website_third = $data['website_third'];
                }
                if(!empty($data['description'])){
                    $cverification->description = $data['description'];
                }
                $cverification->save();
                $response['status'] = "true";
                $response['message'] = "You have been applied successfully for company verification.";
                return response()->json($response);
                
            }else{
            
            $companyverification = new CompanyVerification;
            $companyverification->company_id = $data['user_id'];
            $companyverification->email_phone = $data['email_phone'];
            $companyverification->linked_registered_business = $data['linked_registered_business'];
            $companyverification->website_first = $data['website_first'];
            if(!empty($data['website_second'])){
            $companyverification->website_second = $data['website_second'];
            }
            if(!empty($data['website_third'])){
            $companyverification->website_third = $data['website_third'];
            }
            if(!empty($data['description'])){
                $companyverification->description = $data['description'];
            }
            if ($companyverification->save())
            {
               
                $response['status'] = "true";
                $response['message'] = "You have been applied successfully for company verification.";
                return response()->json($response);
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "Something went wrong. Please Try again.";
                return response()->json($response);
            }
        } 
        }
        
    }

    public function profile(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }  else  {
            $userdata = '';
            $userdata = DB::table('users')
            ->join('user_verification', 'users.id', '=', 'user_verification.user_id')
            ->select('users.*', 'user_verification.verification')
            ->where('users.id', $data['user_id'])
            ->first();
            if(empty($userdata)){
                $userdata =  User::where([['id', '=', $data['user_id']]])->first();
            }
            
            $roleData = array();
			if(!empty($userdata)){
				$role = $userdata->roles;
				if($role != ''){
					$roleArr = explode(',',$role);
					if(!empty($roleArr)){
						foreach($roleArr as $rlar){
							$rle = trim($rlar);
							if(is_numeric($rle)){
								$resRole = Role::where("roles.id", $rle)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*','role_types.title as role_type')
									->first();
							} else {
								$resRole = Role::where("roles.role_title", $rle)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*','role_types.title as role_type')
									->first();
							}
							$roleData[] = $resRole;
						}
					}
				}
			}
			
            $producer_typeArr = array();
			if(!empty($userdata)){
				$prdType = $userdata->producer_type;
				if($prdType != ''){
					$prdtypArr = explode(',',$prdType);
					if(!empty($prdtypArr)){
						foreach($prdtypArr as $rlar){
							$rle = trim($rlar);
							if(is_numeric($rle)){
								$resprdTtp = Career::where("careers.id", $rle) 
									->select('careers.*')
									->first();
							}else{
								$resprdTtp = Career::where("careers.name", $rle) 
									->select('careers.*')
									->first();
							}
								
							$producer_typeArr[] = $resprdTtp;
						}
					}
				}
			}
			
			$profile_image 	= $userdata->image;
			$producer_type 	= $userdata->producer_type;
			$facebook  		= $userdata->facebook;
			$instagram 		= $userdata->instagram;
			$tiktok 		= $userdata->tiktok;
			$snapchat		= $userdata->snapchat;
			$profile_score = '0';
			
			if($userdata->user_type == '1'){
				// auditioner
				//must update: Profile photo, Select their job title,and one social media account.
				if($profile_image != ''){
					$profile_score += 34;
				}
				if($producer_type != ''){
					$profile_score += 33;
				}
				if($facebook != '' || $instagram != '' || $tiktok != '' || $snapchat != ''){
					$profile_score += 33;
				}
				
			}else{
				// producer
				// To unlock the feeds, they must update: Profile photo and select their job title
				if($profile_image != ''){
					$profile_score += 50;
				}
				if($producer_type != ''){
					$profile_score += 50;
				}
			}
			
            
            $userprdata = array(
				"id"				=>  $userdata->id,
				"name"	=>  $userdata->name,
				"stage_name"	=>  $userdata->stage_name,
				"company_name"	=>  $userdata->company_name,
				"email"	=>  $userdata->email,
				"phone"	=>  $userdata->phone,
				"website"	=>  $userdata->website,
				"image" 			=>  $userdata->image,
				"age" 			=>  $userdata->age,
				"height" 			=>  $userdata->height,
				"location" 			=>  $userdata->location,
				"gender" 			=>  $userdata->gender,
				"roles" 			=>  $userdata->roles,
				"social_type" 			=>  $userdata->social_type,
				"social_id" 			=>  $userdata->social_id,
				"user_type" 			=>  $userdata->user_type,
				"union_type" 			=>  $userdata->union_type,
				"pro_member" 			=>  $userdata->pro_member,
				"producer_type" 			=> $userdata->producer_type,
				"skills" 			=>  $userdata->skills,
				"description" 			=>  $userdata->description,
				"status" 			=>  $userdata->status,
				"notification_status" 			=>  $userdata->notification_status,
				"verification" 			=>  $userdata->verification,
				"facebook" 			=>  $userdata->facebook,
				"instagram" 			=>  $userdata->instagram,
				"tiktok" 			=>  $userdata->tiktok,
				"snapchat" 			=>  $userdata->snapchat,
				"profile_score" 			=>  $profile_score,
				"carrier" 			=>  $producer_typeArr,
				);
            
            $skill = new Skill;
            $skill = $skill->orderBy('id', 'DESC')
                ->get();
				
			$career = new Career;
            $career = $career->orderBy('id', 'DESC')
                ->get();
				
            $response['status'] = "true";
            $response['message'] = "skill List";
            $response['data'] = $skill;
            $response['career'] = $career;
            $response['userdata'] = $userprdata;
            $response['role'] = $roleData;
            return response()->json($response);
        }
    }

    public function updateProfile(Request $request){
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ];
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        } else {
            $user = User::where([['id', '=', $data['user_id']]])->first();
            if ($user) {
                $company = NULL;
                if(!empty($data['company_name'])){
                    $company = $data['company_name'];
                }
                $stagename = NULL;
                if(!empty($data['stage_name'])){
                    $stagename = $data['stage_name'];
                    $res= User::where([['id', '!=', $data['user_id']], ['stage_name', '=', $stagename]])->first();
                    if(count((array)$res)>0) {
                        $response['status'] = "false";
                        $response['message'] = "Stage Name is already exists";
                        return response()->json($response);
                    }
                }
                $age = NULL;
                if(!empty($data['age'])){
                    $age = $data['age'];
                }
                $height = NULL;
                if(!empty($data['height'])){
                    $height = $data['height'];
                }
                $gender = NULL;
                if(!empty($data['gender'])){
                    $gender = $data['gender'];
                }
                $roles = NULL;
                if(!empty($data['roles'])){
                    $roles = $data['roles'];
                }
                $uniontype = NULL;
                if(!empty($data['union_type'])){
                    $uniontype = $data['union_type'];
                }
                $skills = NULL;
                if(!empty($data['skills'])){
                    $skills = $data['skills'];
                }
                $description = NULL;
                if(!empty($data['description'])){
                    $description = $data['description'];
                }
				
				$producer_type = NULL;
                if(!empty($data['producer_type'])){
                    $producer_type = $data['producer_type'];
                }
				
				$website = NULL;
                if(!empty($data['website'])){
                    $website = $data['website'];
                } 
                
                $facebook = NULL;
                if(!empty($data['facebook'])){
                    $facebook = $data['facebook'];
                }
                
                $instagram = NULL;
                if(!empty($data['instagram'])){
                    $instagram = $data['instagram'];
                }
                
                $tiktok = NULL;
                if(!empty($data['tiktok'])){
                    $tiktok = $data['tiktok'];
                }
                
                $snapchat = NULL;
                if(!empty($data['snapchat'])){
                    $snapchat = $data['snapchat'];
                }
                
                $user = User::find($data['user_id']);
                $user->stage_name = $stagename;
                $user->name = $data['name'];
                $user->company_name = $company;
                $user->phone = $data['phone'];
                $user->age = $age;
                $user->height = $height;
                $user->gender = $gender;
                $user->website = $website;
                $user->roles = $roles;
                $user->union_type = $uniontype;
                $user->location = $data['location'];
                $user->skills = $skills;
                $user->description = $description;
                $user->producer_type = $producer_type;
                $user->facebook = $facebook;
                $user->instagram = $instagram;
                $user->tiktok = $tiktok;
                $user->snapchat = $snapchat;
                if($request->file('image')){
        		   	   $file = $request->file('image');
        		   	   $name= 'public/assets/users/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
        		   	   $file->move(public_path().'/assets/users/',  $name);
        		   	   $user->image =  $name;
				}
				
				 
                if($user->save()){
                    $response['status'] = "true";
                    $response['message'] = "Profile updated successfully.";
                    $response['data'] = $user;
                    return response()->json($response);
                }else{
                    $response['status'] = "false";
                    $response['message'] = "Something went wrong.";
                    return response()->json($response);
                }
            }
            else
            {
                $response['status'] = "false";
                $response['message'] = "User does not exits";
                return response()->json($response);
            }
        }
    }
    
    public function stageinfo(Request $request)
    {
        
        $data = $request->all();
        $rules = [
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            //$pages = new Pages;
                $howtouse = Pages::where('slug', '=', 'how-to-use')->first();
                $terms = Pages::where('slug', '=', 'terms-and-condition')->first();
                $privacy = Pages::where('slug', '=', 'privacy-policy')->first();
                
                $response['status'] = "true";
                $response['message'] = "cms data List";
                $response['data']['howtouse'] = $howtouse['content'];
                $response['data']['termscondition'] = $terms['content'];
                $response['data']['privacy'] = $privacy['content'];
                return response()->json($response);
        }
    }
    
    public function getPortfolio(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $portfolio = Portfolio::where(["user_id" => $data['user_id']])->first();
            if($portfolio){
                $response['status'] = "true";
                $response['message'] = "Portfolio Data";
                $response['data'] = $portfolio;
                return response()->json($response);
            }else{
                $response['status'] = "true";
                $response['message'] = "Portfolio Data Empty";
                return response()->json($response);
            }
        }
    }
    
    public function updatePortfolio(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
            ];
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $portfolio = Portfolio::where(["user_id" => $data['user_id']])->first();
            if($portfolio){
                $portfolio->user_id = $data['user_id'];
                if($request->file('headshot_first')){
    		   	   $file = $request->file('headshot_first');
    		   	   $name= 'public/assets/users/headshot/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/headshot',  $name);
    		   	   $portfolio->headshot_first =  $name;
    		   	}
    		   	if($request->file('headshot_second')){
    		   	   $file = $request->file('headshot_second');
    		   	   $name= 'public/assets/users/headshot/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/headshot',  $name);
    		   	   $portfolio->headshot_second =  $name;
    		   	}
    		   	if($request->file('headshot_third')){
    		   	   $file = $request->file('headshot_third');
    		   	   $name= 'public/assets/users/headshot/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/headshot',  $name);
    		   	   $portfolio->headshot_third =  $name;
    		   	}
    		   	if($request->file('headshot_fourth')){
    		   	   $file = $request->file('headshot_fourth');
    		   	   $name= 'public/assets/users/headshot/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/headshot',  $name);
    		   	   $portfolio->headshot_fourth =  $name;
    		   	}
    		   	if($request->file('document_first')){
    		   	   $file = $request->file('document_first');
    		   	   $name= 'public/assets/users/document/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/document',  $name);
    		   	   $portfolio->document_first =  $name;
    		   	}
    		   	if($request->file('document_second')){
    		   	   $file = $request->file('document_second');
    		   	   $name= 'public/assets/users/document/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/document',  $name);
    		   	   $portfolio->document_second =  $name;
    		   	}
    		   	if($request->file('document_third')){
    		   	   $file = $request->file('document_third');
    		   	   $name= 'public/assets/users/document/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/document',  $name);
    		   	   $portfolio->document_third =  $name;
    		   	}
    		   	if($request->file('document_fourth')){
    		   	   $file = $request->file('document_fourth');
    		   	   $name= 'public/assets/users/document/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/document',  $name);
    		   	   $portfolio->document_fourth =  $name;
    		   	}
    		   	if($request->file('audio_first')){
    		   	   $file = $request->file('audio_first');
    		   	   $name= 'public/assets/users/audio/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/audio',  $name);
    		   	   $portfolio->audio_first =  $name;
    		   	}
    		   	if($request->file('audio_second')){
    		   	   $file = $request->file('audio_second');
    		   	   $name= 'public/assets/users/audio/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/audio',  $name);
    		   	   $portfolio->audio_second =  $name;
    		   	}
    		   	if($request->file('audio_third')){
    		   	   $file = $request->file('audio_third');
    		   	   $name= 'public/assets/users/audio/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/audio',  $name);
    		   	   $portfolio->audio_third =  $name;
    		   	}
    		   	if($request->file('audio_fourth')){
    		   	   $file = $request->file('audio_fourth');
    		   	   $name= 'public/assets/users/audio/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/audio',  $name);
    		   	   $portfolio->audio_fourth =  $name;
    		   	}
                if(!empty($data['video_first'])){
                    $portfolio->video_first = $data['video_first'];
                }
                if(!empty($data['video_second'])){
                    $portfolio->video_second = $data['video_second'];
                }
                if(!empty($data['video_third'])){
                    $portfolio->video_third = $data['video_third'];
                }
                $portfoliores = $portfolio->save();
                if($portfoliores){
                    $response['status'] = "true";
                    $response['message'] = "Portfolio has been updated successfully";
                    return response()->json($response);
                }else{
                    $response['status'] = "false";
                    $response['message'] = "Something went wrong.";
                    return response()->json($response);
                }
                
            }else{
                $portfolio = new Portfolio;
                $portfolio->user_id = $data['user_id'];
                
                if($request->file('headshot_first')){
    		   	   $file = $request->file('headshot_first');
    		   	   $name= 'public/assets/users/headshot/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/headshot',  $name);
    		   	   $portfolio->headshot_first =  $name;
    		   	}
    		   	
    		   	if($request->file('headshot_second')){
    		   	   $file = $request->file('headshot_second');
    		   	   $name= 'public/assets/users/headshot/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/headshot',  $name);
    		   	   $portfolio->headshot_second =  $name;
    		   	}
    		   	if($request->file('document_first')){
    		   	   $file = $request->file('document_first');
    		   	   $name= 'public/assets/users/document/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/document',  $name);
    		   	   $portfolio->document_first =  $name;
    		   	}
    		   	if($request->file('document_second')){
    		   	   $file = $request->file('document_second');
    		   	   $name= 'public/assets/users/document/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/document',  $name);
    		   	   $portfolio->document_second =  $name;
    		   	}
    		   	if($request->file('audio_first')){
    		   	   $file = $request->file('audio_first');
    		   	   $name= 'public/assets/users/audio/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/audio',  $name);
    		   	   $portfolio->audio_first =  $name;
    		   	}
    		   	if($request->file('audio_second')){
    		   	   $file = $request->file('audio_second');
    		   	   $name= 'public/assets/users/audio/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
    		   	   $file->move(public_path().'/assets/users/audio',  $name);
    		   	   $portfolio->audio_second =  $name;
    		   	}
                
                if(!empty($data['video_first'])){
                    $portfolio->video_first = $data['video_first'];
                }
                if(!empty($data['video_second'])){
                    $portfolio->video_first = $data['video_second'];
                }
                if($portfolio->save()){
                    $response['status'] = "true";
                    $response['message'] = "Portfolio has been updated successfully";
                    return response()->json($response);
                }else{
                    $response['status'] = "false";
                    $response['message'] = "Something went wrong.";
                    return response()->json($response);
                }
                
            }
                 
        }
    }
    
    public function deletePortfolio(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'field_name' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $portfolio = Portfolio::where(["user_id" => $data['user_id']])->first();
            
            $deletefield = $data['field_name'];
            
            $portfolio->$deletefield = NULL;
            
            if($portfolio->save()){
                $response['status'] = "true";
                $response['message'] = "Portfolio Data deleted successfully";
                return response()->json($response);
            }else{
                $response['status'] = "false";
                $response['message'] = "Something went wrong";
                return response()->json($response);
            }
        }
    }

    public function deleteAccount(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
          DB::table('users')->where('id',$request->user_id)->delete(); 
        $response['status'] = "true";
        $response['message'] = "Accountg has been deleted successfully";
        return response()->json($response);
           
        }
    }


    public function getFCMToken(Request $request){
        $rules = [
            'user_id' => 'required'
        ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails()){
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        } else {
            $user = DB::table('users')->where('id',$request->user_id)->first();
            return response()->json([
                'fcm_token' => $user->device_token ?? null
            ]);
        }
    }
    
    

    public function logout(Request $request)
    {
        \Session::flush();

        \Cookie::queue('autologin', null, 5);
		
		if(isset($request['user_id'])){
			$recentLogin = new RecentLogin;
			$recentLogin->user_id = $request['user_id'];
			$recentLogin->status = 0;
			$recentLogin->save();
		}

        return redirect('owner')
            ->with('success_msg', trans('front_message.successfully_logout'))
            ->send();
    }
    
    
    
	public function SocialMediaLogin(Request $request)
	{
	   
		$data = $request->all();
		
		$rules = [
            'social_type' =>  'required',
            'name'        =>  'required',
            'device_type' =>  'required',
            'device_token'=>  'required',
            'user_type'	  =>  'required',
            'social_id'	  =>  'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            $response['status'] = "false"; 
            $response['message'] = $this->validationHandle($validator->messages()); 
            $response['data'] = []; 
             return response()->json($response);
        }else {
			
				//$UserObj = User::where('social_id', '=', $data['social_id'])->where('user_type', '=', $data['user_type'])->first();
				$UserObj = User::where('social_id', '=', $data['social_id'])->first();
				if ($UserObj) {
				    
				    if($UserObj->user_type == $data['user_type']){
				    
    					$UserObj->device_type = $data['device_type'];
    					$UserObj->device_token = $data['device_token'];
    					$UserObj->social_id = $data['social_id']; 
    					$UserObj->social_type = $data['social_type']; 
    					if(!empty($data['email'])){
    						$UserObj->email = $data['email'];
    					}
    					if(!empty($data['phone'])){
    						$UserObj->phone = $data['phone'];
    					}
    					
    					if(!empty($data['user_type'])){
    						$UserObj->user_type = $data['user_type'];
    					}
    					
    					$UserObj->save();
    					$response['status'] = "true"; 
    					$response['message'] = "Successfully login.";
    					
    					if($UserObj->password == ''){ 
    				    	$response['is_new_user'] = "true"; 
    					}else{
    					    $response['is_new_user'] = "false"; 
    					}
    					$response['data'] =   $UserObj; 
					 
				    }else{
				        $response['is_new_user'] = "false"; 
				        $response['status'] = "false"; 
    					$response['message'] = "You have already registed with other user type"; 
				    }
				    
				    return response()->json($response);
					 
				} else {
					$user = new User;
					if(!empty($data['name'])){
					$user->name = $data['name'];
					}
					if(!empty($data['company_name'])){
					$user->company_name = $data['company_name'];
					}
					
					if(!empty($data['email'])){
						$user->email = $data['email'];
					}
					if(!empty($data['phone'])){
						$user->phone = $data['phone'];
					}
				 
					$user->device_type = $data['device_type'];
					$user->device_token = $data['device_token'];
					$user->status = 1;
					$user->social_id = $data['social_id']; 
					$user->social_type = $data['social_type']; 
					$user->user_type = $data['user_type']; 
					$user->save(); 
					
					$response['status'] = "true";
					$response['is_new_user'] = "true"; 
					$response['message'] = "Successfully login."; 
					$response['data'] =   $user; 
					 return response()->json($response);
				}
			 			
		} 
	}
	public function notification(Request $request)
	{
	   
		$data = $request->all();
		
		$rules = [
            'user_id' => 'required',
            'notification_status' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            $response['status'] = "false"; 
            $response['message'] = $this->validationHandle($validator->messages()); 
            $response['data'] = []; 
             return response()->json($response);
        }else {
            $user = User::where([['id', '=', $data['user_id']]])->first();
            $user->notification_status = $data['notification_status']; 
			$user->save();
		    
		    $response['status'] = "true"; 
			$response['message'] = "Notification status updated."; 
			return response()->json($response);
			 			
		} 
	}
	
	public function subscription(Request $request){
	    $data = $request->all();
        $rules = [
            'device_type' => 'required',
            'device_token' => 'required',
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $subscription = Subscription::orderBy('id', 'DESC')->get();
            $response['status']   = "true"; 
            $response['message']  = "Search Data";
            $response['data'] = $subscription;
            return response()->json($response);
        }
	}
	
	
	public function addstripeammount(Request $request) {
        $data = $request->all();

        $rules = [
            'user_id'               =>  'required',
            'email_id'              =>  'required', 
            'amount'                =>  'required',
            'token'                 =>  'required',
            'device_type'           =>  'required',
            'device_token'          =>  'required',
        ];

        $validator = Validator::make(Input::all(), $rules);
       
        if($validator->fails()){
            $response['status'] = "false"; 
            $response['message'] = $this->validationHandle($validator->messages()); 
            $response['data'] = []; 
            $this->response($response);
        }else{
            $time = time();
            $id = $data['user_id'];
            $user = User::find($id);
            
            if($user){ 
                $paymentdata['stripe_token']    = $data['token'];
                $paymentdata['price']           = $data['amount'];
                $paymentdata['email']           = $data['email_id']; 
                $paymentdata['order_id']        = 0;
                $stripePayment = new StripePayment();
				try{
					$stripeResponse = $stripePayment->chargeAmountFromCard($paymentdata); 
					 
					if ($stripeResponse['amount_refunded'] == 0 && empty($stripeResponse['failure_code']) && $stripeResponse['paid'] == true && $stripeResponse['captured'] == true && $stripeResponse['status'] == 'succeeded') {
						
						$amount = $stripeResponse["amount"] /100;
						$trans_history = new Transaction;
						$trans_history->user_id      	 = $data['user_id'];
						$trans_history->amount      	 = $amount;
						$trans_history->txn_id           = $stripeResponse["balance_transaction"];
						$trans_history->payment_response = json_encode($stripeResponse);
						$trans_history->payment_status   = $stripeResponse["status"];
						$trans_history->txn_start_date   = date('Y-m-d H:i:s');
						$trans_history->txn_end_date     = date('Y-m-d H:i:s');
						$trans_history->created_at       = date('Y-m-d H:i:s');
						$trans_history->updated_at 		 = date('Y-m-d H:i:s');
						$trans_history->save();
						
						$tot_amt = $amount;

						/* $user_wallet_update = DB::table('users')->where([
																		['id', '=', $data['user_id']],
																	])->update(['wallet_amount' => $tot_amt]);
																	
						$userdataval = User::where('id', '=', $data['user_id'])->first();
						$user_wallet_amt = $userdataval->wallet_amount; */

						if($trans_history){
							$response['status'] = "true";
							$response['message'] = "The payment TXN ID is ". $stripeResponse["balance_transaction"];
							$response['data'] = $tot_amt;
							$this->response($response);
						}else{
							$response['status'] = "false";
							$response['message'] = "Something went wrong. Please Try again.";
							$response['data'] = [];
							$this->response($response);
						}

					}else{
						$amount = $stripeResponse["amount"] /100;
						$trans_history = new Transaction;
						$trans_history->user_id      	 = $data['user_id'];
						$trans_history->amount      	 = $amount;
						$trans_history->txn_id           = $stripeResponse["balance_transaction"];
						$trans_history->payment_response = json_encode($stripeResponse);
						$trans_history->payment_status   = $stripeResponse["status"];
						$trans_history->txn_start_date   = date('Y-m-d H:i:s');
						$trans_history->txn_end_date     = date('Y-m-d H:i:s');
						$trans_history->created_at       = date('Y-m-d H:i:s');
						$trans_history->updated_at 		 = date('Y-m-d H:i:s');
						$trans_history->save();
						
						 

						if($trans_history){
							$response['status'] = "true";
							$response['message'] = "The payment TXN ID is ". $stripeResponse["balance_transaction"];
							$response['data'] = $amount;
							$this->response($response);
						}else{
							$response['status'] = "false";
							$response['message'] = "Something went wrong. Please Try again.";
							$response['data'] = [];
							$this->response($response);
						}
					}
				}
				catch(\Exception $e)
				{
					$response['status']     = "false"; 
					$response['message']    = $e->getMessage();//"Success"; 
					$response['data']       = []; 
					$this->response($response);
				} 

            }else{
                $response['status'] = "false";
                $response['message'] = "User does not exits";
                $response['data'] = [];
                $this->response($response);
            }
        }
    }
	
	
	public function addRole(Request $request)
    {
        $data = $request->all();
        $rules = [
            'role_title' => 'required', 
			'device_type' => 'required',
			'device_token' => 'required',
		];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
             try{
				 
				$role = new Role;
				 
				$role->role_title = $data['role_title'];
				
				if (!empty($data['role_type']))
				{
					$role->role_type = $data['role_type'];
				}
				if (!empty($data['gender']))
				{
					$role->gender = $data['gender'];
				}
				if (!empty($data['age_min']))
				{
					$role->age_min = $data['age_min'];
				}
				if (!empty($data['age_max']))
				{
					$role->age_max = $data['age_max'];
				}
				if (!empty($data['skills']))
				{
					$role->skills = $data['skills'];
				}
				if (!empty($data['character_description']))
				{
					$role->character_description = $data['character_description'];
				}
				if (!empty($data['nudity_or_bareness']))
				{
					$role->nudity_or_bareness = $data['nudity_or_bareness'];
				}
				 
				$role->save();
				
				$response['status'] = "true";
				$response['message'] = "Role added successfully";
				$response['data'] = $role; 
			   
				$this->response($response);
				
			} catch (\Exception $ex) {
				$response['status'] = "false";
				$response['message'] = "Role not added.";
				$response['data'] = []; 
			   
				$this->response($response);
			}  
        }
    }

    public function roletype(){
		
		try{
			
			$roletype = RoleType::where(['status'=>1])->orderBy('id', 'DESC')->get();
			
			if(!empty($roletype)){
				$response['status']   = "true"; 
				$response['message']  = "Role Type Data";
				$response['data'] = $roletype;
				return response()->json($response);
				
			}else{
				$response['status'] = "false";
				$response['message'] = "RoleType not found.";
				$response['data'] = []; 
			   
				$this->response($response);
			}
            
			
		} catch (\Exception $ex) {
			$response['status'] = "false";
			$response['message'] = "RoleType not found.";
			$response['data'] = []; 
		   
			$this->response($response);
		}
	}

    public function audiroletype(){
		
		try{
			
			$roletype = RoleType::where(['status'=>1])->where(['type'=>0])->orderBy('id', 'DESC')->get();
			
			if(!empty($roletype)){
				$response['status']   = "true"; 
				$response['message']  = "Role Type Data";
				$response['data'] = $roletype;
				return response()->json($response);
				
			}else{
				$response['status'] = "false";
				$response['message'] = "RoleType not found.";
				$response['data'] = []; 
			   
				$this->response($response);
			}
            
			
		} catch (\Exception $ex) {
			$response['status'] = "false";
			$response['message'] = "RoleType not found.";
			$response['data'] = []; 
		   
			$this->response($response);
		}
	}


}

