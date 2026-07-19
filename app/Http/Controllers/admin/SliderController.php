<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\User as User;
use App\Models\Slider;
use App\Models\Setting;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
 

//use Illuminate\Mail\Mailer;
class SliderController extends AdminController {


    public function sliderList() { 

    	if(checkAdminUserPermission('p9','view') == false){
           return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p9','edit');
        $addPermission = checkAdminUserPermission('p9','add');
        $deletePermission = checkAdminUserPermission('p9','delete');
 
        $slider = new Slider;
        /* $post = $request->all();
        
        unset($post['page']);
        unset($post['btngo']);
        unset($post['type']);
        unset($post['orderby']);
        unset($post['order']);
        unset($post['search']); */

         
        $slider = $slider->orderBy('id', 'DESC')->get();
        
              
       return \View::make("admin/Sliders/slider",compact('slider','editPermission','deletePermission','addPermission'));
    }
 
     public function addSlider(Request $request) {

     	if(checkAdminUserPermission('p9','add') == false){
           return redirect("/");
        }
        return \View::make('admin/Sliders/add_slider');
    }
    
    public function editSlider(Request $request,$id=null) {

       if(checkAdminUserPermission('p9','edit') == false){
           return redirect("/");
        }

	   $editSlider = "";    
	   $id =  base64_decode($id);
	   if(!empty($id)){
		   $editSlider = Slider::where('id',$id)->first(); 
	   }
	   
	   return  \View::make('admin/Sliders/edit_slider',compact('id','editSlider'));
    }
    
    public function saveSlider(Request $request) {

    	if(checkAdminUserPermission('p9','add') == false){
           return redirect("/");
        }
        //if($request->ajax()){
           
            $data = $request->all();
            $rules = [ 
                'title'	            =>   'required', 
                'status'            =>   'required',
                'image'             =>   'required',			
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                //return response()->json(['errors'=>$validator->errors()]);	
				
				return redirect("admin/add-slider")->with('error', $validator->errors())
                ->send();
            }else {
                $time = time();
                $slider = new Slider;
 
                $slider->title          = $data['title']; 
                $slider->status         = $data['status'];
				
				if(!empty($data['website'])){
					$slider->website     = $data['website'];
				}
				
                $image = $request->file('image');
				 
                if ($image) {
					 if($image !== null){
						// This code use for image upload
						$destinationPath = '/slider/';
						$mime = $image->getMimeType();
						$responseData = Uploader::doUpload($image,$destinationPath,true,'',$mime);    
						if($responseData['status']=="true"){
							 $slider->image = $responseData['file'];
						}                             
					}   
				}
                $slider->save(); 
				
				// send notification to user
				 
				$users = DB::table('users')
                    ->whereIn('user_type', [1, 2])->where('status', '=', '1')->get();
					
				if(!empty($users)){
					foreach($users as $user){ 
						  
						 if($user->notification_status == 1){
							$message = "New opportunities have been banner posted!";
							$notification_array = array(
								'title' => 'Banner Posted',
								'body' => $message
							);
							//FCM api URL
							$url = 'https://fcm.googleapis.com/fcm/send';

							//api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
							$server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
							
							
							$fields = array();
							$fields['notification'] = $notification_array;
							$fields['to'] = $user->device_token;
							$headers = array(
								'Content-Type:application/json',
								'Authorization:key=' . $server_key
							);
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
							$responce = curl_exec($ch);
							curl_close($ch);
						}
					}
				}
			
                
                return redirect("admin/slider")->with('success', "Slider successful added.")->send();
                return response()->json(['success' => true], 200);
                // Session::flash('success_msg', __('Slider added successfully.'));
                // return response()->json(['success' => true], 200);
            }
        //}

    }
   
    public function updateSlider(Request $request) {

           if(checkAdminUserPermission('p9','edit') == false){
               return redirect("/");
           }

        //if($request->ajax()){
            $data = $request->all();
            $rules = [ 
                'title'	            =>   'required', 
                'status'	        =>   'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) { 
				return redirect("admin/edit-slider")->with('error', $validator->errors())
                ->send();
            }else {
                $time = time();
                $slider = Slider::find( $data['slider_id']); 
                $slider->title          = $data['title']; 
                $slider->status         = $data['status'];
				if(!empty($data['website'])){
					$slider->website     = $data['website'];
				}
                $image                  = $request->file('image');
        
                if ($image) {
                     if($image!==null){
                        // This code use for profile picture upload
                        $destinationPath = '/slider/';
                        $mime = $image->getMimeType();
                        $responseData = Uploader::doUpload($image,$destinationPath,true,'',$mime);    
                        if($responseData['status']=="true"){
                             $slider->image = $responseData['file'];
                        }                             
                    }   
                }
                
                $slider->save();
                $slider_id = base64_encode($data['slider_id']);
                return redirect("admin/edit-slider/".$slider_id)->with('success', "Slider updated successful.")->send();
                
                //Session::flash('success_msg', __('Slider updated successfully.'));
                return response()->json(['success' => true], 200);
            }
        //}

    }

    

    public function delete($id) {

    	if(checkAdminUserPermission('p9','delete') == false){
           return redirect("/");
        }

        try {
            $id =  base64_decode($id);
            Slider::where('id', '=', $id)->delete();
            return redirect("admin/slider")->with('success', "Slider deleted successfully")->send();
          } catch (\Exception $e) {
              
            return redirect("admin/slider")->with('error_msg', "Please try again")->send();
            
        }


    }
    

 
}
