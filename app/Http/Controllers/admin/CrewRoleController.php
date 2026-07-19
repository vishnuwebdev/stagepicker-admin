<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\CrewRole;
use App\Models\Role;
use App\Models\RoleType;
use App\Models\Category;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
//use Illuminate\Mail\Mailer;
class CrewRoleController extends AdminController
{

    public function list(Request $request)
    {
        if(checkAdminUserPermission('p4','view') == false){
           return redirect("/");
        }
        
        $editPermission = checkAdminUserPermission('p4','edit');
        $addPermission = checkAdminUserPermission('p4','add');
        $deletePermission = checkAdminUserPermission('p4','delete');

        $role = new CrewRole;
        $role = $role
            ->select('crew_roles.*')
            ->where('role_title', '!=', Null)
            ->orderBy('crew_roles.id', 'DESC')
            ->get();
         
        return \View::make("admin/crewrole/list", compact('role','editPermission','deletePermission','addPermission'));
    }

    public function add(Request $request)
    {

        if(checkAdminUserPermission('p4','add') == false){
           return redirect("/");
        }

        return \View::make('admin/crewrole/add');
    }

    public function edit(Request $request, $id = null)
    {
        if(checkAdminUserPermission('p4','edit') == false){
           return redirect("/");
        }

        $role = "";
    
        if (!empty($id))
        {
            $role = CrewRole::where('id', $id)->first();
        } 
        
         
        return \View::make('admin/crewrole/edit', compact('id', 'role'));
    }

    public function store(Request $request)
    {
        if(checkAdminUserPermission('p4','add') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['role_title' => 'required'];

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
            
            $object = CrewRole::create($data);
             
            $lastid = $object->id;
             
            return redirect("admin/crewrole")
                ->with('success', "Data has been created successfully.")
                ->send();
        }
    }

    public function update(Request $request)
    {
        if(checkAdminUserPermission('p4','edit') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['role_title' => 'required'];

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
            $role = CrewRole::find($data['role_id']);
            $role->role_title = $data['role_title'];
    
             
            $role->save();  
              
            return redirect("admin/crewrole")
                ->with('success', "Data has been updated successfully.")
                ->send();
        }

    }

    public function delete($id)
    {
        if(checkAdminUserPermission('p4','delete') == false){
           return redirect("/");
        }

        try
        {
            CrewRole::where('id', '=', $id)->delete();
            return redirect("admin/crewrole")
                ->with('success', "Data has been deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {

            return redirect("admin/crewrole")->with('error', "Please try again")
                ->send();

        }

    }


  
}

