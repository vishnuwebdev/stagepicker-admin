<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pages;
use App\Models\Skill;
use App\Models\Career;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Portfolio;
use App\Models\Credit;
use App\Models\Role;
use App\Models\RecentLogin;
use App\Models\Merchandise;
use App\Models\Compcard;
use App\Models\ExtCasting;
use App\Models\Auditionparticipant;
use App\Models\Favourite;
use App\Models\Booking;


use App\Models\CompanyVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use \Session;
use Socialite;
use App\Models\Mail as Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\ContactRequest;
use URL;
use DB;
use File;
use App\Models\RoleType;

class ClassController extends ApiController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $Request;
    protected $User;

    public function __construct(Request $request, User $user)
    {
        parent::__construct();
        // Store All Type Request
        $this->Request = $request;
        $this->User = $user;
    }

    function response($data)
    {
        //return response()->json($this->arrayHandleFun($data),200);
        $data = $this->arrayHandleFun($data);
        // This header pass for allow origin control.
        header("Access-Control-Allow-Origin: *"); 
        header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization, X-CSRF-Token');
        header('Access-Control-Allow-Credentials: true');   
        echo json_encode($data);
        die();
    }

    function validationHandle($validation){
        foreach ($validation->getMessages() as $field_name => $messages){
            if(!isset($firstError)){
                $firstError        =$messages[0];
                $error[$field_name]=$messages[0];
            }
        }
        return $firstError;
    }
	
      
	function arrayHandleFun($array, $isRepeat = false){
        
        foreach ($array as $key => $value) {
            if ($value === null) { 
                $array[$key] =  "";
            }else if (is_array($value)) {
                if (empty($value)){
                    //$array[$key] = "";
                }else{
                    $array[$key] = SELF::arrayHandleFun($value);
                } 
            }
        }
        if (!$isRepeat) {
            $array = SELF::arrayHandleFun($array,true);
        }
        return $array;        
    }  


    public function get_data(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $class=DB::table('classes')->where('status',1)->orderBy('id','desc')->get();
			foreach($class as $ks=>$vs)
			{

				$class[$ks]->image=asset('public/admin/uploads/class/'.$vs->image);

				// Post-purchase access info (link/location) is never sent
				// on public catalog endpoints — only get_booking_detail
				// returns it, and only for a confirmed booking. See
				// WEBINAR_SEMINAR_BOOKING_PLAN.md "Post-purchase access".
				unset($class[$ks]->link);
				unset($class[$ks]->location);
			}

			$webinars=DB::table('webinars')->where('status',1)->orderBy('id','desc')->get();
		    $array['class']=$class;
			foreach($webinars as $ks=>$vs)
			{
				
				$webinars[$ks]->image=asset('public/admin/uploads/webinar/'.$vs->image);
			}
		    $array['webinars']=$webinars;
			$mdata= Merchandise::with('get_image')->where('status',1)->orderBy('id','desc')->get();
			foreach($mdata as $ks=>$vs)
			{
				
				$mdata[$ks]->image=asset('public/admin/uploads/merchandise/'.$vs->image);
				
				
			}
			$array['merchandise']=$mdata;
            $response['data'] = $array;
            $response['message'] ="List of data";
            return response()->json($response);
            
        }
    }
	
	 public function get_class_details(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'class_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $class=DB::table('classes')->where('status',1)->where('id',$request->class_id)->first();

			if (!$class) {
				$response['status'] = "false";
				$response['message'] = "Class not found";
				return response()->json($response);
			}

			$class->image=asset('public/admin/uploads/class/'.$class->image);

			// How many seats are still available, if this class has a
			// max_seats cap — informational only, doesn't gate booking
			// (create_booking re-checks capacity server-side at the time
			// of booking, this is just for display before that).
			if (!empty($class->max_seats)) {
				$taken = Booking::where('bookable_type', 'class')
					->where('bookable_id', $class->id)
					->whereIn('status', [Booking::STATUS_PENDING_PAYMENT, Booking::STATUS_CONFIRMED])
					->sum('quantity');
				$class->seats_left = max(0, (int) $class->max_seats - (int) $taken);
			} else {
				$class->seats_left = null;
			}

			// Post-purchase access info is never sent here — only
			// get_booking_detail, and only for a confirmed booking. See
			// WEBINAR_SEMINAR_BOOKING_PLAN.md "Post-purchase access".
			unset($class->link);
			unset($class->location);

            $response['data'] = $class;
            $response['message'] ="Class Details";
            return response()->json($response);

        }
    }
	
	 public function get_webinar_details(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'webinar_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $class=DB::table('webinars')->where('status',1)->where('id',$request->webinar_id)->first();
			$class->image=asset('public/admin/uploads/webinar/'.$class->image);
			
            $response['data'] = $class;
            $response['message'] ="webinar Details";
            return response()->json($response);
            
        }
    }
	
	 public function get_merchandise_details(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'm_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $mdata= Merchandise::with('get_image','get_date')->where('status',1)->where('id',$request->m_id)->first();
			
			 $mdata->image=asset('public/admin/uploads/merchandise/'.$mdata->image);
			
            $response['data'] = $mdata;
            $response['message'] ="Merchandise Details";
            return response()->json($response);
            
        }
    }

    function addCredit(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'projectType' => 'required', 
            'projectName' => 'required', 
            'directName' => 'required', 
            'role' => 'required',
            'month' => 'required',
            'year' => 'required',
            'notes' => 'required',
            'projectDate'=>'required',
            'productionCompany'=>'required',
		];
		
		   
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			    DB::table("credits")->insert($request->all());
                $response['status'] = "true";
                $response['message'] ="Credit has been added Successfully";
                return response()->json($response);
        }
    }

    function updateCredit(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'id' => 'required',
            'projectType' => 'required', 
            'projectName' => 'required', 
            'directName' => 'required', 
            'role' => 'required',
            'month' => 'required',
            'year' => 'required',
            'notes' => 'required',
            'projectDate'=>'required',
            'productionCompany'=>'required',
		];
		
		   
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $data=[
            'projectType' => $request->projectType, 
            'projectName' => $request->projectName, 
            'directName' => $request->directName, 
            'role' => $request->role,
            'month' => $request->month,
            'year' => $request->year,
            'notes' => $request->notes,
            'projectDate'=>$request->projectDate,'productionCompany'=>$request->productionCompany];

			    DB::table("credits")->where('id',$request->id)->update($data);
                $response['status'] = "true";
                $response['message'] ="Updated has been added Successfully";
                return response()->json($response);
        }
    }
	
    public function get_credits(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $mdata= Credit::where('user_id',$request->user_id)->get();	
            $response['data'] = $mdata;
            $response['message'] ="Credit List";
            return response()->json($response);
        }
    }
	
    public function delete_credits(Request $request)
    {
        $data = $request->all();
        $rules = [
            'id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $mdata= Credit::where('id',$request->id)->delete();	
            $response['message'] ="Credit Deleted";
            return response()->json($response);
        }
    }


    public function get_emoji(Request $request)
    {
       	
           $fname=[];
            if ($handle = opendir(public_path('/assets/front/emoji'))) {

                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $fname[]= URL::to('/').'/public/assets/front/emoji/'.$entry;
                    }
                }
                closedir($handle);
            }
               
            

            $response['message'] ="emoji list";
            $response['status'] ="true";
            $response['data'] =$fname;
            
            return response()->json($response);
        
    }
	
    
    function add_compcard(Request $request) {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'name' => 'required',
            'height' => 'required',
            'bust' => 'required',
            'hips' => 'required',
            'eyes' => 'required',
            'shoes' => 'required',
            'dress' => 'required',
    	];

        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        } else  {
            $data=array();
            if($request->file('cover_photo')){ 
            $file = $request->file('cover_photo');
            $name= 'public/assets/compcard/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/compcard/',  $name);
            
            $data['cover_photo'] =  $name;
            }
            if($request->file('photo2')){ 
            $file = $request->file('photo2');
            $name= 'public/assets/compcard/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/compcard/',  $name);
            $data['photo2'] =  $name;
            
            }
            if($request->file('photo3')){ 
            $file = $request->file('photo3');
            $name= 'public/assets/compcard/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/compcard/',  $name);
            $data['photo3'] =  $name;
            
            }
            if($request->file('photo4')){ 
            $file = $request->file('photo4');
            $name= 'public/assets/compcard/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/compcard/',  $name);
            $data['photo4'] =  $name;
            }
            if($request->file('custom_photo')){ 
            $file = $request->file('custom_photo');
            $name= 'public/assets/compcard/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/compcard/',  $name);
            $data['custom_photo'] =  $name;
            }
        
            $data['user_id']=$request->user_id;
            $data['name']=$request->name;
            $data['height']=$request->height;
            $data['bust']=$request->bust;
            $data['hair_color']=isset($request->hairColor) ? $request->hairColor : '';
            $data['hips']=$request->hips;
            $data['eyes']=$request->eyes;
            $data['shoes']=$request->shoes;
            $data['dress']=$request->dress;
            if($request->id=="") {
                DB::table("compcards")->insert($data);
            } else {
                DB::table("compcards")->where('id',$request->id)->update($data);
            }
            $response['status'] = "true";
            $response['message'] ="Compcard has been added Successfully";
            return response()->json($response);
        }

    }


    public function get_compcard(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $mdata= Compcard::where('user_id',$request->user_id)->first();	
            $response['data'] = $mdata;
            $response['message'] ="Compcard Data";
            return response()->json($response);
        }
    }

    public function get_ext_casting(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            //where('user_id',$request->user_id)->
            $mdata= ExtCasting::get();	
            $response['data'] = $mdata;
            $response['message'] ="External Casting Data";
            return response()->json($response);
        }
    }

    public function delete_compcard(Request $request)
    {
        $data = $request->all();
        $rules = [
            'id' => 'required',
            ];

        $validator = Validator::make($request->all() , $rules);

        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
			$array=array();
            $mdata= Compcard::where('id',$request->id)->delete();	
            $response['message'] ="compcard Deleted";
            return response()->json($response);
        }
    }

    public function get_ext_casting_search(Request $request)
    {
        $data = $request->all();
        $rules = [
             'user_id' => 'required',
            'device_type' => 'required',
            'user_id' => 'required',
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
            $user_id= $request->user_id;
            $audition_type=$request->audition_type;

            $sql = "SELECT post_auditions.*,categories.category_name FROM post_auditions LEFT JOIN categories ON categories.id = post_auditions.category WHERE status = '1'  AND audition_type = '$audition_type'";
            $where = '';
            if(!empty($data['photography_type'])){ 
                $photographytype = $data['photography_type'];
            $where  .= " AND photography_type = '$photographytype'";
            }
            if(!empty($data['skills'])){ 
                $skills = $data['skills'];
            $where .= " AND skills REGEXP CONCAT('(^|,)(', REPLACE('$skills', ',', '|'), ')(,|$)')";
            }
            if(!empty($data['location'])){ 
                $location = $data['location'];
            $where .= " AND location = '$location'";
            }
            if(!empty($data['age_group'])){ 
                $age_group = $data['age_group'];
            $where .= " AND age_group = '$age_group'";
            }
            
           
			
			$order = ' ORDER BY post_auditions.pin_to_top DESC, post_auditions.id DESC';
            
            $completequery = $sql." ".$where." ".$order;
			
            $postaudition =   DB::select("$completequery");
			
			 
            $postauditiondata = array();
            foreach($postaudition as $postauditionval){
                
                $photos = DB::table('post_auditions_photographies') 
					->select('post_auditions_photographies.*')
					->where('post_auditions_photographies.post_auditions_id', $postauditionval->id)
					->get();
				
                $applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $data['user_id'])->first();

                $promember = DB::table('users')->select('pro_member')
                ->where('id', $postauditionval->user_id)
                ->where('pro_member', '1')
                ->first();
                
                $checkapply = false;
                if(!empty($applied)){
                    $checkapply = true;
                }
                $promember = DB::table('users')->select('pro_member')
                ->where('id', $postauditionval->user_id)
                ->where('pro_member', '1')
                ->first();
                $checkpromember = false;
                if(!empty($promember)){
                    $checkpromember = true;
                }
                
                //get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
						->where("user_id", $data['user_id'])  
						->count();
				
				$is_fav = '0'; 		
				
				if($fav > 0){
					$is_fav = '1';   
				}
				
				$time_ago = $this->timeago($postauditionval->created_at, true);
				
                $postauditiondata[] = array
                (
    			  "id"=> $postauditionval->id,  
    			  "user_id"=> $postauditionval->user_id,  
    			  "audition_title"=> $postauditionval->audition_title,  
    			  "audition_type"=> $postauditionval->audition_type,  
    			  "category"=> $postauditionval->category,  
    			  "expire_date"=> $postauditionval->expire_date,  
    			  "gender"=> $postauditionval->gender,  
    			  "union_type"=> $postauditionval->union_type,  
    			  "photography_type"=> $postauditionval->photography_type,  
    			  //"roles"=> $postauditionval->roles,  
    			  "age_group"=> $postauditionval->age_group,  
    			  "location"=> $postauditionval->location,  
    			  "production_description"=> $postauditionval->production_description,  
    			  "skills"=> $postauditionval->skills,  
    			  "status"=> $postauditionval->status, 
				  "studio_name"=> $postauditionval->studio_name,
				  "compensation"=> $postauditionval->compensation,
				  "compensation_description"=> $postauditionval->compensation_description,
				  "production_website"=> $postauditionval->production_website, 
				  "audition_location"=> $postauditionval->audition_location,
    			  "applied"=> $checkapply,
    			  "pro_member"=> $checkpromember,
    			  "category_name"=> $postauditionval->category_name,
				  "photography_image"=> $postauditionval->photography_image,
				  "photography_images_all"=> $photos,
				  "pin_to_top"=> $postauditionval->pin_to_top,
				  "created_at"=> $postauditionval->created_at,
				  "ethnicities"=>$postauditionval->ethnicities,
				  "is_fav"=> $is_fav, 
				  "time_ago"=> $time_ago, 
                ); 
            }
            
            $postaudition =   DB::select("$completequery");
            
			$response['status'] = "true";
			$response['message'] = "Search Result";
			$response['data'] = $postauditiondata;
			return response()->json($response);
        }
    }

    
    public function get_ext_casting_applied(Request $request)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'device_type' => 'required',
            //'device_token' => 'required',
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }
        else
        {
             //$user_id= $request->user_id;
            $postaudition = DB::table('post_audition_participants')
            ->join('post_auditions', 'post_audition_participants.post_audition_id', '=', 'post_auditions.id') 
            ->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
            //->leftJoin('roles', 'post_audition_participants.role_id', '=', 'roles.id')
            //->select('post_auditions.*', 'categories.category_name','roles.role_title as roles')
            ->select('post_auditions.*', 'categories.category_name')
            ->where('post_audition_participants.user_id', $data['user_id'])
            ->where('post_audition_participants.audition_type', '3')
            ->where('post_auditions.status', '1') 
			->orderBy('post_auditions.pin_to_top', 'DESC')
			->orderBy('post_auditions.id', 'DESC')
            //->groupBy("post_audition_participants.post_audition_id")
            ->get();
			
			 
            $postauditiondata = array();
            foreach($postaudition as $postauditionval){
				
				
				$rolesData = array();
				
				$audition_type = $postauditionval->audition_type;
			 
				$participants = array();

				if($audition_type == '2'){
					$participants = DB::table('post_audition_participants')
					->join('users', 'post_audition_participants.user_id', '=', 'users.id')
					//->leftJoin('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
					//->join('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
					->select('post_audition_participants.*', 'users.*')
					->where('post_audition_id', $postauditionval->id)
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
								
									$applieds = Auditionparticipant::where(["post_audition_id"=> $postauditionval->id,'role_id'=>$resrole->id])->where("user_id", $data['user_id'])->count();
									$checkapplys = false;
									if($applieds > 0){
										$checkapplys = true;
									}
									$resrole->applies = $checkapplys;
									
									// get all participants applied to this audition and role
									
									$participants = DB::table('post_audition_participants') 
										->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id') 
										->select('users.name', 'users.image','users.age','users.height','users.email','users.phone','users.pro_member','users.status')
										->where('post_audition_participants.role_id', $resrole->id)
										->where('post_audition_participants.post_audition_id', $postauditionval->id) 
										->get();
										
									$resrole->participants = $participants; 
									 
									$rolesData[] = $resrole;
										
								}
							}else{
								$resrole = Role::where("roles.role_title", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*','role_types.title as role_type')
								->first();
								if(!empty($resrole)){
									
									$applieds = Auditionparticipant::where(["post_audition_id"=> $postauditionval->id,'role_id'=>$resrole->id])->where("user_id", $data['user_id'])->count();
									$checkapplys = false;
									if($applieds > 0){
										$checkapplys = true;
									}
									$resrole->applies = $checkapplys;
									
									// get all participants applied to this audition and role
									
									$participants = DB::table('post_audition_participants') 
										->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id') 
										->select('users.name', 'users.image','users.age','users.height','users.email','users.phone','users.pro_member','users.status')
										->where('post_audition_participants.role_id', $resrole->id)
										->where('post_audition_participants.post_audition_id', $postauditionval->id) 
										->get();
										 
									$resrole->participants = $participants; 
									
									$rolesData[] = $resrole;
								}
							}
						}
					}
				}
				
                 
                $applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $data['user_id'])->first();
                $checkapply = false;
                if(!empty($applied)){
                    $checkapply = true;
                }
                $promember = DB::table('users')->select('pro_member')
                ->where('id', $postauditionval->user_id)
                ->where('pro_member', '1')
                ->first();
                $checkpromember = false;
                if(!empty($promember)){
                    $checkpromember = true;
                }
                
                //get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
						->where("user_id", $data['user_id'])  
						->count();

				$is_fav = '0'; 		

				if($fav > 0){
					$is_fav = '1';   
				}
                
				$time_ago = $this->timeago($postauditionval->created_at, true);
				
                $postauditiondata[] = array 
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
				  "pin_to_top"=> $postauditionval->pin_to_top,
				  "created_at"=> $postauditionval->created_at,
    			  "applied"=> $checkapply,
    			  "pro_member"=> $checkpromember,
				  "roles"=> $rolesData, 				  
				  "participants"=> $participants, 
				  "is_fav"=> $is_fav, 	
				  "time_ago"=> $time_ago, 	
                );
                
            }
            
			$response['status'] = "true";
			$response['message'] = "Post Audition Applied List";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
    }

    public function timeago($date) {
        $timestamp = strtotime($date);	
        
        $strTime = array("second", "minute", "hour", "day", "month", "year");
        $length = array("60","60","24","30","12","10");
 
        $currentTime = time();
        if($currentTime >= $timestamp) {
             $diff     = time()- $timestamp;
             for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
             $diff = $diff / $length[$i];
             }
 
             $diff = round($diff);
             return $diff . " " . $strTime[$i] . "(s) ago ";
        }
     }


     function save_standard_verification(Request $request)
     {

        $data = $request->all();
        $rules = [
            'user_id' => 'required',
		];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }


        if($request->file('document')){
            $file = $request->file('document');
            $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/verification/',  $name);
            $data['document'] =  $name;
         }
         if($request->file('front_photo')){
            $file = $request->file('front_photo');
            $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/verification/',  $name);
            $data['front_photo'] =  $name;
         }
         if($request->file('back_photo')){
            $file = $request->file('back_photo');
            $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/verification/',  $name);
            $data['back_photo'] =  $name;
         }
         if($request->file('real_time_photo')){
            $file = $request->file('real_time_photo');
            $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/verification/',  $name);
            $data['real_time_photo'] =  $name;
         }
         if($request->file('resume')){
            $file = $request->file('resume');
            $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
            $file->move(public_path().'/assets/verification/',  $name);
            $data['resume'] =  $name;
         }
         $data['status'] =  4;
        //  if($request->file('portfolio')){
        //     $file = $request->file('portfolio');
        //     $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName()); 
        //     $file->move(public_path().'/assets/verification/',  $name);
        //     $data['portfolio'] =  $name;
        //  }

         $res=DB::table("user_verification")->where('type','standard')->where('user_id',$request->user_id)->first();
         if(count((array)$res)>0)
         {
            DB::table("user_verification")->where('type','standard')->where('user_id',$request->user_id)->update($data);
            $response['status'] = "true";
            $response['message'] ="updated Successfully";
         }
         else
         {
            DB::table("user_verification")->insert($data);
            $response['status'] = "true";
            $response['message'] ="Inserted Successfully";
         }
         return response()->json($response);


     }

     function getStandardVerification(Request $request) {
        $rules = ['user_id' => 'required'];
        $response['status'] = "false";

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $result = DB::table("user_verification")->where('type','standard')->where('user_id',$request->user_id)->first();
        if(count((array)$result)>0) {
            $response['status'] = "true";
            $response['data'] = $result;
            $response['message'] = "Standard verification informations.";
        }else {
            $response['message'] = "You don't have any Standard verification information.";
        }
        return response()->json($response);
     }

     function save_company_verification(Request $request)
     {

        $data = $request->all();
        $rules = [
            'user_id' => 'required',
		];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }


        if($request->file('document')){
            $file = $request->file('document');
            $name= 'public/assets/verification/'.time(). str_replace( " ", "_",$file->getClientOriginalName());
            $file->move(public_path().'/assets/verification/',  $name);
            $data['document'] =  $name;
         }
         $data['status'] =  4;

         $res=DB::table("user_verification")->where('type','company')->where('user_id',$request->user_id)->first();
         if(count((array)$res)>0)
         {
            DB::table("user_verification")->where('type','company')->where('user_id',$request->user_id)->update($data);
            $response['status'] = "true";
            $response['message'] ="updated Successfully";
         }
         else
         {
            DB::table("user_verification")->insert($data);
            $response['status'] = "true";
            $response['message'] ="Inserted Successfully";
         }
         return response()->json($response);


     }

     function getCompanyVerification(Request $request) {
        $rules = ['user_id' => 'required'];
        $response['status'] = "false";

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $result = DB::table("user_verification")->where('type','company')->where('user_id',$request->user_id)->first();
        if(count((array)$result)>0) {
            $response['status'] = "true";
            $response['data'] = $result;
            $response['message'] = "Company verification informations.";
        }else {
            $response['message'] = "You don't have any Company verification information.";
        }
        return response()->json($response);
     }

    function check_verification(Request $request) {
        $res=DB::table("user_verification")->select('type','status')->where('user_id',$request->user_id)->get();
        if(count((array)$res) > 0) {
            $response['status'] = "true";
            $response['message'] ="list of verification";
            $response['data'] =$res;
            return response()->json($response);
        }  else  {
            $response['status'] = "false";
            $response['message'] ="no data";
            $response['data'] =[];
            return response()->json($response);
        }

     }




}

