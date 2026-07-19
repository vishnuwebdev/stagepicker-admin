<?php
namespace App\Http\Controllers\Admin;

use DB;
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
class CategoryController extends AdminController
{

    public function list(Request $request)
    {
         if(checkAdminUserPermission('p6','view') == false){
           return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p6','edit');
        $addPermission = checkAdminUserPermission('p6','add');
        $deletePermission = checkAdminUserPermission('p6','delete');

        $category = new Category;
        $category = $category->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/category/list", compact('category','editPermission','deletePermission','addPermission'));
    }

    public function add(Request $request)
    {
        if(checkAdminUserPermission('p6','add') == false){
           return redirect("/");
        }
        return \View::make('admin/category/add');
    }

    public function edit(Request $request, $id = null)
    {
        if(checkAdminUserPermission('p6','edit') == false){
           return redirect("/");
        }
        $category = "";
        if (!empty($id))
        {
            $category = Category::where('id', $id)->first();
        }
        return \View::make('admin/category/edit', compact('id', 'category'));
    }

    public function store(Request $request)
    {
        if(checkAdminUserPermission('p6','add') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['category_name' => 'required', ];

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
            $category = new Category;
            $category->category_name = $data['category_name'];
            $category->type = $data['type'];
            $category->save();
            return redirect("admin/category")
                ->with('success', "Category successful updated.")
                ->send();
            return response()
                ->json(['success' => true], 200);
        }
    }

    public function update(Request $request)
    {
        if(checkAdminUserPermission('p6','edit') == false){
           return redirect("/");
        }

        $data = $request->all();
        $rules = ['category_name' => 'required', ];

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
            $category = Category::find($data['category_id']);
            $category->category_name = $data['category_name'];
			$category->type = $data['type'];
            $category->save();
            return redirect("admin/category")
                ->with('success', "Category successful updated.")
                ->send();
        }

    }

    public function delete($id)
    {
        if(checkAdminUserPermission('p6','delete') == false){
           return redirect("/");
        }

        try
        {
            Category::where('id', '=', $id)->delete();
            return redirect("admin/category")
                ->with('success', "Category deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {
            return redirect("admin/category")->with('error', "Please try again")
                ->send();

        }

    }

}

