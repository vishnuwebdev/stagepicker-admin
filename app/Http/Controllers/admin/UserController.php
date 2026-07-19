<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\User as User;
use App\Models\Postaudition;
use App\Models\Portfolio;
use App\Models\Career;
use App\Models\Skill;
use App\Models\CompanyVerification;
use App\Models\UserVerification;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use App\Models\Mail as Mail;

use App\Models\ContactRequest;

class UserController extends AdminController {

    public function list() {
        $users = new User;
        $users = $users->where('user_type', '!=', 0)->orderBy('id', 'DESC')->get();
        return \View::make("admin/users/list",compact('users'));
    }
    public function userDetail(Request $request,$id=null) {
        $userDetail = "";    
            $id =  base64_decode($id);
            if(!empty($id)){
                $userDetail = User::where('id',$id)->first();
                $portfolio  = Portfolio::where('user_id', $userDetail['id'])->first();
                
            }
         return \View::make('admin/users/userdetail',compact('userDetail', 'portfolio'));
    }

    public function download(){
        $fileName = 'users.csv';
        $users =  User::select('name','stage_name','company_name','email','phone','age','gender','producer_type','union_type','pro_member','skills','roles','status')->where('user_type','!=',0)->orderBy('id', 'DESC')->get();
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('User Name', 'Stage Name', 'Company Name', 'Email', 'Phone Number','Age','Gender','Producer Type','Union Type','Skills','Roles','Status');

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $row['User Name']  = $user->name;
                $row['Stage Name']  = $user->stage_name;
                $row['Company Name']  = $user->company_name;
                $row['Email']  = $user->email;
                $row['Phone Number']  = $user->phone;
                $row['Age']  = $user->age;
                $row['Gender']  = $user->gender;
                $row['Producer Type']  = $user->producer_type;
                $row['Union Type']  = ($user->union_type == 1) ? 'Union' : 'Non-Union';
                $row['Skills']  = $user->skills;
                $row['Roles']  = $user->roles;
                $row['Status']  = ($user->status == 1) ? 'Active' : 'Inactive';
              

                fputcsv($file, array($row['User Name'],$row['Stage Name'],$row['Company Name'],$row['Email'],$row['Phone Number'],$row['Age'],$row['Gender'],$row['Producer Type'],$row['Union Type'],$row['Skills'],$row['Roles'],$row['Status']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    public function edit($uid) {
        $category_name  = 'userlists';
        $page_name  = 'form';
         
 
        $userdetail =  \DB::table("users")->where(['id'=>$uid])->first();
        if(!empty($userdetail)){
            $role_types = new Career;
            $role_types = $role_types->orderBy('name', 'ASC')
                ->get();

            $skills = new Skill;
            $skills = $skills->orderBy('skill_name', 'ASC')
                ->get();    
             
            return \View::make("admin/users/edit", compact('uid','userdetail','category_name','page_name','role_types','skills'));
        }else{
            return redirect("/admin/users")->with('error_msg', "No User found!")->send();
        }
    }

    public function editUser(Request $request) {

        $data = $request->all();
        $rules = [
            'name'  => 'required',
            'email'  => 'required', 
            'status'     => 'required',
            // 'phone'     => 'required',  
        ];
        if($request['password']) {
          $rules['password'] = 'required|min:6';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) { 

            //$errmess = implode(', ',$validator->messages()->all());
           // return redirect("/admin/useredit/". $request['user_id'])->with('error_msg', $errmess)->send();
              return redirect("/admin/useredit/". $request['user_id'])->withErrors($validator);
        } else {
            $user = User::find($request['user_id']);

            $user->name = $request['name'];
            $user->company_name = $request['company_name'];
            $user->email    = $request['email']; 
            $user->phone    = $request['phone']; 
            $user->skills    = ($request['skills']) ? implode(',',$request['skills']) : NULL;
            $user->status    = $request['status']; 
            $user->producer_type    = ($request['producer_type']) ? implode(',',$request['producer_type']) : NULL; 
            $user->gender    = $request['gender']; 
            $user->age    = $request['age']; 
            $user->height    = $request['height']; 
            $user->facebook    = $request['facebook']; 
            $user->instagram    = $request['instagram']; 
            $user->tiktok    = $request['tiktok']; 
            $user->snapchat    = $request['snapchat']; 
            $user->website    = $request['website']; 
            
            if($request['password']) {
                
                $user->password = Hash::make($request['password']);
            }

            

            $user->union_type    = ($request['union_type']) ? $request['union_type'] : 0; 
            
            

            if ($request->file('image')) {
                $path = public_path("uploads/user");
                @\File::makeDirectory($path, 0775, true);

                $old_avatar = $user->image;
                $old_avatar_path = $path . '/' . $user->image;
                @unlink($old_avatar_path);

                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $fileNameExt = time() . "-" . rand(1000, 9999);
                $fileName = $fileNameExt . '.' . $extension;
                 
                $image->move($path, $fileName);
                $user->image = $fileName;
            }
             
            $user->save(); 
            
             \Session::put('admindata', $user);
            return redirect("admin/users")->with('success_msg', "Record updated successfully")->send();
             
        }
        
    }
    
    public function companyVerifications(Request $request){
        
        if(checkAdminUserPermission('p5','view') == false){
           return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p5','edit');

        $verifications = DB::table('user_verification')
            ->join('users', 'user_verification.user_id', '=', 'users.id')
            ->select('user_verification.*', 'users.name', 'users.company_name')
            ->groupBy('user_verification.user_id')
            ->orderBy('user_verification.id', 'DESC')
            ->get();
        
        return \View::make("admin/users/verificationlist",compact('verifications','editPermission'));
        
    }

    public function editVerification(Request $request, $id = null) {
        
        $companyverification = UserVerification::where('user_id', $id)->where('type','company')->first();
        $standardVerification = UserVerification::where('user_id', $id)->where('type','standard')->first();
        $userDetail = User::where('id',$id)->first();
        
        return \View::make('admin/users/verificationedit', compact('id', 'companyverification', 'standardVerification', 'userDetail'));
        
    }

    public function viewVerification(Request $request, $id = null) {
        
        $companyverification = UserVerification::where('user_id', $id)->where('type','company')->first();
        $standardVerification = UserVerification::where('user_id', $id)->where('type','standard')->first();
        $userDetail = User::where('id',$id)->first();
        
        return \View::make('admin/users/verificationview', compact('id', 'companyverification', 'standardVerification', 'userDetail'));
        
    }
    
    public function updateVerification(Request $request) {
        $data = $request->all();
        $rules = ['verification_id' => 'required', ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        } else {
            if($data['verification_type'] == 'c'){
                $verification = UserVerification::where('type', 'company')
                ->where('id', $data['verification_id'])
                ->first();
                $verification->status = $data['verification_status'];
                $verification->save();
            } elseif($data['verification_type'] == 's'){
                $verification = UserVerification::where('type', 'standard')
                    ->where('id', $data['verification_id'])
                    ->first();
                    $verification->status = $data['verification_status'];
                    $verification->save();
            }
            $userInfo = User::where('id',$verification->user_id)->first();
            $notification_array = '';
            
            if($verification->status == 1){
                $message = "Your {$verification->type} Verification has been approved.";
                $notification_array = array(
                    'title' => "Your {$verification->type} Verification has been approved",
                    'body' => $message
                );
            }elseif($verification->status == 2){
                $message = "Your {$verification->type} Verification has been rejected";
                $notification_array = array(
                    'title' => "Your {$verification->type} Verification has been rejected",
                    'body' => $message
                );
            }
            //FCM api URL
            $url = 'https://fcm.googleapis.com/fcm/send';

            //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
            $server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
            $fields = array();
            $fields['notification'] = $notification_array;
            
            $fields['to'] = $userInfo->device_token;
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $server_key
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_exec($ch);
            curl_close($ch);
            return redirect("admin/company-verification")
                ->with('success', "Data has been updated successfully.")
                ->send();
        }

    }

    public function updateVerificationData(Request $request)
    {
        $data = $request->all();
        $rules = ['verification_id' => 'required', ];
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        } else {
            $isStatusChanged = false;
            if($data['verification_type'] == 'c'){
                $verification = UserVerification::where('type', 'company')
                ->where('id', $data['verification_id'])
                ->first();
                $verification->email = $data['email'];
                $verification->phone_no = $data['phone_no'];
                $verification->company_website = $data['company_website'];
                $verification->business_registration = $data['business_registration'];
                $verification->industry = $data['industry'];
                $verification->statement = $data['statement'];
                if($request->file('document')){
                    $file = $request->file('document');
                    $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName());
                    $file->move(public_path().'/assets/verification/',  $name);
                    $verification->document =  $name;
                }
                $isStatusChanged = $verification->status != $data['verification_status'];
                $verification->status = $data['verification_status'];
                $verification->save();
                $webMessage = "Company Verification Information is updated.";
            } elseif($data['verification_type'] == 's'){
                $verification = UserVerification::where('type', 'standard')
                    ->where('id', $data['verification_id'])
                    ->first();
                $verification->legal_name = $data['legal_name'];
                // $verification->email = $data['email'];
                $verification->phone_no = $data['phone_no'];
                $verification->statement = $data['statement'];
                $verification->agency = $data['agency'];
                $verification->portfolio = $data['portfolio'];
                $isStatusChanged = $verification->status != $data['verification_status'];
                $verification->status = $data['verification_status'];
                $verification->verification = $data['verification_status'];


                if($request->file('document')){
                    $file = $request->file('document');
                    $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
                    $file->move(public_path().'/assets/verification/',  $name);
                     $verification->document =  $name;
                }
                if($request->file('front_photo')){
                    $file = $request->file('front_photo');
                    $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
                    $file->move(public_path().'/assets/verification/',  $name);
                     $verification->front_photo =  $name;
                }
                if($request->file('back_photo')){
                    $file = $request->file('back_photo');
                    $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
                    $file->move(public_path().'/assets/verification/',  $name);
                     $verification->back_photo =  $name;
                }
                if($request->file('real_time_photo')){
                    $file = $request->file('real_time_photo');
                    $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
                    $file->move(public_path().'/assets/verification/',  $name);
                     $verification->real_time_photo =  $name;
                }
                if($request->file('resume')){
                    $file = $request->file('resume');
                    $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
                    $file->move(public_path().'/assets/verification/',  $name);
                    $verification->resume =  $name;
                }
                $webMessage = "Standard Verification Information is updated.";
                $verification->save();
            }
            if($isStatusChanged){
                $userInfo = User::where('id',$verification->user_id)->first();
                $notification_array = '';
                if ($verification->status == 1){
                    $message = "Your {$verification->type} Verification has been approved.";
                    $notification_array = array(
                        'title' => "Your {$verification->type} Verification has been approved",
                        'body' => $message
                    );
                } elseif($verification->status == 2){
                    $message = "Your {$verification->type} Verification has been rejected";
                    $notification_array = array(
                        'title' => "Your {$verification->type} Verification has been rejected",
                        'body' => $message
                    );
                }
                
                //FCM api URL
                $url = 'https://fcm.googleapis.com/fcm/send';

                //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
                $server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
                $fields = array();
                $fields['notification'] = $notification_array;
                $fields['to'] = $userInfo->device_token;
                $headers = array(
                    'Content-Type:application/json',
                    'Authorization:key=' . $server_key
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                curl_exec($ch);
                curl_close($ch);
            }
            return redirect("admin/company-verification")
                ->with('success', $webMessage ?? '')
                ->send();
        }

    }

    public function statusupdate($userid, $status) {
        try {

            $user = User::find($userid);
            $user->status = $status == 0 ? 1 : 0;
            $user->save();
            return cRedirect("users")->with('success_msg', "Status updated successfully")->send();
        } catch (\Exception $ex) {
            return cRedirect("users")->with('error_msg', "Please try again")->send();
        }
    }

    
    public function delete($userid) {

        try {
            $userid =  base64_decode($userid);
            User::where('id', '=', $userid)->delete();
            Postaudition::where('user_id', '=', $userid)->delete();
            
            return redirect("admin/users")->with('success', "Data deleted successfully")->send();
          } catch (\Exception $e) {
            return redirect("admin/users")->with('error', "Please try again")->send();
        }

    }
    
    public function suspend(Request $request) {

        if($request->ajax()){
            $data = $request->all();
            $rules = [
                'user_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                    $response['status'] = "500";
                    $response['message'] = $validator->errors();
                   return response()->json($response);
            } else {
                $user = User::find($data['user_id']);
                  
                if (!empty($user))
                {
                    $user->status = 2;
                
                    $user->save();
                
                    $emailData = ["name" =>$user->name,"reason" => $data['reason']];
    
                    $mail = Mail::sendMail('admin/mail/suspend_email', 'Your account has been suspended by administrator on Stagepicker', $emailData, $user);
                    
                   // return redirect("admin/users")->with('success_msg', "Account suspended successfully")->send();
                    
                    $response['status'] = "200";
                    $response['message'] = "suspended successfully";
                    
                }else{
                    $response['status'] = "400";
                    $response['message'] = "Not suspended";
                }
                 
                return response()->json($response);
            }
        }

    }
    
    public function profile(Request $request) {
        
          if($request->ajax()){
            print_r($request);
            exit;
           
            $data = $request->all();
            $rules = [
                'user_name' => 'required',
            ];

            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()]);
            } else {
                $user = Admin::find($request['user_id']);
                $user->username = $request['username'];
                $user->phone = $request['phone'];
                $user->about = $request['about'];
                $user->contacts = $request['contacts'];
                //$user->privacy = $request['privacy'];
                
                $user->save();
                
                return cRedirect("profile")->with('success_msg', "Profile updated successfully")->send();
                // Session::flash('success_msg', __('Profile updated successfully.'));
                 return response()->json(['success' => true], 200);
            }
        }else{
        
            $editProfile = Admin::where('id',1)->first();
        
            return \View::make("Admin/Users/profile", compact('editProfile'));
        }
      }

    public function updateprofile(Request $request) {
        $data = $request->all();
        $rules = [
            'username'  => 'required',
            'phone'     => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
                return response()->json(['errors'=>$validator->errors()]);
        } else {
            $user = Admin::find($request['user_id']);
            $userid         = $request['user_id'];
            $user->username = $request['username'];
            $user->phone    = $request['phone'];
            $user->contacts = $request['contacts'];

            if (Input::file('profile_image')) {
                $path = base_path("uploads/admin/$userid/profile");
                @\File::makeDirectory($path, 0775, true);

                $image = Input::file('profile_image');
                $extension = $image->getClientOriginalExtension();
                $fileNameExt = time() . "-" . rand(1000, 9999);
                $fileName = $fileNameExt . '.' . $extension;
                $filePath = "uploads/admin/$userid/profile/" . $fileName;
                $image->move($path, $fileName);
                $user->image = $filePath;
            }
            
            $user->save();
            
            return cRedirect("profile")->with('success_msg', "Profile updated successfully")->send();
            // Session::flash('success_msg', __('Profile updated successfully.'));
            return response()->json(['success' => true], 200);
        }
        
    }
    

    public function logout() {
        \Session::flush();
        return cRedirect("")->send();
    }
    
    public function contactlist() {
        $contacts = DB::table('contact_requests') 
            ->select('contact_requests.*')
            ->orderBy('id', 'DESC')
            ->get();
        
        return \View::make("admin/users/contactlist",compact('contacts'));
    }
    
    
    public function deletecontact($id) {

        try {
            $id =  base64_decode($id);
            ContactRequest::where('id', '=', $id)->delete();
         
            return redirect("admin/contactlist")->with('success', "Data deleted successfully")->send();
        } catch (\Exception $e) {
            return redirect("admin/contactlist")->with('error', "Please try again")->send();
        }

    }
    
    
    public function updateUserStatus(Request $request)
    {
        $data = $request->all();
        $rules = [
            'uId'  => 'required', 
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);
        } else {
            $user = User::find($data['uId']);
             
            $user->status = $data['status']; 
 
            $user->save();
            
            if($user->notification_status == 1 && $data['status'] == '1'){
                $message = "Administrator approved your account.";
                $notification_array = array(
                    'title' => 'Account approved',
                    'body' => $message
                );
                //FCM api URL
                $url = 'https://fcm.googleapis.com/fcm/send';

                //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
                $server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
                
                
                $fields = array();
                $fields['notification'] = $notification_array;
                $fields['to'] = $user->device_token;
                $headers = array(
                    'Content-Type:application/json',
                    'Authorization:key=' . $server_key
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $responce = curl_exec($ch);
                curl_close($ch);
            }
            
            
            return response()->json(['success' => true], 200);
        }

    }
    
    
    public function pushNotification(Request $request) {
        $users = User::where('user_type', '!=', 0)->orderBy('id', 'DESC')->get();
         
        return  \View::make('admin/users/push_notification',compact('users'));
    }
    
    
    
    public function sendpushnotification(Request $request) {
        $data = $request->all();
         
        $rules = [
            'send_to'  => 'required',
            'title'     => 'required', 
            'message'     => 'required', 
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) { 
                return redirect("admin/push-notification")->with('error', $validator->errors())->send();                
        } else {
            //NOTIFICATION to owner FOR ORDER
            
            //dd($data);
            $getdevicetokenArr = array();
            if($data['send_to'] == 1){ 
                
                $getdevicetokenArr = User::where('user_type', '!=', '0')
                    ->where('status', '=', '1')
                    ->whereNotNull('device_token')
                    ->select('device_token')
                    ->get();
                    
                $getdevicetokenArr = $getdevicetokenArr->toArray();
                    
            }else{
                
                $getdevicetokenArr = User::where('id', $data['send_to'])
                    ->where('status', '=', '1')
                    ->whereNotNull('device_token')
                    ->select('device_token')
                    ->get();
                
                $getdevicetokenArr = $getdevicetokenArr->toArray();
                    
            } 
             
            $notification_array = array(
                'title' => $data['title'],
                'body' => $data['message']
            );
            
            if(!empty($getdevicetokenArr)){
                foreach($getdevicetokenArr as $getdevicetoken){
                    $gcmtoken = $getdevicetoken['device_token'];
  
                    $sent_data = date('d-m-Y H:i:s');
              
                    $notification_array = array(
                        'title' => $data['title'],
                        'body' => $data['message']
                    );
                     
                    //FCM api URL
                    $url = 'https://fcm.googleapis.com/fcm/send';

                    //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
                    $server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
                    
                    
                    $fields = array();
                    $fields['notification'] = $notification_array;
                    $fields['to'] = $gcmtoken;
                    $headers = array(
                        'Content-Type:application/json',
                        'Authorization:key=' . $server_key
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $responce = curl_exec($ch);
                    curl_close($ch);
                     
                }
            }
               
            return redirect("admin/push-notification")->with('success', "Notification send successfully")->send();
        }
        
    }
    

}
