<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\User as User;
use Auth;
use App\Models\Postaudition;
use App\Models\Pages;
use App\Models\RecentLogin;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;

class HomeController extends AdminController
{
    public function dashboard(Request $request)
    {
        $activeauditioners = User::where('user_type', '1')->where('status', '1')
            ->count();
            
        $datebefore = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . " -48 hours"));
            
        $recentactiveusers = RecentLogin::where('created_at','>', $datebefore)->where('status', '1')
            ->count();
            
        $auditioners = User::where('user_type', '1')->get();
        $producer = User::where('user_type', '2')->get();
        
        $activeuserper = round(($recentactiveusers * 100)/ (count($auditioners) + count($producer)));
         
        
            
        $totalPostaudition = Postaudition::count();

        $postaudition = DB::table('post_auditions')
            ->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
            ->join('users', 'post_auditions.user_id', '=', 'users.id')
            ->select('post_auditions.*', 'categories.category_name', 'users.name')
            ->orderBy('id', 'DESC')
            ->get();

        $categories = DB::table('post_auditions')
        ->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
        ->select('categories.category_name', DB::raw("COUNT(post_auditions.id) as item"))
        ->orderBy('categories.category_name', 'ASC')
            ->groupBy('categories.id')
            ->get();    
        return \View::make('admin/dashboard', compact('categories','auditioners', 'producer','totalPostaudition', 'postaudition','activeauditioners','recentactiveusers','activeuserper'));

    }

    

    public function profile(Request $request)
    {
        $user = Auth::user();
        return \View::make('admin/profile', compact('user'));
    }

    public function updateprofile(Request $request)
    {
        $data = $request->all();
        $rules = ['name' => 'required', ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return response()
                ->json(['errors' => $validator->errors() ]);
        }
        else
        {
            $user = Auth::user();
            $user = User::find($user['id']);
            $user->name = $request['name'];
            $user->phone = $request['phone'];
            $user->save();
            return redirect("admin/profile")
                ->with('success', "Profile updated successfully")
                ->send();
        }
    }

    public function changepassword(Request $request)
    {
        $user = Auth::user();
        return \View::make('admin/changepass', compact('user'));
    }

    public function updatepass(Request $request)
    {
        $data = $request->all();
        $rules = ['old_password' => 'required', 'new_password' => 'required|min:8|max:20', 'cnfrmpassword' => 'required|same:new_password', ];
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
            $user = Auth::user();
            $UserObj = User::find($user['id']);
            if ($UserObj)
            {
                if (Hash::check($data['old_password'], $UserObj->password))
                {
                    $UserObj->password = Hash::make($data['new_password']);
                    $UserObj->save();
                    return redirect("admin/changepassword")
                        ->with('success', "Password Change Successfully")
                        ->send();
                }
                else
                {
                    return redirect("admin/changepassword")
                        ->with('error', "Old Password do not match, Please try again")
                        ->send();
                }
            }
            else
            {
                return redirect("admin/changepassword")
                    ->with('error', "Something went wrong, Please try again")
                    ->send();
            }
        }
    }

    public function cmslist(Request $request)
    {
        if(checkAdminUserPermission('p8','view') == false){
           return redirect("/");
        }
        $editPermission = checkAdminUserPermission('p8','edit');
        
        $pages = new Pages;
        $pages = $pages->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/pages/list", compact('pages','editPermission'));
    }

    public function editpage(Request $request, $id = null)
    {
        $pages = "";
        if (!empty($id))
        {
            $pages = Pages::where('id', $id)->first();
        }
        return \View::make('admin/pages/edit', compact('id', 'pages'));
    }

    public function updatePage(Request $request)
    {
        $data = $request->all();
        $rules = ['title' => 'required', 'content' => 'required', ];
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
            $category = Pages::find($data['page_id']);
            $category->title = $data['title'];
            $category->content = $data['content'];
            $category->save();
            return redirect("admin/cms")
                ->with('success', "Pages successful updated.")
                ->send();
        }
    }
}

