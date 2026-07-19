<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\User as User;
use App\Models\Postaudition;
use App\Models\Portfolio;
use App\Models\Career;
use App\Models\Skill;
use App\Models\CompanyVerification;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use App\Models\Mail as Mail;
use URL;

use App\Models\ContactRequest;

class AdminUserController extends AdminController {


    public function list() {

        if(checkAdminUserPermission('p2','view') == false){
           return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p2','edit');
        $addPermission = checkAdminUserPermission('p2','add');

        $users = new User;
        $users = $users->where('id', '!=', 1)->where('user_type','=',0)->orderBy('id', 'DESC')->get();
        return \View::make("admin/adminusers/list",compact('users','editPermission','addPermission'));
    }

    public function add() {

        if(checkAdminUserPermission('p2','add') == false){
           return redirect("/");
        }
        
        return \View::make("admin/adminusers/add");
    }


     public function store(Request $request) {

        if(checkAdminUserPermission('p2','add') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = [
            'name'  => 'required',
            'email' => 'required|email|unique:users,email',
            'phone'     => 'required',  
            'password' => 'required|min:6|max:20',
        ];
   
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) { 

        
              return redirect("/admin/adminuser/add")->withErrors($validator);
        } else {
            $user = new User();


            $userpermission = [];
            
            $user->password = Hash::make($request['password']);

            foreach($request['permission'] as $per) {

              $obj = new \stdClass();
              $obj->module = $per['module'];
              $obj->moduleId = $per['moduleId'];
              $obj->view = isset($per['view']) ? 1 :0;
              $obj->add = isset($per['add']) ? 1 :0;
              $obj->edit = isset($per['edit']) ? 1 :0;
              $obj->delete = isset($per['delete']) ? 1 :0;
               
              array_push($userpermission,$obj);
 
            }

            $user->name = $request['name'];
            $user->company_name = $request['company_name'];
            $user->email    = $request['email']; 
            $user->phone    = $request['phone']; 
            $user->status    = ($request['status']) ? $request['status'] : 0;
            $user->user_type = 0; 
            $user->permissions = json_encode($userpermission);
        
             
            $user->save(); 

            $emailData = ['email'=> $user->email,'password' => $request['password'],'update' => 0,'link' => URL::to('/')];

            $mail = Mail::sendMail('admin/mail/admin_new_user_welcome', 'Admin New Staff Login Details', $emailData, $user);
            
        
            return redirect("admin/adminuser/list")->with('success_msg', "Record updated successfully")->send();
             
        }
        
    }


  
    
    public function edit($uid) { 

        if(checkAdminUserPermission('p2','edit') == false){
           return redirect("/");
        }
       
        $page_name  = 'form';
         
 
        $userdetail =  \DB::table("users")->where(['id'=>$uid])->first();
        if(!empty($userdetail)){
            
             
            return \View::make("admin/adminusers/edit", compact('uid','userdetail','page_name'));
        }else{
            return redirect("/admin/adminuser/list")->with('error_msg', "No User found!")->send();
        }
    }

    public function update(Request $request) {

        if(checkAdminUserPermission('p2','edit') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = [
            'name'  => 'required',
            'email'  => 'required', 
            'phone'     => 'required',  
        ];

        
         if(!empty($request['password'])) { 
           $rules = array_merge($rules,['password' => 'nullable|min:6|max:20']);
         }
   
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) { 

        
              return redirect("/admin/adminuser/edit/". $request['user_id'])->withErrors($validator);

        } else {

           

            $userpermission = [];

            foreach($request['permission'] as $per) {

              $obj = new \stdClass();
              $obj->module = $per['module'];
              $obj->moduleId = $per['moduleId'];
              $obj->view = isset($per['view']) ? 1 :0;
              $obj->add = isset($per['add']) ? 1 :0;
              $obj->edit = isset($per['edit']) ? 1 :0;
              $obj->delete = isset($per['delete']) ? 1 :0;
               
              array_push($userpermission,$obj);
 
            }
      
            

            $user = User::find($request['user_id']);

            $user->name = $request['name'];
            $user->company_name = $request['company_name'];
            $user->email    = $request['email']; 
            $user->phone    = $request['phone']; 
            $user->status    = ($request['status']) ? $request['status'] : 0;
            $user->permissions = json_encode($userpermission);

            if(!empty($request['password'])) { 

                 $user->password = Hash::make($request['password']);
            }
       
             
            $user->save(); 

            if(!empty($request['password'])) {
               
               $emailData = ['email'=> $user->email,'password' => $request['password'],'update' => 1,'link' => URL::to('/')];

               $mail = Mail::sendMail('admin/mail/admin_new_user_welcome', 'Admin Staff Login Details', $emailData, $user);

            } 

            
            
        
            return redirect("admin/adminuser/list")->with('success_msg', "Record updated successfully")->send();
             
        }
        
    }
    
   
   

}
