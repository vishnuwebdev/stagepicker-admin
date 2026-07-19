<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Role;
use App\Models\RoleType;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
//use Illuminate\Mail\Mailer;
class RoleController extends AdminController
{

    public function list(Request $request)
    {
        
        $role = new Role;
        $role = $role->orderBy('id', 'DESC')
            ->get();

        return \View::make("admin/role/list", compact('role'));
    }

    public function add(Request $request)
    {
		$roletype = RoleType::where('status','1')->orderBy('id', 'DESC')->get();
        return \View::make('admin/role/add',compact('roletype'));
    }

    public function edit(Request $request, $id = null)
    {
        $role = "";
		$roletype = RoleType::where('status','1')->orderBy('id', 'DESC')->get();
        if (!empty($id))
        {
            $role = Role::where('id', $id)->first();
        }
		 
        return \View::make('admin/role/edit', compact('id', 'role', 'roletype'));
    }

    public function store(Request $request)
    {
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
			
			$object = Role::create($data);
			 
			$lastid = $object->id;
			 
            return redirect("admin/role")
                ->with('success', "Data has been created successfully.")
                ->send();
        }
    }

    public function update(Request $request)
    {

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
            $role = Role::find($data['role_id']);
            $role->role_title = $data['role_title'];
			
			if(!empty($data['role_type'])){
				$role->role_type = $data['role_type'];
			}
			if(!empty($data['gender'])){
				$role->gender = $data['gender'];
			}
			if(!empty($data['age_min'])){
				$role->age_min = $data['age_min'];
			}
			if(!empty($data['age_max'])){
				$role->age_max = $data['age_max'];
			}
			if(!empty($data['skills'])){
				$role->skills = $data['skills'];
			}
			if(!empty($data['character_description'])){
				$role->character_description = $data['character_description'];
			}
			if(!empty($data['nudity_or_bareness'])){
				$role->nudity_or_bareness = $data['nudity_or_bareness'];
			}
			
            $role->save();  
			  
            return redirect("admin/role")
                ->with('success', "Data has been updated successfully.")
                ->send();
        }

    }

    public function delete($id)
    {
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



	public function roletype(Request $request)
    {
        if(checkAdminUserPermission('p3','view') == false){
           return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p3','edit');
        $addPermission = checkAdminUserPermission('p3','add');
        $deletePermission = checkAdminUserPermission('p3','delete');

        $role = new RoleType;
        $role = $role->whereNull('type')->orWhere('type','!=','1')->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/role/roletype", compact('role','editPermission','deletePermission','addPermission'));
    }

    public function addroletype(Request $request)
    {
        if(checkAdminUserPermission('p3','add') == false){
           return redirect("/");
        }

        return \View::make('admin/role/add_roletype');
    }

    public function editroletype(Request $request, $id = null)
    {
        if(checkAdminUserPermission('p3','edit') == false){
           return redirect("/");
        }

        $role = "";
        if (!empty($id))
        {
            $role = RoleType::where('id', $id)->first();
        }
        return \View::make('admin/role/edit_roletype', compact('id', 'role'));
    }

    public function storeroletype(Request $request)
    {
        if(checkAdminUserPermission('p3','add') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['title' => 'required', ];

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
            $role = new RoleType;
            $role->title = $data['title'];
            $role->status = $data['status'];
			$role->type = 0;
            $role->save();
            return redirect("admin/roletype")
                ->with('success', "Data has been created successfully.")
                ->send();
        }
    }

    public function updateroletype(Request $request)
    {
        if(checkAdminUserPermission('p3','edit') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['title' => 'required', ];

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
            $role = RoleType::find($data['id']);
            $role->title = $data['title'];
			$role->status = $data['status'];
            $role->save();
            return redirect("admin/roletype")
                ->with('success', "Data has been updated successfully.")
                ->send();
        }

    }

    public function deleteroletype($id)
    {
        if(checkAdminUserPermission('p3','delete') == false){
           return redirect("/");
        }

        try
        {
            RoleType::where('id', '=', $id)->delete();
            return redirect("admin/roletype")
                ->with('success', "Data has been deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {

            return redirect("admin/roletype")->with('error', "Please try again")
                ->send();

        }

    }
	
	
	
	public function crewroletype(Request $request)
    {
        $role = new RoleType;
        $role = $role->where('type','1')->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/role/crewroletype", compact('role'));
    }

    public function addcrewroletype(Request $request)
    {
        return \View::make('admin/role/add_crewroletype');
    }

    public function editcrewroletype(Request $request, $id = null)
    {
        $role = "";
        if (!empty($id))
        {
            $role = RoleType::where('id', $id)->first();
        }
        return \View::make('admin/role/edit_crewroletype', compact('id', 'role'));
    }

    public function storecrewroletype(Request $request)
    {
        $data = $request->all();
        $rules = ['title' => 'required', ];

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
            $role = new RoleType;
            $role->title = $data['title'];
            $role->status = $data['status'];
            $role->type = 1;
            $role->save();
            return redirect("admin/crewroletype")
                ->with('success', "Data has been created successfully.")
                ->send();
        }
    }

    public function updatecrewroletype(Request $request)
    {

        $data = $request->all();
        $rules = ['title' => 'required', ];

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
            $role = RoleType::find($data['id']);
            $role->title = $data['title'];
			$role->status = $data['status'];
            $role->save();
            return redirect("admin/crewroletype")
                ->with('success', "Data has been updated successfully.")
                ->send();
        }

    }

    public function deletecrewroletype($id)
    {
        try
        {
            RoleType::where('id', '=', $id)->delete();
            return redirect("admin/crewroletype")
                ->with('success', "Data has been deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {

            return redirect("admin/crewroletype")->with('error', "Please try again")
                ->send();

        }

    }


}

