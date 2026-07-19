<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Career;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
//use Illuminate\Mail\Mailer;
class CareerController extends AdminController
{

    public function careerList(Request $request)
    {

        $editPermission = checkAdminUserPermission('p3','edit');
        $addPermission = checkAdminUserPermission('p3','add');
        $deletePermission = checkAdminUserPermission('p3','delete');

        if(checkAdminUserPermission('p3','view') == false){
           return redirect("/");
        }

        $career = new Career;
        $career = $career->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/career/list", compact('career','editPermission','deletePermission','addPermission'));
    }

    public function add(Request $request)
    {
        if(checkAdminUserPermission('p3','add') == false){
           return redirect("/");
        }

        return \View::make('admin/career/add');
    }

    public function edit(Request $request, $id = null)
    {
        if(checkAdminUserPermission('p3','edit') == false){
           return redirect("/");
        }

        $career = "";
        if (!empty($id))
        {
            $career = Career::where('id', $id)->first();
        }
        return \View::make('admin/career/edit', compact('id', 'career'));
    }

    public function store(Request $request)
    {
        if(checkAdminUserPermission('p3','add') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['name' => 'required', ];

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
            $career = new Career;
            $career->name = $data['name'];
            $career->save();
            return redirect("admin/career")
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
        $rules = ['name' => 'required', ];

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
            $career = Career::find($data['career_id']);
            $career->name = $data['name'];
            $career->save();
            return redirect("admin/career")
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
            Career::where('id', '=', $id)->delete();
            return redirect("admin/career")
                ->with('success', "Data has been deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {

            return redirect("admin/career")->with('error', "Please try again")
                ->send();

        }

    }

}

