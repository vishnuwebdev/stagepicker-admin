<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Skill;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
//use Illuminate\Mail\Mailer;
class SkillController extends AdminController
{

    public function skillList(Request $request)
    {
        if(checkAdminUserPermission('p3','view') == false){
           return redirect("/");
        }
        
        $editPermission = checkAdminUserPermission('p3','edit');
        $addPermission = checkAdminUserPermission('p3','add');
        $deletePermission = checkAdminUserPermission('p3','delete');

        $skill = new Skill;
        $skill = $skill->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/skill/list", compact('skill','editPermission','deletePermission','addPermission'));
    }

    public function add(Request $request)
    {
        if(checkAdminUserPermission('p3','add') == false){
           return redirect("/");
        }
        return \View::make('admin/skill/add');
    }

    public function edit(Request $request, $id = null)
    {
        if(checkAdminUserPermission('p3','edit') == false){
           return redirect("/");
        }

        $skill = "";
        if (!empty($id))
        {
            $skill = Skill::where('id', $id)->first();
        }
        return \View::make('admin/skill/edit', compact('id', 'skill'));
    }

    public function store(Request $request)
    {
        if(checkAdminUserPermission('p3','add') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['skill_name' => 'required', ];

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
            $skill = new Skill;
            $skill->skill_name = $data['skill_name'];
            $skill->save();
            return redirect("admin/skill")
                ->with('success', "Data has been created successfully.")
                ->send();
        }
    }

    public function update(Request $request)
    {
        if(checkAdminUserPermission('p3','edit') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['skill_name' => 'required', ];

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
            $skill = Skill::find($data['skill_id']);
            $skill->skill_name = $data['skill_name'];
            $skill->save();
            return redirect("admin/skill")
                ->with('success', "Data has been updated successfully.")
                ->send();
        }

    }

    public function delete($id)
    {
        if(checkAdminUserPermission('p3','delete') == false){
           return redirect("/");
        }

        try
        {
            Skill::where('id', '=', $id)->delete();
            return redirect("admin/skill")
                ->with('success', "Data has been deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {

            return redirect("admin/skill")->with('error', "Please try again")
                ->send();

        }

    }

}

