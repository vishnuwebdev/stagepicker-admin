<?php
namespace App\Http\Controllers\Admin;
use DB;
use App\Models\Classes;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
//use Illuminate\Mail\Mailer;
class ClassController extends AdminController
{

    public function list(Request $request)
    {
        $category = new Classes;
        $category = $category->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/classes/list", compact('category'));
    }

    public function add(Request $request)
    {
        return \View::make('admin/category/add');
    }

    public function edit(Request $request, $id = null)
    {
        $category = "";
        if (!empty($id))
        {
            $category = Category::where('id', $id)->first();
        }
        return \View::make('admin/category/edit', compact('id', 'category'));
    }

    public function store(Request $request)
    {
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

    public function  add_classes() 
    {

        
        return \View::make("admin/classes/add");


    }

    public function  store_classes(Request $request) 
    {
      

        $data = $request->all();
        $rules = [
        'title' => 'required', 
        'price' => 'required',
        'tags' => 'required',
        'duration_type' => 'required',
        'duration' => 'required',
        //'image' => 'required',
        'description' => 'required',
    ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        }else{
            if($request->hasFile('image')){
                $path_original = public_path().'/admin/uploads/class';
                $file = $request->image;
                $photo_name = time() . '-' . $file->getClientOriginalName();
                // dd($file);$request->file('file')->getClientOriginalName()
                $file->move($path_original, $photo_name);
                $data['image'] = $photo_name;
            }
            
             

             $InserARR=array(
                'title'=>$request->title,
                'price'=>$request->price,
                'tags'=>$request->tags,
                'duration'=>$request->duration,
                'duration_type'=>$request->duration_type,
                'image'=>$photo_name,
                'description'=>$request->description,
                );
          $Response= DB::table('classes')->insert($InserARR);
          if($Response){
            return  redirect("admin/classes")
            ->with('success', "Class Add  successfully")
            ->send();
     
        }else{
            return  redirect("admin/classes")
            ->with('error', "Something went wrong")
            ->send();
     

        }

     }

      
    }

    public function  view_classes($id) 
    {
       $data['category']=DB::table('classes')->where('id',$id)->get();
     
     return \View::make("admin/classes/view",$data);


    }


    public function  edit_classes($id) 
    {
       
       $data['category']=DB::table('classes')->where('id',$id)->get()->first();
     
     return \View::make("admin/classes/edit",$data);


    }

    public function  update_classes($id,Request $request) 
    {


        $data = $request->all();
        $rules = [
        'title' => 'required', 
        'price' => 'required',
        'tags' => 'required',
        'duration_type' => 'required',
        'duration' => 'required',
        //'image' => 'required',
        'description' => 'required',
        ];


    $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($validator->errors());
        }else{
            if($request->hasFile('image')){
                $path_original = public_path().'/admin/uploads/class';
                $file = $request->image;
                $photo_name = time() . '-' . $file->getClientOriginalName();
                // dd($file);$request->file('file')->getClientOriginalName()
                $file->move($path_original, $photo_name);
                $data['image'] = $photo_name;
            }
            
             

             $updateARR=array(
                'title'=>$request->title,
                'price'=>$request->price,
                'tags'=>$request->tags,
                'duration'=>$request->duration,
                'duration_type'=>$request->duration_type,
                'image'=>$photo_name,
                'description'=>$request->description,
                );
          $Response= DB::table('classes')->where('id',$id)->update($updateARR);
          if($Response){
            return  redirect("admin/classes")
            ->with('success', "Class update  successfully")
            ->send();
     
        }else{
            return  redirect("admin/classes")
            ->with('error', "Something went wrong")
            ->send();
     

        }

     }
    //    dd('jhfghfjg');
    //    $data['category']=DB::table('classes')->where('id',$id)->get()->first();
     
    //  return \View::make("admin/classes/edit",$data);


    }

    public function delete_classes($id)  
    {
    
        try
        {
              DB::table('classes')->where('id',$id)->delete();
            return redirect("admin/classes")
                ->with('success', "Category deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {
            return redirect("admin/classes")->with('error', "Please try again")
                ->send();

        }

    }
}

