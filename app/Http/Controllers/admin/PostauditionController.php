<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Category;
use App\Models\Skill;
use App\Models\Postaudition;
use App\Models\ProductionCrew;
use App\Models\Role;
use App\Models\CrewRole;
use App\Models\Report;
use App\Models\User;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\CarbonInterval;
use \Session;
use App\Lib\Uploader;
//use Illuminate\Mail\Mailer;
class PostauditionController extends AdminController
{

    public function list(Request $request)
    {

    	if(checkAdminUserPermission('p3','view') == false){
           return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p3','edit');
        $deletePermission = checkAdminUserPermission('p3','delete');

        $postaudition = DB::table('post_auditions')
            ->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
            ->join('users', 'post_auditions.user_id', '=', 'users.id')
            ->select('post_auditions.*', 'categories.category_name', 'users.name')
            ->orderBy('id', 'DESC')
            ->get();

         
               
        return \View::make("admin/audition/list", compact('postaudition','editPermission','deletePermission'));
    }
    
    public function edit($pid) { 

        if(checkAdminUserPermission('p3','edit') == false){
           return redirect("/");
        }

        $postaudition =  DB::table('post_auditions')
					->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
					->leftJoin('users', 'post_auditions.user_id', '=', 'users.id')
					->select('post_auditions.*', 'categories.category_name','users.name as producer_name')
					->where('post_auditions.id', $pid)
					->first();
        
         $skills = Skill::orderBy('id', 'DESC')->get();
         $ethnicities=DB::table('ethnicities')->where(['status'=>1])->orderBy('id', 'DESC')->get();
        // $role = Role::orderBy('id', 'DESC')->get();
        $category = Category::whereNull('type')->orWhere('type','!=','1')->orderBy('id', 'DESC')->get();

        if(!empty($postaudition)){
             
             
            return \View::make("admin/audition/edit", compact('pid','skills','ethnicities','postaudition','category'));

        }else{

            return redirect("/admin/postaudition")->with('error_msg', "No User found!")->send();
        }
    }

    public function editAudition(Request $request) {

    	if(checkAdminUserPermission('p3','edit') == false){
           return redirect("/");
        }

    	$data = $request->all();
        $rules = [
        
		
            'audition_title' => 'required', 
            'audition_date' => 'required', 
            'audition_time' => 'required', 
            //'age_group' => 'required',
            'location' => 'required',
            'production_description' => 'required',
           
            ];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return redirect("/admin/postaudition/edit/". $request['p_id'])->withErrors($validator);

        } else {
          

            $postaudition = Postaudition::find($request['p_id']);
              
			$postaudition->ethnicities = $request['ethnicities'];
			
			
            $postaudition->audition_title = $request['audition_title'];
			
            if(!empty($request['category'])){
               $postaudition->category = $request['category']; 
            }else{
                $postaudition->category = 0;
            }
            if(!empty($data['audition_type'])){
               $postaudition->audition_type = $request['audition_type']; 
            }
            if(!empty($data['photography_type'])){
               $postaudition->photography_type = $request['photography_type']; 
            }
            
			if(!empty($request['audition_date']) && !empty($request['audition_time'])){
                $postaudition->expire_date = date('d-m-Y h:i A',strtotime($request['audition_date']. " ". $request['audition_time']));
            }
			
			if(!empty($request['gender'])){
                $postaudition->gender = $request['gender'];
            }
			
            if(!empty($request['union_type'])){
				$postaudition->union_type = $request['union_type'];
            }
			
            if(!empty($request['roles'])){
                $postaudition->roles = $request['roles'];
            }
			
			if(!empty($request['age_group'])){
				$postaudition->age_group = $request['age_group'];
			}
			
            $postaudition->location = $request['location'];
            $postaudition->status = '1';
            $postaudition->production_description = $request['production_description'];
             
			
			if(!empty($request['skills'])){
				$postaudition->skills = $request['skills'];
			}
			if(!empty($request['studio_name'])){
				$postaudition->studio_name = $request['studio_name'];
			}
			
			if(!empty($request['compensation'])){
				$postaudition->compensation = $request['compensation'];
			}
			
			if(!empty($request['compensation_description'])){
				$postaudition->compensation_description = $request['compensation_description'];
			}
			
			if(!empty($request['production_website'])){
				$postaudition->production_website = $request['production_website'];
			}
			
			if(!empty($request['audition_location'])){
				$postaudition->audition_location = $request['audition_location'];
			}

			 $postaudition->save(); 
			
			 return redirect("admin/postaudition")
                ->with('success', "Audition successful updated.")
                ->send();;

        }

        
    }

    public function detailbackup(Request $request,$id=null) {
        $detail = "";    
            $id =  base64_decode($id);
            if(!empty($id)){
				$detail = DB::table('post_auditions')
				->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
				->leftjoin("roles",\DB::raw("FIND_IN_SET(roles.id,post_auditions.roles)"),">",\DB::raw("'0'"))
				->select('post_auditions.*', 'categories.category_name',
					DB::raw("GROUP_CONCAT(roles.role_title) as roles")
				)
				->where('post_auditions.id', $id)
				->first();
				
			
				$participants = DB::table('post_audition_participants')
				->join('users', 'post_audition_participants.user_id', '=', 'users.id')
				->leftJoin('roles', 'post_audition_participants.role_id', '=', 'roles.id')
				->select('post_audition_participants.*', 'users.*', 'roles.role_title')
				->where('post_audition_id', $id)
				->get();
				
				$photos = DB::table('post_auditions_photographies') 
					->select('post_auditions_photographies.*')
					->where('post_auditions_photographies.post_auditions_id', $id)
					->get();
				 
            }
			 
			
         return \View::make('admin/audition/detail',compact('detail', 'participants','rolesData','photos'));
    }
	
	
	public function detail(Request $request,$id=null) {

       if(checkAdminUserPermission('p3','view') == false){
           return redirect("/");
        }

        $detail = "";    
            $id =  base64_decode($id);
            if(!empty($id)){
				
				$postauditionval = DB::table('post_auditions')
					->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
					->leftJoin('users', 'post_auditions.user_id', '=', 'users.id')
					->select('post_auditions.*', 'categories.category_name','users.name as producer_name')
					->where('post_auditions.id', $id)
					->first();
				//dd($postauditionval);
				 
				$detail = array();
				
				$audition_type = $postauditionval->audition_type;
				
				$rolesData = array();
				$participants = array();
				$photos = array();

				if($audition_type == '2'){
					$participants = DB::table('post_audition_participants')
						->join('users', 'post_audition_participants.user_id', '=', 'users.id') 
						->select('post_audition_participants.*', 'users.*')
						->where('post_audition_id', $id)
						->get(); 
						
					$photos = DB::table('post_auditions_photographies') 
					->select('post_auditions_photographies.*')
					->where('post_auditions_photographies.post_auditions_id', $id)
					->get();
				}else{
					$roles = $postauditionval->roles;
				 
					$allroles = explode(',',$roles);
					 
					if(!empty($allroles)){
						foreach($allroles as $rol){
							$rol = trim($rol);
							if(is_numeric($rol)){
								$resrole = Role::where("roles.id", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*','role_types.title as role_type')
								->first();
								if(!empty($resrole)){ 
									// get all participants applied to this audition and role
									
									$participantData = DB::table('post_audition_participants') 
										->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id') 
										->select('users.*')
										->where('post_audition_participants.role_id', $resrole->id)
										->where('post_audition_participants.post_audition_id', $postauditionval->id) 
										->get();
										
									$resrole->participants = $participantData; 
									
									$rolesData[] = $resrole;
										
								}
							}else{
								$resrole = Role::where("roles.role_title", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*','role_types.title as role_type')
								->first();
								if(!empty($resrole)){
									 
									// get all participants applied to this audition and role
									
									$participantData = DB::table('post_audition_participants') 
										->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id') 
										->select('users.name', 'users.image','users.age','users.height','users.email','users.phone','users.pro_member','users.status')
										->where('post_audition_participants.role_id', $resrole->id)
										->where('post_audition_participants.post_audition_id', $postauditionval->id) 
										->get();
										 
									$resrole->participants = $participantData; 
									
									$rolesData[] = $resrole;
								}
							}
						}
					}
				}
				 
				$detail = array 
				(
					  "id"=> $postauditionval->id,  
					  "user_id"=> $postauditionval->user_id,  
					  "audition_title"=> $postauditionval->audition_title,  
					  "category"=> $postauditionval->category,  
					  "expire_date"=> $postauditionval->expire_date,
					  "gender"=> $postauditionval->gender,  
					  "union_type"=> $postauditionval->union_type,
					  "audition_type"=> $postauditionval->audition_type,
					  "age_group"=> $postauditionval->age_group,  
					  "location"=> $postauditionval->location,  
					  "photography_type"=> $postauditionval->photography_type,  
					  "photography_image"=> $postauditionval->photography_image,  
					  "production_description"=> $postauditionval->production_description,  
					  "skills"=> $postauditionval->skills,  
					  "status"=> $postauditionval->status,  
					  "category_name"=> $postauditionval->category_name, 
					  "studio_name"=> $postauditionval->studio_name,
					  "compensation"=> $postauditionval->compensation,
					  "compensation_description"=> $postauditionval->compensation_description,
					  "production_website"=> $postauditionval->production_website, 
					  "audition_location"=> $postauditionval->audition_location, 
					  "producer_name"=>$postauditionval->producer_name,
					  "roles"=> $rolesData, 				  
					  "participants"=> $participants 				  
				); 
				
				$detail = (object)$detail;
            }
		//dd($detail);	 
			
		return \View::make('admin/audition/detail',compact('detail', 'participants','photos'));
		
    }
    
    public function delete($id)
    {

        if(checkAdminUserPermission('p3','delete') == false){
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
    
    public function auditionPostAudition(Request $request)
    {
        $data = $request->all();
        $rules = [
            'post_aud_id'  => 'required', 
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
			return response()->json(['errors'=>$validator->errors()]);
        } else {
            $audition = Postaudition::find($data['post_aud_id']);
            $audition->pin_to_top = $data['pin_to_top']; 
 
            $audition->save();
            
			return response()->json(['success' => true], 200);
        }

    }
	 
	public function productioncrewlist(Request $request){
		$postaudition = DB::table('production_crews')
            ->leftJoin('categories', 'production_crews.category', '=', 'categories.id')
            ->join('users', 'production_crews.user_id', '=', 'users.id')
            ->select('production_crews.*', 'categories.category_name', 'users.name')
            ->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/audition/crewlist", compact('postaudition'));
	}
	
	
	public function auditionProdCrew(Request $request)
    {
        $data = $request->all();
        $rules = [
            'post_aud_id'  => 'required', 
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
			return response()->json(['errors'=>$validator->errors()]);
        } else {
            $audition = ProductionCrew::find($data['post_aud_id']);
            $audition->pin_to_top = $data['pin_to_top']; 
 
            $audition->save();
            
			return response()->json(['success' => true], 200);
        }

    }
   
	public function crewdetail(Request $request,$id=null) {
        $detail = "";    
            $id =  base64_decode($id);
            if(!empty($id)){
				
				$postauditionval = DB::table('production_crews')
					->leftJoin('categories', 'production_crews.category', '=', 'categories.id')
					->select('production_crews.*', 'categories.category_name')
					->where('production_crews.id', $id)
					->first();
				//dd($postauditionval);
				 
				$detail = array();
				
				 
				$rolesData = array();
				$participants = array();
				$photos = array();

				 
				$roles = $postauditionval->roles;
			 
				$allroles = explode(',',$roles);
				 
				if(!empty($allroles)){
					foreach($allroles as $rol){
						$rol = trim($rol);
						 
						$resrole = CrewRole::where("crew_roles.id", $rol)
						->leftJoin('role_types', 'crew_roles.role_type', '=', 'role_types.id')
						->leftJoin('categories', 'crew_roles.category', '=', 'categories.id')
						->select('crew_roles.*','role_types.title as role_type','categories.category_name as category')
						->first();
						if(!empty($resrole)){ 
							// get all participants applied to this audition and role
							$participantData = array();
							/* $participantData = DB::table('post_audition_participants') 
								->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id') 
								->select('users.*')
								->where('post_audition_participants.role_id', $resrole->id)
								->where('post_audition_participants.post_audition_id', $postauditionval->id) 
								->get();*/
								
							$resrole->participants = $participantData;  
							
							$rolesData[] = $resrole;
								
						} 
					}
				}
				 
				 
				$detail = array 
				(
					"id"=> $postauditionval->id,  
					"user_id"=> $postauditionval->user_id,  
					"title"=> $postauditionval->title,  
					"category"=> $postauditionval->category,  
					"expire_date"=> $postauditionval->expire_date, 
					"union_type"=> $postauditionval->union_type,   
					"location"=> $postauditionval->location,  
					"description"=> $postauditionval->description,  
					"skills"=> $postauditionval->skills,  
					"status"=> $postauditionval->status,  
					"category_name"=> $postauditionval->category_name, 
					"compensation"=> $postauditionval->compensation,
					"compensation_description"=> $postauditionval->compensation_description,
					"website_url"=> $postauditionval->website_url, 
					"roles"=> $rolesData, 				  
					"participants"=> $participants 				  
				); 
				
				$detail = (object)$detail;
            }
		//dd($detail);	 
			
		return \View::make('admin/audition/crewdetail',compact('detail', 'participants','photos'));
		
    }
    
	public function report(Request $request)
    {
        $reports = DB::table('reports')
            ->leftJoin('post_auditions', 'reports.post_audition_id', '=', 'post_auditions.id')
            ->join('users', 'reports.user_id', '=', 'users.id')
            ->select('reports.*', 'post_auditions.audition_title', 'users.name as user_name')
            ->orderBy('id', 'DESC')
            ->get();
        return \View::make("admin/audition/reportlist", compact('reports'));
    }
	
	public function reportdelete($id)
    {
        try
        {
            Report::where('id', '=', $id)->delete();
            return redirect("admin/report")
                ->with('success', "Data deleted successfully")
                ->send();
        }
        catch(\Exception $e)
        {
            return redirect("admin/report")->with('error', "Please try again")
                ->send();
        }

    }
    
	public function updateStatus(Request $request)
    {
        $data = $request->all();
        $rules = [
            'Id'  => 'required', 
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
			return response()->json(['errors'=>$validator->errors()]);
        } else {
            $audition = Postaudition::find($data['Id']);
			 
            $audition->status = $data['status']; 
 
            $audition->save();
			
			$userId = $audition->user_id;
			$user = User::find($userId);
			
			if($user->notification_status == 1){
				if($data['status'] == '1'){ 
					$message = "Administrator activated your post.";
					$notification_array = array(
						'title' => 'Post activated',
						'body' => $message
					);
				}else{
					$message = "Administrator deactivated your post.";
					$notification_array = array(
						'title' => 'Post deactivated',
						'body' => $message
					); 
				}
				
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
			return response()->json(['success' => true], 200);
        }

    }
	
	
	public function updateCrewStatus(Request $request)
    {
        $data = $request->all();
        $rules = [
            'Id'  => 'required', 
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
			return response()->json(['errors'=>$validator->errors()]);
        } else {
            $audition = ProductionCrew::find($data['Id']);
			 
            $audition->status = $data['status']; 
 
            $audition->save();
			
			$userId = $audition->user_id;
			$user = User::find($userId);
			
			if($user->notification_status == 1){
				if($data['status'] == '1'){ 
					$message = "Administrator activated your audition crew.";
					$notification_array = array(
						'title' => 'Crew activated',
						'body' => $message
					);
				}else{
					$message = "Administrator deactivated your audition crew.";
					$notification_array = array(
						'title' => 'Crew deactivated',
						'body' => $message
					); 
				}
				
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
			return response()->json(['success' => true], 200);
        }

    }
	
}

