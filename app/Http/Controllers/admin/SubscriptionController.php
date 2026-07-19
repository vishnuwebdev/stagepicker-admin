<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Role;
use App\Models\Subscription;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
//use Illuminate\Mail\Mailer;
class SubscriptionController extends AdminController
{

    public function subscriptionlist(Request $request)
    {

        if(checkAdminUserPermission('p9','view') == false){
           return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p9','edit');
        $addPermission = checkAdminUserPermission('p9','add');
        $deletePermission = checkAdminUserPermission('p9','delete');

        $subscription = new Subscription;
        $subscription = $subscription->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/subscription/list", compact('subscription','editPermission','deletePermission','addPermission'));
    }

    public function add(Request $request)
    {
        if(checkAdminUserPermission('p9','add') == false){
           return redirect("/");
        }

        return \View::make('admin/role/add');
    }

    public function edit(Request $request, $id = null)
    {

        if(checkAdminUserPermission('p9','edit') == false){
           return redirect("/");
        }
        
        $subscription = Subscription::where('id', $id)->first();
        return \View::make('admin/subscription/edit', compact('id', 'subscription'));
    }

    public function store(Request $request)
    {
         if(checkAdminUserPermission('p9','add') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['role_title' => 'required', ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        }
        else
        {
            $role = new Role;
            $role->role_title = $data['role_title'];
            $role->save();
            return redirect("admin/role")
                ->with('success', "Data has been created successfully.")
                ->send();
        }
    }

    public function update(Request $request)
    {

         if(checkAdminUserPermission('p9','edit') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = [
            'title' => 'required',
            'amount' => 'required',
            'description' => 'required',
        ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        }
        else
        {
            $role = Subscription::find($data['subscription_id']);
            $role->title = $data['title'];
            $role->amount = $data['amount'];
            $role->duration = $data['duration'];
            $role->description = $data['description'];
            
            $image = $request->file('image');
            
            
				 
            if ($image) {
				 if($image!==null){
					// This code use for profile picture upload
					$destinationPath = '/slider/';
					$mime = $image->getMimeType();
					$responseData = Uploader::doUpload($image,$destinationPath,true,'',$mime);    
					if($responseData['status']=="true"){
						 $role->image = $responseData['file'];
					}                             
				}   
			}
			 
            $role->save();
            return redirect("admin/subscription")
                ->with('success', "Data has been updated successfully.")
                ->send();
        }

    }

    public function delete($id)
    {

         if(checkAdminUserPermission('p9','delete') == false){
           return redirect("/");
        }

        try
        {
            Role::where('id', '=', $id)->delete();
            return redirect("admin/role")
                ->with('success', "Data has been deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {

            return redirect("admin/role")->with('error', "Please try again")
                ->send();

        }

    }

}

