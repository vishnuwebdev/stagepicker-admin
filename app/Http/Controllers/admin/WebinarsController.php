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
class WebinarsController extends AdminController
{
    public function list(Request $request)
    {

     
        $data['webinars']=DB::table('webinars')->get();

        return \View::make("admin/webinar/list",$data);
    }


    public function add()
    {

        return \View::make("admin/webinar/add");
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $rules = [
        'title' => 'required', 
        'price' => 'required',
        'date' => 'required',
        'time' => 'required',
        'link' => 'required',
        //'image' => 'required',
        
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
                $path_original = public_path().'/admin/uploads/webinar';
                $file = $request->image;
                $photo_name = time() . '-' . $file->getClientOriginalName();
                // dd($file);$request->file('file')->getClientOriginalName()
                $file->move($path_original, $photo_name);
                $data['image'] = $photo_name;
            }

             $InserARR=array(
                'title'=>$request->title,
                'price'=>$request->price,
                'date'=>$request->date,
                'time'=>$request->time,
                'link'=>$request->link,
                'image'=>$photo_name,
              
                );
          $Response= DB::table('webinars')->insert($InserARR);
          if($Response){
            return  redirect("admin/webinars")
            ->with('success', "Webinars Add  successfully")
            ->send();
     
        }else{
            return  redirect("admin/webinars")
            ->with('error', "Something went wrong")
            ->send();
     

        }

     }

    }
    public function  view($id) 
    {
       
       $data['weninar']=DB::table('webinars')->where('id',$id)->get()->first();
     
     return \View::make("admin/webinar/view",$data);


    }


    public function  edit($id) 
    {
      
       $data['weninar']=DB::table('webinars')->where('id',$id)->get()->first();
     
     return \View::make("admin/webinar/edit",$data);


    } 

     public function  update($id,Request $request) 
    {
     
        $data = $request->all();
        $rules = [
            'title' => 'required', 
            'price' => 'required',
            'date' => 'required',
            'time' => 'required',
            'link' => 'required',
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
                $path_original = public_path().'/admin/uploads/webinar';
                $file = $request->image;
                $photo_name = time() . '-' . $file->getClientOriginalName();
                // dd($file);$request->file('file')->getClientOriginalName()
                $file->move($path_original, $photo_name);
                $data['image'] = $photo_name;
            }
             $updateARR=array(
                'title'=>$request->title,
                'price'=>$request->price,
                'date'=>$request->date,
                'time'=>$request->time,
                'link'=>$request->link,
                'image'=>$photo_name,
                );
          $Response= DB::table('webinars')->where('id',$id)->update($updateARR);
          if($Response){
            return  redirect("admin/webinars")
            ->with('success', "Class update  successfully")
            ->send();
     
        }else{
            return  redirect("admin/webinars")
            ->with('error', "Something went wrong")
            ->send();
     

        }
    }
     

    } 

    public function delete($id)  
    {
    
        try
        {
              DB::table('webinars')->where('id',$id)->delete();
            return redirect("admin/webinars")
                ->with('success', "Category deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {
            return redirect("admin/webinars")->with('error', "Please try again")
                ->send();

        }

    }

}