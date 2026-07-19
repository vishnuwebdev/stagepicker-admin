<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Auditionparticipant;
use App\Models\Skill;
use App\Models\Role;
use App\Models\Report;
use App\Models\CrewRole;
use App\Models\Career;
use App\Models\RoleType;
use App\Models\Postaudition;
use App\Models\ProductionCrew;
use App\Models\AuditionView;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use \Session;
use Socialite;
use App\Models\Mail as Mail;
use Illuminate\Support\Facades\Auth;
use App\FireStore\FireStoreApiClient;
use App\FireStore\FireStoreDocument;
use App\Models\Favourite;
use Carbon\Carbon;
use DB;
use App\Services\FCMService;


class PostauditionController extends ApiController
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


	function sendUserNotification($data)
	{
		$fcmService = new FCMService();

		$result = $fcmService->sendNotification(
			$data['fcmToken'], // FCM Token
			$data['title'],           // Title
			$data['message'],      // Body
			$data['customData']  // Custom Data Array
		);

		return response()->json($result);
	}

	function saveNotification($request)
	{
		if(empty($request['user_id'])){
			return false;
		}
		$data = [
			'user_id' => $request['user_id'],
			'title' => $request['title'],
			'body' => $request['message'],
			'type' => 'message',
			'ref_id' => $request['ref_id'] ?? null,
			'is_read' => $request['is_read'] ?? false,
			'custom_data' => $request['custom_data'] ?? null,
		];


		DB::table("notifications")->insert($data);
	}

	function response($data)
	{
		//return response()->json($this->arrayHandleFun($data),200);
		$data = $this->arrayHandleFun($data);
		// This header pass for allow origin control.
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Origin: http://demo2server.com/petjournal/api");
		header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');
		header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization, X-CSRF-Token');
		header('Access-Control-Allow-Credentials: true');
		echo json_encode($data);
		die();
	}

	function validationHandle($validation)
	{
		foreach ($validation->getMessages() as $field_name => $messages) {
			if (!isset($firstError)) {
				$firstError = $messages[0];
				$error[$field_name] = $messages[0];
			}
		}
		return $firstError;
	}

	function arrayHandleFun($array, $isRepeat = false)
	{

		foreach ($array as $key => $value) {
			if ($value === null) {
				$array[$key] = "";
			} else if (is_array($value)) {
				if (empty($value)) {
					//$array[$key] = "";

				} else {
					$array[$key] = self::arrayHandleFun($value);
				}
			}
		}
		if (!$isRepeat) {
			$array = self::arrayHandleFun($array, true);
		}
		return $array;
	}

	
	public function searchData(Request $request)
	{
		$data = $request->all();
		$rules = [
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$skills = Skill::orderBy('id', 'DESC')->get();
			$role = Role::orderBy('id', 'DESC')->get();
			$category = Category::whereNull('type')->orWhere('type', '!=', '1')->orderBy('id', 'DESC')->get();
			$crew_category = Category::where('type', '=', '1')->orderBy('id', 'DESC')->get();
			$careers = Career::orderBy('id', 'DESC')->get();
			//$roletype = RoleType::where(['status'=>1])->where(['type'=>0])->orderBy('id', 'DESC')->get();
			$roletype = DB::table('role_type_list')->where(['status' => 1])->where(['type' => 1])->orderBy('id', 'DESC')->get();
			$pcroletype = DB::table('role_type_list')->where(['status' => 1])->where(['type' => 2])->orderBy('id', 'DESC')->get();
			$ethnicities = DB::table('ethnicities')->where(['status' => 1])->orderBy('id', 'DESC')->get();


			$response['status'] = "true";
			$response['message'] = "Search Data";
			$response['data']['role'] = $role;
			$response['data']['skills'] = $skills;
			$response['data']['category'] = $category;
			$response['data']['crew_category'] = $crew_category;
			$response['data']['careers'] = $careers;
			$response['data']['roletype'] = $roletype;
			$response['data']['pcroletype'] = $pcroletype;
			$response['data']['ethnicities'] = $ethnicities;

			return response()->json($response);
		}
	}

	public function dashboardData(Request $request){
		$data = $request->all();
		$date = Carbon::now();
		$formatted = $date->format('Y-m-d H:i:s');
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postaudition1 = DB::table('post_auditions')
				->join('categories', 'post_auditions.category', '=', 'categories.id')
				->join('users', 'post_auditions.user_id', '=', 'users.id')
				->leftJoin('post_auditions_photographies', 'post_auditions_photographies.post_auditions_id', '=', 'post_auditions.id')
				->select('post_auditions.*', 'categories.category_name', DB::raw('users.name as pruducerName'), DB::raw('users.image as producerImage'), DB::raw('GROUP_CONCAT(post_auditions_photographies.image) as photography_images'))
				->where('post_auditions.status', '1')
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				->whereIn('post_auditions.audition_type', [1, 2])
				->orderBy('post_auditions.pin_to_top', 'DESC')
				->orderBy('post_auditions.id', 'DESC')
				->groupBy('post_auditions.id')
				->limit(10)->get();


				


				// photography_image" => $safePostAudition->photography_image,
				// 		"photography_images_all" => $photos,

			//       $postaudition1 = DB::table('post_auditions')
			//       ->join('categories', 'post_auditions.category', '=', 'categories.id')
			//       ->select('post_auditions.*', 'categories.category_name'
			//  //DB::raw("GROUP_CONCAT(roles.role_title) as roles")
			// )
			// //->leftjoin("roles",\DB::raw("FIND_IN_SET(roles.id,post_auditions.roles)"),">",\DB::raw("'0'"))
			//       ->where('post_auditions.status', '1')
			// 			->whereRaw("STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= ?", [$date])
			// ->whereIn('post_auditions.audition_type',[1,2])
			//       ->orderBy('post_auditions.pin_to_top', 'DESC')
			// ->orderBy('post_auditions.id', 'DESC')
			//       ->groupBy("post_auditions.id")
			//       ->limit(10)
			//       ->get();

			$postaudition2 = DB::table('post_auditions')
				->select('post_auditions.*',DB::raw('GROUP_CONCAT(post_auditions_photographies.image) as photography_images'))
					->leftJoin('post_auditions_photographies', 'post_auditions_photographies.post_auditions_id', '=', 'post_auditions.id')
				//->leftjoin("roles",\DB::raw("FIND_IN_SET(roles.id,post_auditions.roles)"),">",\DB::raw("'0'"))
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				
				->where('post_auditions.status', '1')
				->where('post_auditions.audition_type', 3)
				->orderBy('post_auditions.pin_to_top', 'DESC')
				->orderBy('post_auditions.id', 'DESC')
				->groupBy("post_auditions.id")
				->limit(10)
				->get();
			$postaudition = $postaudition1->merge($postaudition2);
			$postauditiondata = array();
			foreach ($postaudition as $postauditionval) {

				//get role data 
				$roles = $postauditionval->roles;
				$allroles = explode(',', $roles);
				$rolesData = array();

				if (!empty($allroles)) {
					foreach ($allroles as $rol) {
						$rol = trim($rol);
						if (is_numeric($rol)) {
							$resrole = Role::where("roles.id", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {
								$rolesData[] = $resrole;
							}
						} else {
							$resrole = Role::where("roles.role_title", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {
								$rolesData[] = $resrole;
							}
						}
					}
				}


				$applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $data['user_id'])->first();
				$checkapply = false;
				if (!empty($applied)) {
					$checkapply = true;
				}
				$promember = DB::table('users')->select('pro_member')
					->where('id', $postauditionval->user_id)
					->where('pro_member', '1')
					->first();
				$checkpromember = false;
				if (!empty($promember)) {
					$checkpromember = true;
				}

				//get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
					->where("user_id", $data['user_id'])
					->count();

				$is_fav = '0';

				if ($fav > 0) {
					$is_fav = '1';
				}

				$time_ago = $this->timeago($postauditionval->created_at, true);

				$postauditiondata[] = array
				(
					"id" => $postauditionval->id,
					"user_id" => $postauditionval->user_id,
					"producer_name" => $postauditionval->pruducerName ?? '',
					"producer_image" => $postauditionval->producerImage ?? '',
					"audition_title" => $postauditionval->audition_title,
					"category" => $postauditionval->category,
					"expire_date" => $postauditionval->expire_date,
					"gender" => $postauditionval->gender,
					"union_type" => $postauditionval->union_type,
					"audition_type" => $postauditionval->audition_type,
					"age_group" => $postauditionval->age_group,
					"location" => $postauditionval->location,
					"photography_type" => $postauditionval->photography_type,
					"photography_image" => $postauditionval->photography_image,
					"production_description" => $postauditionval->production_description,
					"skills" => $postauditionval->skills,
					"status" => $postauditionval->status,
					"category_name" => @$postauditionval->category_name,
					"studio_name" => $postauditionval->studio_name,
					"compensation" => $postauditionval->compensation,
					"compensation_description" => $postauditionval->compensation_description,
					"production_website" => $postauditionval->production_website,
					"audition_location" => $postauditionval->audition_location,
					"created_at" => $postauditionval->created_at,
					"applied" => $checkapply,
					"pro_member" => $checkpromember,
					"pin_to_top" => $postauditionval->pin_to_top,
					"roles" => $rolesData,
					"is_fav" => $is_fav,
					"time_ago" => $time_ago,
					"photography_images" => $postauditionval->photography_images ?? '',
				);

			}

			$crewRecords = DB::table('production_crews')
				->join('categories', 'production_crews.category', '=', 'categories.id')
				->leftJoin('post_auditions_photographies', 'post_auditions_photographies.post_auditions_id', '=', 'production_crews.id')
				->select('production_crews.*', 'categories.category_name', DB::raw('GROUP_CONCAT(post_auditions_photographies.image) as photography_images'))
				->where('production_crews.status', '1')
				->orderBy('production_crews.pin_to_top', 'DESC')
				->orderBy('production_crews.id', 'DESC')
				->groupBy('production_crews.id')
				->limit(15)
				->get();

			$productionCrewData = array();

			foreach ($crewRecords as $crewVal) {
				$roles = $crewVal->roles;
				$allroles = explode(',', $roles);
				$rolesData = array();

				if (!empty($allroles)) {
					foreach ($allroles as $rol) {
						$rol = trim($rol);
						$resrole = CrewRole::where('crew_roles.id', $rol)
							->leftJoin('role_types', 'crew_roles.role_type', '=', 'role_types.id')
							->leftJoin('categories', 'crew_roles.category', '=', 'categories.id')
							->select('crew_roles.*', 'role_types.title as role_type', 'categories.category_name as category')
							->first();
						if (!empty($resrole)) {
							$rolesData[] = $resrole;
						}
					}
				}

				$fav = Favourite::where('post_audition_id', $crewVal->id)
					->where('user_id', $data['user_id'])
					->first();
				$is_fav = !empty($fav) ? '1' : '0';

				$time_ago = $this->timeago($crewVal->created_at, true);

				$productionCrewData[] = array(
					"id"                       => $crewVal->id,
					"user_id"                  => $crewVal->user_id,
					"title"                    => $crewVal->title,
					"category"                 => $crewVal->category,
					"expire_date"              => date("Y-m-d H:i A", strtotime($crewVal->expire_date)),
					"roles"                    => $rolesData,
					"union_type"               => $crewVal->union_type,
					"location"                 => $crewVal->location,
					"description"              => $crewVal->description,
					"skills"                   => $crewVal->skills,
					"status"                   => $crewVal->status,
					"compensation"             => $crewVal->compensation,
					"compensation_description" => $crewVal->compensation_description,
					"website_url"              => $crewVal->website_url,
					"category_name"            => $crewVal->category_name,
					"photography_images"       => $crewVal->photography_images ?? '',
					"created_at"               => $crewVal->created_at,
					"is_fav"                   => $is_fav,
					"time_ago"                 => $time_ago,
				);
			}

			$sliders = DB::table('sliders')
				->select('sliders.*')
				->where('status', '1')
				->get();
			$response['status'] = "true";
			$response['message'] = "Post Audition List";
			$response['data'] = $postauditiondata;
			$response['production_crew_data'] = $productionCrewData;
			$response['sliders'] = $sliders;
			return response()->json($response);
		}
	}

	public function list(Request $request)
	{
		$data = $request->all();
		$date = Carbon::now();
		$formatted = $date->format('Y-m-d H:i:s');
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postaudition = DB::table('post_auditions')
				->join('categories', 'post_auditions.category', '=', 'categories.id')
				->select('post_auditions.*', 'categories.category_name', DB::raw('GROUP_CONCAT(post_auditions_photographies.image) as photography_images'))
				//->leftjoin("roles",\DB::raw("FIND_IN_SET(roles.id,post_auditions.roles)"),">",\DB::raw("'0'"))
				->leftJoin('post_auditions_photographies', 'post_auditions_photographies.post_auditions_id', '=', 'post_auditions.id')


				->where('post_auditions.user_id', $data['user_id'])
				->where('post_auditions.status', '1')
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				->where('post_auditions.audition_type', '1')
				->orderBy('post_auditions.pin_to_top', 'DESC')
				->orderBy('post_auditions.id', 'DESC')
				->groupBy("post_auditions.id")
				->limit(5)
				->get();

			$postauditiondata = array();

			if (!empty($postaudition)) {
				foreach ($postaudition as $postauditionval) {

					//get role data 
					$roles = $postauditionval->roles;
					$allroles = explode(',', $roles);
					$rolesData = array();

					if (!empty($allroles)) {
						foreach ($allroles as $rol) {
							$rol = trim($rol);
							if (is_numeric($rol)) {
								$resrole = Role::where("roles.id", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();

								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							} else {
								$resrole = Role::where("roles.role_title", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();
								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							}
						}
					}

					//get fav data
					$fav = Favourite::where("post_audition_id", $postauditionval->id)
						->where("user_id", $data['user_id'])
						->first();

					$is_fav = '0';

					if (!empty($fav)) {
						$is_fav = '1';
					}

					$time_ago = $this->timeago($postauditionval->created_at, true);

					$postauditiondata[] = array(
						"id" => $postauditionval->id,
						"user_id" => $postauditionval->user_id,
						"audition_title" => $postauditionval->audition_title,
						"category" => $postauditionval->category,
						"expire_date" => $postauditionval->expire_date,
						"gender" => $postauditionval->gender,
						"roles" => $rolesData,
						"union_type" => $postauditionval->union_type,
						"audition_type" => $postauditionval->audition_type,
						"age_group" => $postauditionval->age_group,
						"location" => $postauditionval->location,
						"photography_type" => $postauditionval->photography_type,
						"photography_image" => $postauditionval->photography_image,
						"photography_images" => $postauditionval->photography_images ?? '',
						"production_description" => $postauditionval->production_description,
						"skills" => $postauditionval->skills,
						"status" => $postauditionval->status,
						"studio_name" => $postauditionval->studio_name,
						"compensation" => $postauditionval->compensation,
						"compensation_description" => $postauditionval->compensation_description,
						"production_website" => $postauditionval->production_website,
						"audition_location" => $postauditionval->audition_location,
						"category_name" => $postauditionval->category_name,
						"created_at" => $postauditionval->created_at,
						"pin_to_top" => $postauditionval->pin_to_top,
						"is_fav" => $is_fav,
						"fav_data" => $fav,
						"time_ago" => $time_ago
					);
				}
			}

			$response['status'] = "true";
			$response['message'] = "Post Audition List";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}

	public function producerAuditionDetail(Request $request)
	{
		$data = $request->all();
		$date = Carbon::now();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			'audition_id' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postaudition = DB::table('post_auditions')
				->join('categories', 'post_auditions.category', '=', 'categories.id')
				->select('post_auditions.*', 'categories.category_name')
				->where('post_auditions.user_id', $data['user_id'])
				->where("post_auditions.id", $data['audition_id'])
				->first();

			$postauditiondata = null;

			if (!empty($postaudition)) {
					$roles = $postaudition->roles;
					$allroles = explode(',', $roles);
					$rolesData = array();

					if (!empty($allroles)) {
						foreach ($allroles as $rol) {
							$rol = trim($rol);
							if (is_numeric($rol)) {
								$resrole = Role::where("roles.id", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();

								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							} else {
								$resrole = Role::where("roles.role_title", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();
								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							}
						}
					}

					//get fav data
					$fav = Favourite::where("post_audition_id", $postaudition->id)
						->where("user_id", $data['user_id'])
						->first();

					$is_fav = '0';

					if (!empty($fav)) {
						$is_fav = '1';
					}

					$time_ago = $this->timeago($postaudition->created_at, true);

					$postauditiondata[] = array(
						"id" => $postaudition->id,
						"user_id" => $postaudition->user_id,
						"audition_title" => $postaudition->audition_title,
						"category" => $postaudition->category,
						"expire_date" => $postaudition->expire_date,
						"gender" => $postaudition->gender,
						"roles" => $rolesData,
						"union_type" => $postaudition->union_type,
						"audition_type" => $postaudition->audition_type,
						"age_group" => $postaudition->age_group,
						"location" => $postaudition->location,
						"photography_type" => $postaudition->photography_type,
						"photography_image" => $postaudition->photography_image,
						"production_description" => $postaudition->production_description,
						"skills" => $postaudition->skills,
						"status" => $postaudition->status,
						"studio_name" => $postaudition->studio_name,
						"compensation" => $postaudition->compensation,
						"compensation_description" => $postaudition->compensation_description,
						"production_website" => $postaudition->production_website,
						"audition_location" => $postaudition->audition_location,
						"category_name" => $postaudition->category_name,
						"created_at" => $postaudition->created_at,
						"pin_to_top" => $postaudition->pin_to_top,
						"is_fav" => $is_fav,
						"fav_data" => $fav,
						"time_ago" => $time_ago
					);
			}
		}

		$response['status'] = "true";
		$response['message'] = "Producer Audition Detail";
		$response['data'] = $postauditiondata;
		return response()->json($response);
	}

	public function auditionDetail(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'user_id' => 'required',
			'audition_id' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postaudition = DB::table('post_auditions')
				->join('categories', 'post_auditions.category', '=', 'categories.id')
				->select('post_auditions.*', 'categories.category_name')
				->where('post_auditions.status', '1')
				->where('post_auditions.id', $data['audition_id'])
				->first();
			$postauditiondata =	null;
			if (!empty($postaudition)) {
				
					$safePostAudition = optional($postaudition);
					//get role data 
					$roles = $safePostAudition->roles;
					$allroles = explode(',', $roles);
					$rolesData = array();

					if (!empty($allroles)) {
						foreach ($allroles as $rol) {
							$rol = trim($rol);
							if (is_numeric($rol)) {
								$resrole = Role::where("roles.id", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();

								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							} else {
								$resrole = Role::where("roles.role_title", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();
								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							}
						}
					}

					$photos = DB::table('post_auditions_photographies')
					->select('post_auditions_photographies.*')
					->where('post_auditions_photographies.post_auditions_id', $safePostAudition->id)
					->get();

					//get fav data
					$fav = Favourite::where("post_audition_id", $safePostAudition->id)
						->where("user_id", $data['user_id'])
						->first();

					$is_fav = !empty($fav) ? 1 :  '0';
					$time_ago = $this->timeago($safePostAudition->created_at, true);

					$postauditiondata[] = array(
						"id" => $safePostAudition->id,
						"user_id" => $safePostAudition->user_id,
						"audition_title" => $safePostAudition->audition_title,
						"category" => $safePostAudition->category,
						"expire_date" => $safePostAudition->expire_date,
						"gender" => $safePostAudition->gender,
						"roles" => $rolesData,
						"union_type" => $safePostAudition->union_type,
						"audition_type" => $safePostAudition->audition_type,
						"age_group" => $safePostAudition->age_group,
						"location" => $safePostAudition->location,
						"photography_type" => $safePostAudition->photography_type,
						"photography_image" => $safePostAudition->photography_image,
						"photography_images_all" => $photos,
						"production_description" => $safePostAudition->production_description,
						"skills" => $safePostAudition->skills,
						"status" => $safePostAudition->status,
						"studio_name" => $safePostAudition->studio_name,
						"compensation" => $safePostAudition->compensation,
						"compensation_description" => $safePostAudition->compensation_description,
						"production_website" => $safePostAudition->production_website,
						"audition_location" => $safePostAudition->audition_location,
						"category_name" => $safePostAudition->category_name,
						"created_at" => $safePostAudition->created_at,
						"pin_to_top" => $safePostAudition->pin_to_top,
						"is_fav" => $is_fav,
						"fav_data" => $fav,
						"time_ago" => $time_ago
					);
	
			}

			$response['status'] = "true";
			$response['message'] = "Audition Detail";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}


	public function auditionfeedlist(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$sql = "SELECT post_auditions.*,categories.category_name FROM post_auditions INNER JOIN categories ON post_auditions.category=categories.id WHERE post_auditions.status = '1' AND post_auditions.audition_type = '1'";
			$where = '';
			if (!empty($data['skills'])) {
				$skills = $data['skills'];
				$where .= " AND skills REGEXP CONCAT('(^|,)(', REPLACE('$skills', ',', '|'), ')(,|$)')";
			}

			if (!empty($data['location'])) {
				$location = $data['location'];
				$where .= " AND location = '$location'";
			}
			if (!empty($data['gender'])) {
				$gender = $data['gender'];
				$where .= " AND gender = '$gender'";
			}
			if (!empty($data['category'])) {
				$category = $data['category'];
				$where .= " AND category = '$category'";
			}
			if (!empty($data['role'])) {
				$role = $data['role'];
				//$where .= " AND roles = '$role'";
				$where .= " AND FIND_IN_SET($role,roles)";
			}
			if (!empty($data['union_type'])) {
				$union_type = $data['union_type'];
				$where .= " AND union_type = '$union_type'";
			}
			if (!empty($data['age_group'])) {
				$age_group = $data['age_group'];
				$where .= " AND age_group = '$age_group'";
			}

			$order = ' ORDER BY post_auditions.pin_to_top DESC, post_auditions.id DESC';

			$completequery = $sql . " " . $where . " " . $order;

			$postaudition = DB::select("$completequery");

			$postauditiondata = array();
			foreach ($postaudition as $postauditionval) {

				$roles = $postauditionval->roles;
				$allroles = explode(',', $roles);
				$rolesData = array();

				if (!empty($allroles)) {
					foreach ($allroles as $rol) {
						$rol = trim($rol);
						if (is_numeric($rol)) {
							$resrole = Role::where("roles.id", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {

								$applieds = Auditionparticipant::where(["post_audition_id" => $postauditionval->id, 'role_id' => $rol])->where("user_id", $data['user_id'])->count();
								$checkapplys = false;
								if ($applieds > 0) {
									$checkapplys = true;
								}
								$resrole->applies = $checkapplys;

								$rolesData[] = $resrole;

								// get all participants applied to this audition and role

								/* $participants = DB::table('post_audition_participants') 
									->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id') 
									->select('users.name', 'users.image','users.age','users.height','users.email','users.phone','users.pro_member','users.status')
									->where('post_audition_participants.role_id', $resrole->id)
									->where('post_audition_participants.post_audition_id', $postauditionval->id) 
									->get();

								$resrole->participants = $participants; */

							}
						} else {
							$resrole = Role::where("roles.role_title", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {


								$applieds = Auditionparticipant::where(["post_audition_id" => $postauditionval->id, 'role_id' => $resrole->id])->where("user_id", $data['user_id'])->count();
								$checkapplys = false;
								if ($applieds > 0) {
									$checkapplys = true;
								}
								$resrole->applies = $checkapplys;

								$rolesData[] = $resrole;

								// get all participants applied to this audition and role

								/* $participants = DB::table('post_audition_participants') 
									->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id') 
									->select('users.name', 'users.image','users.age','users.height','users.email','users.phone','users.pro_member','users.status')
									->where('post_audition_participants.role_id', $resrole->id)
									->where('post_audition_participants.post_audition_id', $postauditionval->id) 
									->get();

								$resrole->participants = $participants; */
							}
						}
					}
				}

				$applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $data['user_id'])->first();
				$checkapply = false;
				if (!empty($applied)) {
					$checkapply = true;
				}
				$promember = DB::table('users')->select('pro_member')
					->where('id', $postauditionval->user_id)
					->where('pro_member', '1')
					->first();
				$checkpromember = false;
				if (!empty($promember)) {
					$checkpromember = true;
				}

				//get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
					->where("user_id", $data['user_id'])
					->count();

				$is_fav = '0';

				if ($fav > 0) {
					$is_fav = '1';
				}

				$time_ago = $this->timeago($postauditionval->created_at, true);

				$postauditiondata[] = array
				(
					"id" => $postauditionval->id,
					"user_id" => $postauditionval->user_id,
					"audition_title" => $postauditionval->audition_title,
					"category" => $postauditionval->category,
					"expire_date" => $postauditionval->expire_date,
					"gender" => $postauditionval->gender,
					"roles" => $rolesData,
					"union_type" => $postauditionval->union_type,
					"age_group" => $postauditionval->age_group,
					"location" => $postauditionval->location,
					"production_description" => $postauditionval->production_description,
					"skills" => $postauditionval->skills,
					"status" => $postauditionval->status,
					"category_name" => $postauditionval->category_name,
					"studio_name" => $postauditionval->studio_name,
					"compensation" => $postauditionval->compensation,
					"compensation_description" => $postauditionval->compensation_description,
					"production_website" => $postauditionval->production_website,
					"audition_location" => $postauditionval->audition_location,
					"pin_to_top" => $postauditionval->pin_to_top,
					"created_at" => $postauditionval->created_at,
					"applied" => $checkapply,
					"pro_member" => $checkpromember,
					"is_fav" => $is_fav,
					"time_ago" => $time_ago,
					"ethnicities" => $postauditionval->ethnicities,

				);

			}

			$response['status'] = "true";
			$response['message'] = "Search Result";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}

	public function auditionfeedlistbackup(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$users = User::find($data['user_id']);
			$userskill = $users['skills'];
			//$postaudition =   DB::select("SELECT post_auditions.*,categories.category_name FROM post_auditions INNER JOIN categories ON post_auditions.category=categories.id WHERE skills REGEXP CONCAT('(^|,)(', REPLACE('$userskill', ',', '|'), ')(,|$)') AND status = '1'");


			$postaudition = DB::select("SELECT post_auditions.*,categories.category_name FROM post_auditions INNER JOIN categories ON post_auditions.category=categories.id WHERE status = '1'");

			$postauditiondata = array();

			foreach ($postaudition as $postauditionval) {



				$checkapply = false;
				$applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $data['user_id'])->first();

				if (!empty($applied)) {
					$checkapply = true;
				}

				$postauditiondata[] = array
				(
					"id" => $postauditionval->id,
					"user_id" => $postauditionval->user_id,
					"audition_title" => $postauditionval->audition_title,
					"category" => $postauditionval->category,
					"expire_date" => $postauditionval->expire_date,
					"roles" => $postauditionval->roles,
					"age_group" => $postauditionval->age_group,
					"location" => $postauditionval->location,
					"production_description" => $postauditionval->production_description,
					"skills" => $postauditionval->skills,
					"status" => $postauditionval->status,
					"category_name" => $postauditionval->category_name,
					"applied" => $checkapply

				);
				$postaudition['applied'] = 'true';

			}

			$response['status'] = "true";
			$response['message'] = "Audition feed List";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}
	public function actormodelfeed(Request $request)
	{
		$data = $request->all();
		$rules = [
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$auditioner = User::where('user_type', '1')->where('status', '1')->get();

			$response['status'] = "true";
			$response['message'] = "Actor Model List";
			$response['data'] = $auditioner;
			return response()->json($response);
		}
	}
	public function addPostauditionData(Request $request)
	{
		$data = $request->all();
		$rules = [
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$category = Category::whereNull('type')->orWhere('type', '!=', '1')->orderBy('id', 'DESC')->get();
			$crew_category = Category::where('type', '=', '1')->orderBy('id', 'DESC')->get();

			//$crew_role = CrewRole::orderBy('id', 'DESC')->get(); 
			//$role = Role::orderBy('id', 'DESC')->get();

			$skills = Skill::orderBy('id', 'DESC')->get();
			$roletype = RoleType::where('status', '1')->whereNull('type')->orWhere('type', '!=', '1')->orderBy('id', 'DESC')->get();

			$crewroletype = RoleType::where('status', '1')->where('type', '=', '1')->orderBy('id', 'DESC')->get();
			$designation = DB::table('designation')->get();
			$response['status'] = "true";
			$response['message'] = "Post Audition Data";
			//$response['data']['role'] = $role;
			$ethnicities = DB::table('ethnicities')->where(['status' => 1])->orderBy('id', 'DESC')->get();

			$response['data']['role'] = $roletype;
			$response['data']['category'] = $category;
			$response['data']['crew_category'] = $crew_category;
			$response['data']['crew_role'] = $crewroletype;
			$response['data']['skills'] = $skills;
			$response['data']['designation'] = $designation;
			$response['data']['ethnicities'] = $ethnicities;


			return response()->json($response);
		}
	}

	public function hasAppliedForRole(Request $request)
	{
		$data = $request->all();
		$rules = [
			'device_type' => 'required',
			'user_id' => 'required',
			'role_id' => 'required',
			'post_audition_id' => 'required'
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$appliedData = Auditionparticipant::where("post_audition_id", $data['post_audition_id'])->where("user_id", $data['user_id'])->where('role_id', $data['role_id'])->first();
				
			$response['status'] = "true";
			$response['message'] = "Post Audition Data";
			$response['hasApplied'] = !empty($appliedData);
			$response['appliedId'] = $response['hasApplied'] ?  $appliedData->id : null;
			$response['isSelected'] = $response['hasApplied'] ?  $appliedData->is_selected : null;

			return response()->json($response);
		}
	}

	public function addPostaudition(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'audition_title' => 'required',
			'audition_date' => 'required',
			'audition_time' => 'required',
			//'age_group' => 'required',
			'location' => 'required',
			'production_description' => 'required',
			//'skills' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$postaudition = new Postaudition;
			$postaudition->user_id = $data['user_id'];
			;
			$postaudition->audition_title = $data['audition_title'];
			$postaudition->ethnicities = $data['ethnicities'];

			if (!empty($data['category'])) {
				$postaudition->category = $data['category'];
			} else {
				$postaudition->category = 0;
			}
			if (!empty($data['audition_type'])) {
				$postaudition->audition_type = $data['audition_type'];
			}
			if (!empty($data['photography_type'])) {
				$postaudition->photography_type = $data['photography_type'];
			}

			if (!empty($data['audition_date']) && !empty($data['audition_time'])) {
				$postaudition->expire_date = $data['audition_date'] . " " . $data['audition_time'];
			}

			if (!empty($data['gender'])) {
				$postaudition->gender = $data['gender'];
			}

			if (!empty($data['union_type'])) {
				$postaudition->union_type = $data['union_type'];
			}

			if (!empty($data['roles'])) {
				$postaudition->roles = $data['roles'];
			}

			if (!empty($data['age_group'])) {
				$postaudition->age_group = $data['age_group'];
			}

			$postaudition->location = $data['location'];
			$postaudition->status = '1';
			$postaudition->production_description = $data['production_description'];


			if (!empty($data['skills'])) {
				$postaudition->skills = $data['skills'];
			}

			if (!empty($data['studio_name'])) {
				$postaudition->studio_name = $data['studio_name'];
			}

			$postaudition->compensation = $data['compensation'] ? '1' : '0';

			if (!empty($data['compensation_description'])) {
				$postaudition->compensation_description = $data['compensation_description'];
			}

			if (!empty($data['production_website'])) {
				$postaudition->production_website = $data['production_website'];
			}

			if (!empty($data['audition_location'])) {
				$postaudition->audition_location = $data['audition_location'];
			}

			/* if(!empty($data['role_title'])){
				$role_title = $data['role_title'];

				foreach($role_title as $role){
					$created_at = date("Y-m-d H:i:s");
					$updated_at = date("Y-m-d H:i:s");

					//check if role exists
					$roleData =   DB::select("SELECT id FROM roles WHERE role_title = '". $role. "'");

					if(empty($roleData)){ 
						DB::insert('insert into roles (id, role_title,created_at,updated_at) values (?, ?, ?, ?)', [Null,$role, $created_at,$updated_at]);

					}
				}
				$postaudition->roles = implode(',',$role_title);
			} */

			if (!empty($data['roles'])) {
				$ids = array();
				foreach ($data['roles'] as $roledata) {

					if (empty($data['skills']) && !empty($roledata['skills'])) {
						$postaudition->skills = $roledata['skills'];
					}


					if (empty($data['age_group']) && !empty($roledata['age_min']) && !empty($roledata['age_max'])) {
						$postaudition->age_group = $roledata['age_min'] . '-' . $roledata['age_max'];
					}

					$object = Role::updateOrCreate($roledata);

					$lastid = $object->id;

					$ids[] = $lastid;
				}
				$postaudition->roles = implode(',', $ids);
			}


			if ($request->file('photography_image')) {
				$file = $request->file('photography_image');
				$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $file->getClientOriginalName());
				$file->move(public_path() . '/assets/photography/', $name);
				$postaudition->photography_image = $name;
			}

			//new code for multiple file upload 
			$images = array();
			if ($request->file('photo')) {
				$file = $request->file('photo');

				if (!empty($file)) {
					foreach ($file as $key => $fileVal) {
						$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $fileVal->getClientOriginalName());
						$fileVal->move(public_path() . '/assets/photography/', $name);
						if ($key < 1) {
							$postaudition->photography_image = $name;
						}
						$images[] = $name;
					}
				}
			}


			if ($postaudition->save()) {

				if (!empty($images)) {
					foreach ($images as $key => $img) {
						DB::insert('insert into post_auditions_photographies (id, post_auditions_id,image) values (?, ?, ?)', [Null, $postaudition->id, $img]);
					}
				}

				if ($postaudition->skills != '') {
					$userskill = $postaudition->skills;

					$usersbyskill = DB::select("SELECT * FROM users WHERE skills REGEXP CONCAT('(^|,)(', REPLACE('$userskill', ',', '|'), ')(,|$)') AND notification_status = '1' AND status = '1' AND user_type = '1'");

					foreach ($usersbyskill as $usersbyskillval) {

						$message = "Audition added may be interested in";
						$notification_array = array(
							'title' => 'Audition added, may be you are interested',
							'body' => $message
						);
						//FCM api URL
						$url = 'https://fcm.googleapis.com/fcm/send';

						//api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
						$server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
						$fields = array();
						$fields['notification'] = $notification_array;

						$fields['to'] = $usersbyskillval->device_token;
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


				$response['status'] = "true";
				$response['message'] = "Audition Successfully added";
				return response()->json($response);
			} else {
				$response['status'] = "false";
				$response['message'] = "Something went wrong. Please Try again.";
				return response()->json($response);
			}
		}
	}

	public function addPhotography(Request $request)
	{
		$data = $request->all();

		$rules = [
			'user_id' => 'required',
			'title' => 'required',
			'audition_date' => 'required',
			'audition_time' => 'required',
			'category' => 'required',
			'location' => 'required',
			'production_description' => 'required',
			'studio_name' => 'required',
			'device_type' => 'required',
			// 'device_token' => 'required',
		];


		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$postaudition = new Postaudition;
			$postaudition->user_id = $data['user_id'];
			;
			$postaudition->audition_title = $data['title'];

			if (!empty($data['category'])) {
				$postaudition->category = $data['category'];
			} else {
				$postaudition->category = 0;
			}

			$postaudition->audition_type = 2;

			if (!empty($data['photography_type'])) {
				$postaudition->photography_type = $data['photography_type'];
			}

			if (!empty($data['audition_date']) && !empty($data['audition_time'])) {
				$postaudition->expire_date = $data['audition_date'] . " " . $data['audition_time'];
			}

			if (!empty($data['gender'])) {
				$postaudition->gender = $data['gender'];
			}

			if (!empty($data['union_type'])) {
				$postaudition->union_type = $data['union_type'];
			}

			if (!empty($data['roles'])) {
				$postaudition->roles = $data['roles'];
			}

			if (!empty($data['age_group'])) {
				$postaudition->age_group = $data['age_group'];
			}

			$postaudition->location = $data['location'];
			$postaudition->status = '1';
			$postaudition->production_description = $data['production_description'];


			if (!empty($data['skills'])) {
				$postaudition->skills = $data['skills'];
			}

			if (!empty($data['studio_name'])) {
				$postaudition->studio_name = $data['studio_name'];
			}

			if (!empty($data['compensation'])) {
				$postaudition->compensation = $data['compensation'];
			}

			if (!empty($data['compensation_description'])) {
				$postaudition->compensation_description = $data['compensation_description'];
			}

			if (!empty($data['production_website'])) {
				$postaudition->production_website = $data['production_website'];
			}

			if (!empty($data['audition_location'])) {
				$postaudition->audition_location = $data['audition_location'];
			}

			if (!empty($data['roles'])) {
				$ids = array();
				foreach ($data['roles'] as $roledata) {
					$object = Role::updateOrCreate($roledata);


					if (empty($data['skills']) && !empty($roledata['skills'])) {
						$postaudition->skills = $roledata['skills'];
					}


					if (empty($data['age_group']) && !empty($roledata['age_min']) && !empty($roledata['age_max'])) {
						$postaudition->age_group = $roledata['age_min'] . '-' . $roledata['age_max'];
					}

					$lastid = $object->id;

					$ids[] = $lastid;
				}
				$postaudition->roles = implode(',', $ids);
			}



			$images = array();
			if ($request->file('photo')) {
				$file = $request->file('photo');

				if (!empty($file)) {
					foreach ($file as $key => $fileVal) {
						$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $fileVal->getClientOriginalName());
						$fileVal->move(public_path() . '/assets/photography/', $name);
						if ($key < 1) {
							$postaudition->photography_image = $name;
						}
						$images[] = $name;
					}
				}
			}


			if ($postaudition->save()) {

				if (!empty($images)) {
					foreach ($images as $key => $img) {
						DB::insert('insert into post_auditions_photographies (id, post_auditions_id,image) values (?, ?, ?)', [Null, $postaudition->id, $img]);
					}
				}

				if ($postaudition->skills != '') {
					$userskill = $postaudition->skills;

					$usersbyskill = DB::select("SELECT * FROM users WHERE skills REGEXP CONCAT('(^|,)(', REPLACE('$userskill', ',', '|'), ')(,|$)') AND notification_status = '1' AND status = '1' AND user_type = '1'");

					foreach ($usersbyskill as $usersbyskillval) {

						$message = "Audition added may be interested in";
						$notification_array = array(
							'title' => 'Audition added, may be you are interested',
							'body' => $message
						);
						//FCM api URL
						$url = 'https://fcm.googleapis.com/fcm/send';

						//api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
						$server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
						$fields = array();
						$fields['notification'] = $notification_array;

						$fields['to'] = $usersbyskillval->device_token;
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


				$response['status'] = "true";
				$response['message'] = "Photography event Successfully added";
				return response()->json($response);
			} else {
				$response['status'] = "false";
				$response['message'] = "Something went wrong. Please Try again.";
				return response()->json($response);
			}
		}
	}

	public function updatePhotography(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'audition_id' => 'required',
			'title' => 'required',
			'audition_date' => 'required',
			'audition_time' => 'required',
			'category' => 'required',
			'location' => 'required',
			'production_description' => 'required',
			'studio_name' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$postaudition = Postaudition::find($data['audition_id']);

			$postaudition->user_id = $data['user_id'];

			$postaudition->audition_title = $data['title'];

			if (!empty($data['category'])) {
				$postaudition->category = $data['category'];
			} else {
				$postaudition->category = 0;
			}
			if (!empty($data['audition_type'])) {
				$postaudition->audition_type = $data['audition_type'];
			}
			if (!empty($data['photography_type'])) {
				$postaudition->photography_type = $data['photography_type'];
			}

			if (!empty($data['audition_date']) && !empty($data['audition_time'])) {
				$postaudition->expire_date = $data['audition_date'] . " " . $data['audition_time'];
			}

			if (!empty($data['gender'])) {
				$postaudition->gender = $data['gender'];
			}

			if (!empty($data['union_type'])) {
				$postaudition->union_type = $data['union_type'];
			}

			if (!empty($data['roles'])) {
				$postaudition->roles = $data['roles'];
			}

			if (!empty($data['age_group'])) {
				$postaudition->age_group = $data['age_group'];
			}

			$postaudition->location = $data['location'];
			$postaudition->status = '1';
			$postaudition->production_description = $data['production_description'];


			if (!empty($data['skills'])) {
				$postaudition->skills = $data['skills'];
			}
			if (!empty($data['studio_name'])) {
				$postaudition->studio_name = $data['studio_name'];
			}

			if (!empty($data['compensation'])) {
				$postaudition->compensation = $data['compensation'];
			}

			if (!empty($data['compensation_description'])) {
				$postaudition->compensation_description = $data['compensation_description'];
			}

			if (!empty($data['production_website'])) {
				$postaudition->production_website = $data['production_website'];
			}

			if (!empty($data['audition_location'])) {
				$postaudition->audition_location = $data['audition_location'];
			}

			if (!empty($data['roles'])) {
				$ids = array();
				foreach ($data['roles'] as $roledata) {
					$object = Role::updateOrCreate($roledata);


					if (empty($data['skills']) && !empty($roledata['skills'])) {
						$postaudition->skills = $roledata['skills'];
					}


					if (empty($data['age_group']) && !empty($roledata['age_min']) && !empty($roledata['age_max'])) {
						$postaudition->age_group = $roledata['age_min'] . '-' . $roledata['age_max'];
					}

					$lastid = $object->id;

					$ids[] = $lastid;
				}
				$postaudition->roles = implode(',', $ids);
			}

			$images = array();
			if ($request->file('photo')) {
				$file = $request->file('photo');

				if (!empty($file)) {
					foreach ($file as $key => $fileVal) {
						$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $fileVal->getClientOriginalName());
						$fileVal->move(public_path() . '/assets/photography/', $name);
						if ($key < 1) {
							$postaudition->photography_image = $name;
						}
						$images[] = $name;
					}
				}
			}

			if ($postaudition->save()) {
				if (!empty($images)) {
					// delete records and images

					$oldimg = DB::select("SELECT * FROM post_auditions_photographies WHERE post_auditions_id = $postaudition->id");

					if (!empty($oldimg)) {
						foreach ($oldimg as $imgg) {
							$imgUrl = str_replace('public', '', trim($imgg->image));

							@unlink(public_path() . $imgUrl);
						}
					}

					DB::table('post_auditions_photographies')->where('post_auditions_id', '=', $postaudition->id)->delete();

					foreach ($images as $key => $img) {
						DB::insert('insert into post_auditions_photographies (id, post_auditions_id,image) values (?, ?, ?)', [Null, $postaudition->id, $img]);
					}
				}

				$response['status'] = "true";
				$response['message'] = "Photography event Successfully updated";
				return response()->json($response);
			} else {
				$response['status'] = "false";
				$response['message'] = "Something went wrong. Please Try again.";
				return response()->json($response);
			}
		}
	}

	public function updatePostaudition(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'audition_id' => 'required',
			'audition_title' => 'required',
			'audition_date' => 'required',
			'audition_time' => 'required',
			//'age_group' => 'required',
			'location' => 'required',
			'production_description' => 'required',
			//'skills' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$postaudition = Postaudition::find($data['audition_id']);

			$postaudition->user_id = $data['user_id'];
			$postaudition->ethnicities = $data['ethnicities'];


			$postaudition->audition_title = $data['audition_title'];

			if (!empty($data['category'])) {
				$postaudition->category = $data['category'];
			} else {
				$postaudition->category = 0;
			}
			if (!empty($data['audition_type'])) {
				$postaudition->audition_type = $data['audition_type'];
			}
			if (!empty($data['photography_type'])) {
				$postaudition->photography_type = $data['photography_type'];
			}

			if (!empty($data['audition_date']) && !empty($data['audition_time'])) {
				$postaudition->expire_date = $data['audition_date'] . " " . $data['audition_time'];
			}

			if (!empty($data['gender'])) {
				$postaudition->gender = $data['gender'];
			}

			if (!empty($data['union_type'])) {
				$postaudition->union_type = $data['union_type'];
			}

			if (!empty($data['roles'])) {
				$postaudition->roles = $data['roles'];
			}

			if (!empty($data['age_group'])) {
				$postaudition->age_group = $data['age_group'];
			}

			$postaudition->location = $data['location'];
			$postaudition->status = '1';
			$postaudition->production_description = $data['production_description'];


			if (!empty($data['skills'])) {
				$postaudition->skills = $data['skills'];
			}
			if (!empty($data['studio_name'])) {
				$postaudition->studio_name = $data['studio_name'];
			}

			$postaudition->compensation = $data['compensation'] ? '1' : '0';

			if (!empty($data['compensation_description'])) {
				$postaudition->compensation_description = $data['compensation_description'];
			}

			if (!empty($data['production_website'])) {
				$postaudition->production_website = $data['production_website'];
			}

			if (!empty($data['audition_location'])) {
				$postaudition->audition_location = $data['audition_location'];
			}

			if (!empty($data['roles'])) {
				$ids = array();
				foreach ($data['roles'] as $roledata) {
					$object = Role::updateOrCreate($roledata);


					if (empty($data['skills']) && !empty($roledata['skills'])) {
						$postaudition->skills = $roledata['skills'];
					}


					if (empty($data['age_group']) && !empty($roledata['age_min']) && !empty($roledata['age_max'])) {
						$postaudition->age_group = $roledata['age_min'] . '-' . $roledata['age_max'];
					}

					$lastid = $object->id;

					$ids[] = $lastid;
				}
				$postaudition->roles = implode(',', $ids);
			}

			if ($request->file('photography_image')) {
				$file = $request->file('photography_image');
				$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $file->getClientOriginalName());
				$file->move(public_path() . '/assets/photography/', $name);
				$postaudition->photography_image = $name;
			}

			$images = array();
			if ($request->file('photo')) {
				$file = $request->file('photo');

				if (!empty($file)) {
					foreach ($file as $key => $fileVal) {
						$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $fileVal->getClientOriginalName());
						$fileVal->move(public_path() . '/assets/photography/', $name);
						if ($key < 1) {
							$postaudition->photography_image = $name;
						}
						$images[] = $name;
					}
				}
			}
			
			if ($postaudition->save()) {
				if (!empty($images)) {
					foreach ($images as $key => $img) {
						DB::insert('insert into post_auditions_photographies (id, post_auditions_id,image) values (?, ?, ?)', [Null, $postaudition->id, $img]);
					}
				}
				$response['status'] = "true";
				$response['message'] = "Post Audition Successfully updated";
				return response()->json($response);
			} else {
				$response['status'] = "false";
				$response['message'] = "Something went wrong. Please Try again.";
				return response()->json($response);
			}
		}
	}

	public function postAuditionApply(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'post_audition_id' => 'required',
			//'role_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			if (isset($data['role_id']) && $data['role_id'] != '') {
				$audiotioparticipant = Auditionparticipant::where(["user_id" => $data['user_id'], "post_audition_id" => $data['post_audition_id'], "role_id" => $data['role_id']])->get();
			} else {
				$audiotioparticipant = Auditionparticipant::where(["user_id" => $data['user_id'], "post_audition_id" => $data['post_audition_id']])->get();
			}

			if (count($audiotioparticipant) > 0) {
				$response['status'] = "false";
				$response['message'] = "You have already applied";
				return response()->json($response);
			} else {
				$paudiparti = new Auditionparticipant;
				$paudiparti->user_id = $data['user_id'];

				if (!empty($data['role_id'])) {
					$paudiparti->role_id = $data['role_id'];
				}
				$paudiparti->post_audition_id = $data['post_audition_id'];

				if (isset($data['audition_type']) && $data['audition_type'] != '') {
					$paudiparti->audition_type = $data['audition_type'];
				}

				if ($paudiparti->save()) {


					$postauditiondata = Postaudition::where("id", $data['post_audition_id'])->first();
					$userdata = User::where("id", $postauditiondata['user_id'])->first();
					$auditionUser = User::where("id", $data['user_id'])->first();
					$auditionStageName = $auditionUser->stage_name ?? $auditionUser->name;
					$audiotioparticipant = Auditionparticipant::where([
						"user_id" => $data['user_id']
					])->get();
					$postTitle = $postauditiondata->audition_title ?? 'the audition';
					$isAppliedFirstTimeOnPlatform = count($audiotioparticipant) == 1;
					if($isAppliedFirstTimeOnPlatform){

						$this->sendUserNotification([
							'fcmToken' => $auditionUser['device_token'],
							'title' =>  'Congratulations!',
							'message' => "Your first applied opportunity on Acthound.",
							'customData' => [],
						]);
					if(!empty($auditionUser['id'])){
						$this->saveNotification([
							'user_id' => $auditionUser['id'],
							'title' => 'Congratulations!',
							'message' => "Your first applied opportunity on Acthound.",
							'notification_type' => 'audition_application',
							'reference_id' => $data['post_audition_id'],
							'custom_data' => json_encode([
								'type' => "audition_selected",
								"id" => $data['post_audition_id'],
							])
						]);
					}

					}


					$title = "Audition Update";
					$message = "$auditionStageName has applied to $postTitle";
				
					// if($userdata['notification_status'] == 1){

					$this->sendUserNotification([
						'fcmToken' => $userdata['device_token'],
						'title' => $title,
						'message' => $message,
						'customData' => [],
					]);
					if(!empty($userdata['id'])){
						$this->saveNotification([
							'user_id' => $userdata['id'],
							'title' => $title,
							'message' => $message,
							'notification_type' => 'audition_application',
							'reference_id' => $data['post_audition_id'],
							'custom_data' => json_encode([
								'type' => "producer_audition_selected",
								"id" => $data['post_audition_id'],
							])
						]);
					}

					// }



					$response['status'] = "true";
					$response['message'] = "Successfully Applied";
					return response()->json($response);
				} else {
					$response['status'] = "false";
					$response['message'] = "Something went wrong. Please Try again.";
					return response()->json($response);
				}
			}
		}
	}

	public function postauditionappliedlist(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postaudition = DB::table('post_audition_participants')
				->join('post_auditions', 'post_audition_participants.post_audition_id', '=', 'post_auditions.id')
				->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
				//->leftJoin('roles', 'post_audition_participants.role_id', '=', 'roles.id')
				//->select('post_auditions.*', 'categories.category_name','roles.role_title as roles')
				->select('post_auditions.*', 'categories.category_name')
				->where('post_audition_participants.user_id', $data['user_id'])
				->where('post_audition_participants.audition_type', '=', '1')
				->where('post_auditions.status', '1')
				->orderBy('post_auditions.pin_to_top', 'DESC')
				->orderBy('post_auditions.id', 'DESC')
				//->groupBy("post_audition_participants.post_audition_id")
				->get();


			$postauditiondata = array();
			foreach ($postaudition as $postauditionval) {


				$rolesData = array();

				$audition_type = $postauditionval->audition_type;

				$participants = array();

				if ($audition_type == '2') {
					$participants = DB::table('post_audition_participants')
						->join('users', 'post_audition_participants.user_id', '=', 'users.id')
						//->leftJoin('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
						//->join('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
						->select('post_audition_participants.*', 'users.*')
						->where('post_audition_id', $postauditionval->id)
						->get();

				} else {

					$roles = $postauditionval->roles;
					$allroles = explode(',', $roles);

					if (!empty($allroles)) {
						foreach ($allroles as $rol) {
							$rol = trim($rol);
							if (is_numeric($rol)) {
								$resrole = Role::where("roles.id", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();
								if (!empty($resrole)) {

									$applieds = Auditionparticipant::where(["post_audition_id" => $postauditionval->id, 'role_id' => $resrole->id])->where("user_id", $data['user_id'])->count();
									$checkapplys = false;
									if ($applieds > 0) {
										$checkapplys = true;
									}
									$resrole->applies = $checkapplys;

									// get all participants applied to this audition and role

									$participants = DB::table('post_audition_participants')
										->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id')
										->select('users.name', 'users.image', 'users.age', 'users.height', 'users.email', 'users.phone', 'users.pro_member', 'users.status')
										->where('post_audition_participants.role_id', $resrole->id)
										->where('post_audition_participants.post_audition_id', $postauditionval->id)
										->get();

									$resrole->participants = $participants;

									$rolesData[] = $resrole;

								}
							} else {
								$resrole = Role::where("roles.role_title", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();
								if (!empty($resrole)) {

									$applieds = Auditionparticipant::where(["post_audition_id" => $postauditionval->id, 'role_id' => $resrole->id])->where("user_id", $data['user_id'])->count();
									$checkapplys = false;
									if ($applieds > 0) {
										$checkapplys = true;
									}
									$resrole->applies = $checkapplys;

									// get all participants applied to this audition and role

									$participants = DB::table('post_audition_participants')
										->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id')
										->select('users.name', 'users.image', 'users.age', 'users.height', 'users.email', 'users.phone', 'users.pro_member', 'users.status')
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
				if (!empty($applied)) {
					$checkapply = true;
				}
				$promember = DB::table('users')->select('pro_member')
					->where('id', $postauditionval->user_id)
					->where('pro_member', '1')
					->first();
				$checkpromember = false;
				if (!empty($promember)) {
					$checkpromember = true;
				}

				//get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
					->where("user_id", $data['user_id'])
					->count();

				$is_fav = '0';

				if ($fav > 0) {
					$is_fav = '1';
				}

				$time_ago = $this->timeago($postauditionval->created_at, true);

				$postauditiondata[] = array
				(
					"id" => $postauditionval->id,
					"user_id" => $postauditionval->user_id,
					"audition_title" => $postauditionval->audition_title,
					"category" => $postauditionval->category,
					"expire_date" => $postauditionval->expire_date,
					"gender" => $postauditionval->gender,
					"union_type" => $postauditionval->union_type,
					"audition_type" => $postauditionval->audition_type,
					"age_group" => $postauditionval->age_group,
					"location" => $postauditionval->location,
					"photography_type" => $postauditionval->photography_type,
					"photography_image" => $postauditionval->photography_image,
					"production_description" => $postauditionval->production_description,
					"skills" => $postauditionval->skills,
					"status" => $postauditionval->status,
					"category_name" => $postauditionval->category_name,
					"studio_name" => $postauditionval->studio_name,
					"compensation" => $postauditionval->compensation,
					"compensation_description" => $postauditionval->compensation_description,
					"production_website" => $postauditionval->production_website,
					"audition_location" => $postauditionval->audition_location,
					"pin_to_top" => $postauditionval->pin_to_top,
					"created_at" => $postauditionval->created_at,
					"applied" => $checkapply,
					"pro_member" => $checkpromember,
					"roles" => $rolesData,
					"participants" => $participants,
					"is_fav" => $is_fav,
					"time_ago" => $time_ago,
				);

			}

			$response['status'] = "true";
			$response['message'] = "Post Audition Applied List";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}

	public function selectParticipant(Request $request)
	{
		$rules = [
			'device_type' => 'required|string',
			'id' => 'required|exists:post_audition_participants,id',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status' => "false",
				'message' => $this->validationHandle($validator->messages())
			]);
		}

		$updated = DB::table('post_audition_participants')
			->where('id', $request->id)
			->update([
				'is_selected' => '1'
			]);

		if ($updated) {

			$userData = DB::table('post_audition_participants')
				->join('users', 'post_audition_participants.user_id', '=', 'users.id')
				->join('post_auditions', 'post_audition_participants.post_audition_id', '=', 'post_auditions.id')
				->where('post_audition_participants.id', $request->id)
				->select('users.*','post_audition_participants.post_audition_id as post_audition_id','post_auditions.audition_title as post_title')
				->first();

			if($userData->device_token){
				$this->sendUserNotification([
					'fcmToken' => $userData->device_token ,
					'title' => $userData->post_title,
					'message' => "Congratulations! You've been selected to audition for " . $userData->post_title,
					'customData' => [],
				]);
			}
			if(!empty($userData->id)){
				$this->saveNotification([
					'user_id' => $userData->id,
					'title' => $userData->post_title,
					'message' => "Congratulations! You've been selected to audition for " . $userData->post_title,
					'notification_type' => 'participant_selected',
					'reference_id' => $request->id,
					'custom_data' => json_encode([
						'type' => "audition_selected",
						"id" => optional($userData)->post_audition_id,
					])
				]);
			}

			return response()->json([
				'status' => "true",
				'message' => "Participant selected successfully"
			]);
		} else {
			return response()->json([
				'status' => "false",
				'message' => "Failed to select the participant"
			]);
		}
	}

	public function deSelectParticipant(Request $request)
	{
		$rules = [
			'device_type' => 'required|string',
			'id' => 'required|exists:post_audition_participants,id',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status' => "false",
				'message' => $this->validationHandle($validator->messages())
			]);
		}

		$updated = DB::table('post_audition_participants')
			->where('id', $request->id)
			->update([
				'is_selected' => '0'
			]);

		if ($updated) {
			return response()->json([
				'status' => "true",
				'message' => "Participant de selected successfully"
			]);
		} else {
			return response()->json([
				'status' => "false",
				'message' => "Failed to de select the participant"
			]);
		}
	}

	public function auditionParticipants(Request $request)
	{
		$data = $request->all();
		$rules = [
			'audtion_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postauditionval = DB::table('post_auditions')
				->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
				->select('post_auditions.*', 'categories.category_name')
				->where('post_auditions.id', $data['audtion_id'])
				->first();
			//dd($postauditionval);

			//$audiotioparticipant = Portfolio::where(["post_audition_id" => $data['audtion_id']])->first();


			/* $participants = DB::table('post_audition_participants')
			->join('users', 'post_audition_participants.user_id', '=', 'users.id')
			//->leftJoin('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
			//->join('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
			->select('post_audition_participants.*', 'users.*')
			->where('post_audition_id', $data['audtion_id'])
			->get(); */


			/* $participantsdata = array();

			foreach($participants as $participantsval){

					$audiotioparticipant = Portfolio::where(["user_id" => $participantsval->user_id])->first();

					$participantsdata[] = array
		(
				"id"=> $participantsval->id,  
				"user_id"=> $participantsval->user_id,  
							"post_audition_id"=> $participantsval->post_audition_id,
							"name"=> $participantsval->name,
							"stage_name"=> $participantsval->stage_name,
							"company_name"=> $participantsval->company_name,
							"email"=> $participantsval->email,
							"phone"=> $participantsval->phone,
							"website"=> $participantsval->website,
							"image"=> $participantsval->image,
							"age"=> $participantsval->age,
							"height"=> $participantsval->height,
							"location"=> $participantsval->location,
							"gender"=> $participantsval->gender,
							"roles"=> $participantsval->roles,
							"user_type"=> $participantsval->user_type,
							"union_type"=> $participantsval->union_type,
							"skills"=> $participantsval->skills,
							"description"=> $participantsval->description,
							"status"=> $participantsval->status,
							"device_type"=> $participantsval->device_type,
							"device_token"=> $participantsval->device_token,
							"headshot_first"=> $audiotioparticipant['headshot_first'],
		"headshot_second"=> $audiotioparticipant['headshot_second'],
		"document_first"=> $audiotioparticipant['document_first'],
		"document_second"=> $audiotioparticipant['document_second'],
		"audio_first"=> $audiotioparticipant['audio_first'],
		"audio_second"=> $audiotioparticipant['audio_second'],
		"video_first"=> $audiotioparticipant['video_first'],
		"video_second"=> $audiotioparticipant['video_second'], 
					);  
			} 


	$auditionparticipant = array(
		"id"				=>  $postaudition->id,
		"audition_title"	=>  $postaudition->audition_title,
		"location"	=>  $postaudition->location,
		"roles"	=>  $postaudition->roles,
		"age_group"	=>  $postaudition->age_group,
		"skills"	=>  $postaudition->skills,
		"audition_date"	=>  $postaudition->expire_date,
		"category" 			=>  $postaudition->category_name,
		"participants"      =>  $participantsdata,
	);*/

			$postauditiondata = array();

			$audition_type = $postauditionval->audition_type;

			$rolesData = array();
			$participants = array();

			if ($audition_type == '2') {
				$participants = DB::table('post_audition_participants')
					->join('users', 'post_audition_participants.user_id', '=', 'users.id')
					->leftJoin('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
					//->join('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
					->select('post_audition_participants.*', 'post_audition_participants.id as post_audition_participant_id','users.*', 'portfolios.*')
					->where('post_audition_id', $data['audtion_id'])
					->get();

			} else {
				$roles = $postauditionval->roles;

				$allroles = explode(',', $roles);


				if (!empty($allroles)) {
					foreach ($allroles as $rol) {
						$rol = trim($rol);
						if (is_numeric($rol)) {
							$resrole = Role::where("roles.id", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {
								// get all participants applied to this audition and role

								$participants = DB::table('post_audition_participants')
									->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id')
									->leftJoin('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
									->select('users.*', 'portfolios.*', 'post_audition_participants.id as post_audition_participant_id', 'post_audition_participants.is_selected', 'users.id as user_id')
									->where('post_audition_participants.role_id', $resrole->id)
									->where('post_audition_participants.post_audition_id', $postauditionval->id)
									->get();

								$resrole->participants = $participants;
								//dd($participants);
								$rolesData[] = $resrole;

							}
						} else {
							$resrole = Role::where("roles.role_title", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {

								// get all participants applied to this audition and role

								$participants = DB::table('post_audition_participants')
									->Join('users', 'post_audition_participants.user_id', '=', 'users.id')
									->Join('portfolios', 'post_audition_participants.user_id', '=', 'portfolios.user_id')
									->select('users.name', 'users.id', 'users.image', 'users.age', 'users.user_id', 'users.height', 'users.email', 'users.phone', 'users.pro_member', 'users.status', 'portfolios.*', 'post_audition_participants.id as post_audition_participant_id', 'post_audition_participants.is_selected')
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



			$applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $postauditionval->user_id)->first();
			$checkapply = false;
			if (!empty($applied)) {
				$checkapply = true;
			}
			$promember = DB::table('users')->select('pro_member')
				->where('id', $postauditionval->user_id)
				->where('pro_member', '1')
				->first();
			$checkpromember = false;
			if (!empty($promember)) {
				$checkpromember = true;
			}

			$time_ago = $this->timeago($postauditionval->created_at, true);

			$postauditiondata = array
			(
				"id" => $postauditionval->id,
				"user_id" => $postauditionval->user_id,
				"audition_title" => $postauditionval->audition_title,
				"category" => $postauditionval->category,
				"expire_date" => $postauditionval->expire_date,
				"gender" => $postauditionval->gender,
				"union_type" => $postauditionval->union_type,
				"audition_type" => $postauditionval->audition_type,
				"age_group" => $postauditionval->age_group,
				"location" => $postauditionval->location,
				"photography_type" => $postauditionval->photography_type,
				"photography_image" => $postauditionval->photography_image,
				"production_description" => $postauditionval->production_description,
				"skills" => $postauditionval->skills,
				"status" => $postauditionval->status,
				"category_name" => $postauditionval->category_name,
				"studio_name" => $postauditionval->studio_name,
				"compensation" => $postauditionval->compensation,
				"compensation_description" => $postauditionval->compensation_description,
				"production_website" => $postauditionval->production_website,
				"audition_location" => $postauditionval->audition_location,
				"pin_to_top" => $postauditionval->pin_to_top,
				"created_at" => $postauditionval->created_at,
				"applied" => $checkapply,
				"pro_member" => $checkpromember,
				"roles" => $rolesData,
				"participants" => $participants,
				"time_ago" => $time_ago
			);


			$response['status'] = "true";
			$response['message'] = "Post Audition Participants";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}


	public function deleteaudiotion(Request $request)
	{

		$data = $request->all();
		$rules = [
			'audition_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$firestore = new FireStoreApiClient(
				'stage-picker',
				'AIzaSyB21YVALjKHU_Q3S76qT9vdH908esWbTJI'
			);

			$audiotiondata = Postaudition::where(["id" => $data['audition_id']])->first();

			$auditiontitle = $audiotiondata['audition_title'];
			$auditionid = $data['audition_id'];
			$deleteablemany = $auditionid . '_' . $auditiontitle;

			$firestore->deleteDocument('groups', $deleteablemany);

			$audiotioparticipant = Auditionparticipant::where(["post_audition_id" => $data['audition_id']])->get();

			foreach ($audiotioparticipant as $audiotioparticipantval) {

				$participant_id = $audiotioparticipantval->user_id;
				$deleteableonebyone = $auditionid . '_' . $participant_id;
				$firestore->deleteDocument('groups', $deleteableonebyone);
			}

			Postaudition::where('id', '=', $data['audition_id'])->delete();
			Auditionparticipant::where('post_audition_id', '=', $data['audition_id'])->delete();
			$response['status'] = "true";
			$response['message'] = "Audition Data deleted successfully";
			return response()->json($response);

		}
	}



	public function photographylist(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			/* $postaudition = DB::table('post_auditions') 
			->select('post_auditions.*')
			->where('user_id', $data['user_id'])
			->where('status', '1')
			->where('audition_type', '2')
			->get(); */


			$postaudition = Postaudition::with('post_auditions_photographies')
				->select('post_auditions.id', 'post_auditions.user_id', 'post_auditions.audition_title', 'post_auditions.expire_date', 'post_auditions.gender', 'post_auditions.photography_type', 'post_auditions.union_type', 'post_auditions.location', 'post_auditions.production_description', 'post_auditions.skills', 'post_auditions.status', 'post_auditions.studio_name', 'post_auditions.compensation', 'post_auditions.compensation_description', 'post_auditions.production_website', 'post_auditions.audition_location', 'post_auditions.pin_to_top', 'post_auditions.created_at', 'categories.category_name')
				->leftJoin('categories', 'post_auditions.category', '=', 'categories.id')
				->where('user_id', $data['user_id'])
				->where('status', '1')
				->where('audition_type', '2')
				->orderBy('post_auditions.pin_to_top', 'DESC')
				->orderBy('post_auditions.id', 'DESC')
				->get();

			$response['status'] = "true";
			$response['message'] = "Photography Audition List";
			$response['data'] = $postaudition;
			return response()->json($response);
		}
	}


	public function external_casting_list(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			//'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			/* $postaudition = DB::table('post_auditions') 
			->select('post_auditions.*')
			->where('user_id', $data['user_id'])
			->where('status', '1')
			->where('audition_type', '2')
			->get(); */


			$postaudition = Postaudition::with('post_auditions_photographies')
				->select(
					'post_auditions.id',
					'post_auditions.user_id',
					'post_auditions.audition_title',
					'post_auditions.expire_date',
					'post_auditions.gender',
					'post_auditions.photography_type',
					'post_auditions.union_type',
					'post_auditions.location',
					'post_auditions.production_description',
					'post_auditions.skills',
					'post_auditions.status',
					'post_auditions.studio_name',
					'post_auditions.compensation',
					'post_auditions.compensation_description',
					'post_auditions.production_website',
					'post_auditions.audition_location',
					'post_auditions.pin_to_top',
					'post_auditions.created_at',
					DB::raw('GROUP_CONCAT(post_auditions_photographies.image) as photography_images')
				)
				->leftJoin('post_auditions_photographies', 'post_auditions_photographies.post_auditions_id', '=', 'post_auditions.id')
				->where('user_id', $data['user_id'])
				->where('status', '1')
				->where('audition_type', '3')
				->orderBy('post_auditions.pin_to_top', 'DESC')
				->orderBy('post_auditions.id', 'DESC')
				->groupBy('post_auditions.id')
				->get();

			$response['status'] = "true";
			$response['message'] = "External Casting List";
			$response['data'] = $postaudition;
			return response()->json($response);
		}
	}


	public function photographysearch(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$sql = "SELECT post_auditions.*,categories.category_name FROM post_auditions LEFT JOIN categories ON categories.id = post_auditions.category WHERE status = '1' AND audition_type = '2'";
			$where = '';
			if (!empty($data['photography_type'])) {
				$photographytype = $data['photography_type'];
				$where .= " AND photography_type = '$photographytype'";
			}
			if (!empty($data['skills'])) {
				$skills = $data['skills'];
				$where .= " AND skills REGEXP CONCAT('(^|,)(', REPLACE('$skills', ',', '|'), ')(,|$)')";
			}
			if (!empty($data['location'])) {
				$location = $data['location'];
				$where .= " AND location = '$location'";
			}
			if (!empty($data['age_group'])) {
				$age_group = $data['age_group'];
				$where .= " AND age_group = '$age_group'";
			}



			$order = ' ORDER BY post_auditions.pin_to_top DESC, post_auditions.id DESC';

			$completequery = $sql . " " . $where . " " . $order;

			$postaudition = DB::select("$completequery");


			$postauditiondata = array();
			foreach ($postaudition as $postauditionval) {

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
				if (!empty($applied)) {
					$checkapply = true;
				}
				$promember = DB::table('users')->select('pro_member')
					->where('id', $postauditionval->user_id)
					->where('pro_member', '1')
					->first();
				$checkpromember = false;
				if (!empty($promember)) {
					$checkpromember = true;
				}

				//get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
					->where("user_id", $data['user_id'])
					->count();

				$is_fav = '0';

				if ($fav > 0) {
					$is_fav = '1';
				}

				$time_ago = $this->timeago($postauditionval->created_at, true);

				$postauditiondata[] = array
				(
					"id" => $postauditionval->id,
					"user_id" => $postauditionval->user_id,
					"audition_title" => $postauditionval->audition_title,
					"audition_type" => $postauditionval->audition_type,
					"category" => $postauditionval->category,
					"expire_date" => $postauditionval->expire_date,
					"gender" => $postauditionval->gender,
					"union_type" => $postauditionval->union_type,
					"photography_type" => $postauditionval->photography_type,
					//"roles"=> $postauditionval->roles,  
					"age_group" => $postauditionval->age_group,
					"location" => $postauditionval->location,
					"production_description" => $postauditionval->production_description,
					"skills" => $postauditionval->skills,
					"status" => $postauditionval->status,
					"studio_name" => $postauditionval->studio_name,
					"compensation" => $postauditionval->compensation,
					"compensation_description" => $postauditionval->compensation_description,
					"production_website" => $postauditionval->production_website,
					"audition_location" => $postauditionval->audition_location,
					"applied" => $checkapply,
					"pro_member" => $checkpromember,
					"category_name" => $postauditionval->category_name,
					"photography_image" => $postauditionval->photography_image,
					"photography_images_all" => $photos,
					"pin_to_top" => $postauditionval->pin_to_top,
					"created_at" => $postauditionval->created_at,
					"ethnicities" => $postauditionval->ethnicities,
					"is_fav" => $is_fav,
					"time_ago" => $time_ago,
				);
			}

			$postaudition = DB::select("$completequery");

			$response['status'] = "true";
			$response['message'] = "Search Result";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}


	public function producerDashboard(Request $request)
	{
		$data = $request->all();
		$date = Carbon::now();
		$formatted = $date->format('Y-m-d H:i:s');
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$user_id = $data['user_id'];
			$postaudition = DB::table('post_auditions')
				->where('post_auditions.audition_type', '1')
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				->where('post_auditions.user_id', $data['user_id'])
				->count();

			$postphotography = DB::table('post_auditions')
				->where('post_auditions.audition_type', '2')
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				->where('post_auditions.user_id', $data['user_id'])
				->count();

			$runningpostaudition = DB::table('post_auditions')
				->where('post_auditions.status', '1')
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				->where('post_auditions.audition_type', '1')
				->where('post_auditions.user_id', $data['user_id'])
				->count();

			$runningpostphotography = DB::table('post_auditions')
				->where('post_auditions.status', '1')
				->where('post_auditions.audition_type', '2')
				->where('post_auditions.user_id', $data['user_id'])
				->count();

			$totalphotographyapply = DB::table('post_audition_participants')
				->join('post_auditions', function ($join) use ($user_id) {
					$join->on('post_auditions.id', '=', 'post_audition_participants.post_audition_id')
						->where('post_auditions.user_id', $user_id)
						->where('post_auditions.audition_type', '2');
				})
				->count();

			$totalauditionapply = DB::table('post_audition_participants')
				->join('post_auditions', function ($join) use ($user_id) {
					$join->on('post_auditions.id', '=', 'post_audition_participants.post_audition_id')
						->where('post_auditions.user_id', $user_id)
						->where('post_auditions.audition_type', '1');
				})
				->count();

			$sliders = DB::table('sliders')
				->select('sliders.*')
				->where('status', '1')
				->get();

			$productioncrew = DB::table('production_crews')
				->where('production_crews.user_id', $data['user_id'])
				->count();

			$runningproductioncrew = DB::table('production_crews')
				->where('production_crews.status', '1')
				->where('production_crews.expire_date', '>=', date("Y-m-d H:i:s"))
				->where('production_crews.user_id', $data['user_id'])
				->count();

			$totalcrewapply = DB::table('post_audition_participants')
				->join('production_crews', function ($join) use ($user_id) {
					$join->on('production_crews.id', '=', 'post_audition_participants.post_audition_id')
						->where('production_crews.user_id', $user_id)
						->where('post_audition_participants.audition_type', '1');
				})
				->count();

			$response['status'] = "true";
			$response['message'] = "Post Audition List";
			$response['postaudition'] = $postaudition;
			$response['postphotography'] = $postphotography;
			$response['runningpostaudition'] = $runningpostaudition;
			$response['runningpostphotography'] = $runningpostphotography;
			$response['totalauditionapply'] = $totalauditionapply;
			$response['totalphotographyapply'] = $totalphotographyapply;
			$response['productioncrew'] = $productioncrew;
			$response['runningproductioncrew'] = $runningproductioncrew;
			$response['totalcrewapply'] = $totalcrewapply;
			$response['sliders'] = $sliders;
			return response()->json($response);
		}
	}

	public function archivePosts(Request $request)
	{
		$data = $request->all();
		$date = Carbon::now();
		$formatted = $date->format('Y-m-d H:i:s');
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$d = DB::table('post_auditions')
				->join('categories', 'post_auditions.category', '=', 'categories.id')
				->select('post_auditions.*', 'categories.category_name')
				->where('post_auditions.user_id', $data['user_id'])
				// ->where('post_auditions.status', '1')
				// ->whereRaw("STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') < ?", [$date])
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') < STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				->where('post_auditions.audition_type', '1')
				->orderBy('post_auditions.pin_to_top', 'DESC')
				->orderBy('post_auditions.id', 'DESC')
				->groupBy("post_auditions.id");
			$sql = Str::replaceArray('?', $d->getBindings(), $d->toSql());

			// dd($sql);
			$postaudition = $d->get();
			$postauditiondata = array();

			if (!empty($postaudition)) {
				foreach ($postaudition as $postauditionval) {

					//get role data 
					$roles = $postauditionval->roles;
					$allroles = explode(',', $roles);
					$rolesData = array();

					if (!empty($allroles)) {
						foreach ($allroles as $rol) {
							$rol = trim($rol);
							if (is_numeric($rol)) {
								$resrole = Role::where("roles.id", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();

								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							} else {
								$resrole = Role::where("roles.role_title", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();
								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							}
						}
					}

					//get fav data
					$fav = Favourite::where("post_audition_id", $postauditionval->id)
						->where("user_id", $data['user_id'])
						->first();

					$is_fav = '0';

					if (!empty($fav)) {
						$is_fav = '1';
					}

					$time_ago = $this->timeago($postauditionval->created_at, true);

					$postauditiondata[] = array(
						"id" => $postauditionval->id,
						"user_id" => $postauditionval->user_id,
						"audition_title" => $postauditionval->audition_title,
						"category" => $postauditionval->category,
						"expire_date" => $postauditionval->expire_date,
						"gender" => $postauditionval->gender,
						"roles" => $rolesData,
						"union_type" => $postauditionval->union_type,
						"audition_type" => $postauditionval->audition_type,
						"age_group" => $postauditionval->age_group,
						"location" => $postauditionval->location,
						"photography_type" => $postauditionval->photography_type,
						"photography_image" => $postauditionval->photography_image,
						"production_description" => $postauditionval->production_description,
						"skills" => $postauditionval->skills,
						"status" => $postauditionval->status,
						"studio_name" => $postauditionval->studio_name,
						"compensation" => $postauditionval->compensation,
						"compensation_description" => $postauditionval->compensation_description,
						"production_website" => $postauditionval->production_website,
						"audition_location" => $postauditionval->audition_location,
						"category_name" => $postauditionval->category_name,
						"created_at" => $postauditionval->created_at,
						"pin_to_top" => $postauditionval->pin_to_top,
						"is_fav" => $is_fav,
						"fav_data" => $fav,
						"time_ago" => $time_ago
					);
				}
			}

			$response['status'] = "true";
			$response['message'] = "Post Audition List";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}

	public function sliders(Request $request)
	{
		$data = $request->all();
		$rules = [
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$sliders = DB::table('sliders')
				->select('sliders.*')
				->where('status', '1')
				->get();

			$response['status'] = "true";
			$response['message'] = "Sliders List";
			$response['data'] = $sliders;
			return response()->json($response);
		}
	}


	public function analytics(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'audition_type' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$user_id = $data['user_id'];
			$audition_type = $data['audition_type'];
			$date = Carbon::now();
			$formatted = $date->format('Y-m-d H:i:s');

			$postaudition = Postaudition::where('post_auditions.audition_type', $audition_type)
				->where('post_auditions.user_id', $user_id)
				// ->where(DB::raw("(STR_TO_DATE(post_auditions.expire_date,'%d-%m-%Y'))"), ">=", date("Y-m-d"))
				->whereRaw(
					"STR_TO_DATE(post_auditions.expire_date, '%d-%m-%Y %h:%i %p') >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
					[$formatted]
				)
				//->with(['post_auditions_apply','post_auditions_viewed']) 
				->withCount([
					'post_auditions_apply',
					'post_auditions_viewed'
				])
				->orderBy('post_auditions.id', 'asc')
				->groupBy(['post_auditions.id'])
				->get();

			$totalusers = DB::table('users')
				->where('users.status', '1')
				->where('users.user_type', '1')
				->count();


			$response['status'] = "true";
			$response['message'] = "Post Audition Analytics";
			$response['postaudition'] = $postaudition;
			$response['totalusers'] = $totalusers;
			return response()->json($response);
		}
	}


	public function postAuditionViewed(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'audition_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			if (isset($data['audition_type']) && $data['audition_type'] != '') {
				$auditionView = DB::table('post_auditions_views')
					->where('post_auditions_views.user_id', $data['user_id'])
					->where('post_auditions_views.audition_type', $data['audition_type'])
					->where('post_auditions_views.post_audition_id', $data['audition_id'])
					->count();
			} else {
				$auditionView = DB::table('post_auditions_views')
					->where('post_auditions_views.user_id', $data['user_id'])
					->where('post_auditions_views.post_audition_id', $data['audition_id'])
					->count();
			}

			if ($auditionView > 0) {
				$response['status'] = "false";
				$response['message'] = "You have already applied";
				return response()->json($response);
			} else {
				$paudiparti = new AuditionView;
				$paudiparti->user_id = $data['user_id'];

				$paudiparti->post_audition_id = $data['audition_id'];

				if (isset($data['audition_type']) && $data['audition_type'] != '') {
					$audition_type = $data['audition_type'];
				} else {
					$audition_type = 1;
				}
				$paudiparti->audition_type = $audition_type;
				$paudiparti->created_at = date("Y-m-d H:i:s");
				$paudiparti->updated_at = date("Y-m-d H:i:s");

				if ($paudiparti->save()) {
					$response['status'] = "true";
					$response['message'] = "Successfully addedd";
					return response()->json($response);
				} else {
					$response['status'] = "false";
					$response['message'] = "Something went wrong. Please Try again.";
					return response()->json($response);
				}
			}
		}
	}


	public function favouriteAdd(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'audition_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$favView = DB::table('favourites')
				->where('favourites.user_id', $data['user_id'])
				->where('favourites.post_audition_id', $data['audition_id'])
				->count();

			if ($favView > 0) {
				$response['status'] = "false";
				$response['message'] = "You have already marked as fav";
				return response()->json($response);
			} else {
				$fav = new Favourite;
				$fav->user_id = $data['user_id'];

				$fav->post_audition_id = $data['audition_id'];
				$fav->created_at = date("Y-m-d H:i:s");
				$fav->updated_at = date("Y-m-d H:i:s");

				if ($fav->save()) {
					$response['status'] = "true";
					$response['message'] = "Successfully marked as favourite";
					return response()->json($response);
				} else {
					$response['status'] = "false";
					$response['message'] = "Something went wrong. Please Try again.";
					return response()->json($response);
				}
			}
		}
	}

	public function favouriteRemove(Request $request)
	{
		$data = $request->all();
		$rules = [
			'audition_id' => 'required',
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			try {
				Favourite::where(["post_audition_id" => $data['audition_id'], "user_id" => $data['user_id']])->delete();
				$response['status'] = "true";
				$response['message'] = "Portfolio Data deleted successfully";
				return response()->json($response);

			} catch (\Exception $e) {
				$response['status'] = "false";
				$response['message'] = "Something went wrong";
				return response()->json($response);
			}
		}
	}


	public function favouriteList(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$user_id = $data['user_id'];

			$data = Postaudition::whereHas('favourite', function ($query) use ($user_id) {
				$query->where('favourites.user_id', $user_id);
			})
				->with(['favourite'])
				->get();

			$favourite = array();

			if (!empty($data)) {
				foreach ($data as $key => $postauditionval) {
					$rolesData = array();
					$roles = $postauditionval->roles;
					$category = $postauditionval->category;
					$allroles = explode(',', $roles);

					if ($category != '') {
						$catData = Category::where("categories.id", $category)
							->select('categories.category_name')
							->first();

						if (!empty($catData)) {
							$data[$key]['category_name'] = $catData->category_name;
						}
					}

					if (!empty($allroles)) {
						foreach ($allroles as $rol) {
							$rol = trim($rol);
							if (is_numeric($rol)) {

								$resrole = Role::where("roles.id", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();

								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}

							} else {

								$resrole = Role::where("roles.role_title", $rol)
									->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
									->select('roles.*', 'role_types.title as role_type')
									->first();

								if (!empty($resrole)) {
									$rolesData[] = $resrole;
								}
							}
						}
					}

					$data[$key]['roles'] = $rolesData;
				}
			}

			$response['status'] = "true";
			$response['message'] = "Favourite List";
			$response['data'] = $data;
			return response()->json($response);
		}
	}

	public function addProductionCrew(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'crew_member' => 'required',
			'title' => 'required',
			'expire_date' => 'required',
			'time' => 'required',
			'location' => 'required',

			'description' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$postaudition = new ProductionCrew;
			$postaudition->user_id = $data['user_id'];
			//$postaudition->role_type = $data['role_type'];
			$postaudition->title = $data['title'];
			$postaudition->crew_member = $data['crew_member'];
			if (!empty($data['category'])) {
				$postaudition->category = $data['category'];
			} else {
				$postaudition->category = 0;
			}
			if (!empty($data['union_type'])) {
				$postaudition->union_type = $data['union_type'];
			}
			if (!empty($data['photography_type'])) {
				$postaudition->photography_type = $data['photography_type'];
			}

			if (!empty($data['expire_date']) && !empty($data['time'])) {
				$arr = explode('-', $data['expire_date']);
				$expire_date = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
				$postaudition->expire_date = $expire_date . " " . date("H:i:s", strtotime($data['time']));
			}
			if (!empty($data['roles'])) {
				$postaudition->roles = $data['roles'];
			}

			$postaudition->location = $data['location'];
			$postaudition->status = '1';
			$postaudition->description = $data['description'];
			$postaudition->ethnicities = @$data['ethnicities'];




			if (!empty($data['skills'])) {
				$postaudition->skills = $data['skills'];
			}

			if (!empty($data['compensation'])) {
				$postaudition->compensation = $data['compensation'];
			}

			if (!empty($data['compensation_description'])) {
				$postaudition->compensation_description = $data['compensation_description'];
			}

			if (!empty($data['website_url'])) {
				$postaudition->website_url = $data['website_url'];
			}

			if (!empty($data['location'])) {
				$postaudition->location = $data['location'];
			}

			if (!empty($data['roles'])) {
				$ids = array();
				foreach ($data['roles'] as $roledata) {

					if (empty($data['skills']) && !empty($roledata['skills'])) {
						$postaudition->skills = $roledata['skills'];
					}

					$object = CrewRole::updateOrCreate($roledata);

					$lastid = $object->id;

					$ids[] = $lastid;
				}
				$postaudition->roles = implode(',', $ids);
			}


		

			//new code for multiple file upload
			$images = array();
			if ($request->file('photo')) {
				$file = $request->file('photo');

				if (!empty($file)) {
					foreach ($file as $key => $fileVal) {
						$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $fileVal->getClientOriginalName());
						$fileVal->move(public_path() . '/assets/photography/', $name);
						
						$images[] = $name;
					}
				}
			}

			if ($postaudition->save()) {

				if (!empty($images)) {
					foreach ($images as $key => $img) {
						DB::insert('insert into post_auditions_photographies (id, post_auditions_id,image) values (?, ?, ?)', [Null, $postaudition->id, $img]);
					}
				}

				/* if($postaudition->skills != ''){
	$userskill = $postaudition->skills;

	$usersbyskill =   DB::select("SELECT * FROM users WHERE skills REGEXP CONCAT('(^|,)(', REPLACE('$userskill', ',', '|'), ')(,|$)') AND notification_status = '1' AND status = '1' AND user_type = '1'");

	foreach($usersbyskill as $usersbyskillval){

		$message = "Production crew added may be interested in";
		$notification_array = array(
			'title' => 'Production crew added, may be you are interested',
			'body' => $message
		);
		//FCM api URL
		$url = 'https://fcm.googleapis.com/fcm/send';

		//api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
		$server_key = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';
		$fields = array();
		$fields['notification'] = $notification_array;

		$fields['to'] = $usersbyskillval->device_token;
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
} */

				$response['status'] = "true";
				$response['message'] = "Crew Successfully added";
				return response()->json($response);
			} else {
				$response['status'] = "false";
				$response['message'] = "Something went wrong. Please Try again.";
				return response()->json($response);
			}
		}
	}

	public function updateProductionCrew(Request $request)
	{
		$data = $request->all();
		$rules = [
			'id' => 'required',
			'user_id' => 'required',
			'crew_member' => 'required',
			'title' => 'required',
			'expire_date' => 'required',
			'time' => 'required',
			'location' => 'required',
			'description' => 'required',
			'device_type' => 'required',
			'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postaudition = ProductionCrew::find($data['id']);
			$postaudition->user_id = $data['user_id'];
			;
			$postaudition->title = $data['title'];
			//$postaudition->role_type = $data['role_type'];
			$postaudition->crew_member = $data['crew_member'];
			if (!empty($data['category'])) {
				$postaudition->category = $data['category'];
			} else {
				$postaudition->category = 0;
			}
			if (!empty($data['union_type'])) {
				$postaudition->union_type = $data['union_type'];
			}
			if (!empty($data['photography_type'])) {
				$postaudition->photography_type = $data['photography_type'];
			}

			if (!empty($data['expire_date']) && !empty($data['time'])) {
				$arr = explode('-', $data['expire_date']);
				$expire_date = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
				$postaudition->expire_date = $expire_date . " " . date("H:i:s", strtotime($data['time']));
			}

			$postaudition->location = $data['location'];
			$crewStatus = '1';
			if (isset($data['status']) && $data['status'] != '') {
				$crewStatus = $data['status'];
			}

			$postaudition->status = $crewStatus;


			$postaudition->description = $data['description'];
			$postaudition->ethnicities = $data['ethnicities'] ?? '';


			if (!empty($data['skills'])) {
				$postaudition->skills = $data['skills'];
			}

			if (!empty($data['compensation'])) {
				$postaudition->compensation = $data['compensation'];
			}

			if (!empty($data['compensation_description'])) {
				$postaudition->compensation_description = $data['compensation_description'];
			}

			if (!empty($data['website_url'])) {
				$postaudition->website_url = $data['website_url'];
			}

			if (!empty($data['location'])) {
				$postaudition->location = $data['location'];
			}

			// Parse roles: handle both PHP-parsed nested array and flat bracket-notation keys
			// (e.g. roles[0][role_title] sent as form-data may not always be auto-parsed)
			$rolesInput = [];
			if (!empty($data['roles']) && is_array($data['roles'])) {
				$rolesInput = $data['roles'];
			} else {
				foreach ($data as $key => $value) {
					if (preg_match('/^roles\[(\d+)\]\[(\w+)\]$/', $key, $matches)) {
						$rolesInput[(int)$matches[1]][$matches[2]] = $value;
					}
				}
			}
			
			if (!empty($rolesInput)) {
				$ids = [];
				foreach ($rolesInput as $roledata) {
					
					if (empty($data['skills']) && !empty($roledata['skills'])) {
						$postaudition->skills = $roledata['skills'];
					}

					$crewRole = new CrewRole;
					$crewRole->role_title  = $roledata['role_title'] ?? '';
					$crewRole->role_type   = $roledata['role_type'] ?? '';
					$crewRole->category    = $roledata['category'] ?? '';
					$crewRole->skills      = $roledata['skills'] ?? '';
					$crewRole->description = $roledata['description'] ?? '';
					
					$crewRole->save();
					$ids[] = $crewRole->id;
				}
				$postaudition->roles = implode(',', $ids);
			}
			

			//new code for multiple file upload
			$images = array();
			if ($request->file('photo')) {
				$file = $request->file('photo');

				if (!empty($file)) {
					foreach ($file as $key => $fileVal) {
						$name = 'public/assets/photography/' . time() . str_replace(" ", "_", $fileVal->getClientOriginalName());
						$fileVal->move(public_path() . '/assets/photography/', $name);
						
						$images[] = $name;
					}
				}
			}

			if ($postaudition->save()) {

				if (!empty($images)) {
					$oldimg = DB::select("SELECT * FROM post_auditions_photographies WHERE post_auditions_id = $postaudition->id");

					if (!empty($oldimg)) {
						foreach ($oldimg as $imgg) {
							$imgUrl = str_replace('public', '', trim($imgg->image));
							@unlink(public_path() . $imgUrl);
						}
					}

					DB::table('post_auditions_photographies')->where('post_auditions_id', '=', $postaudition->id)->delete();

					foreach ($images as $key => $img) {
						DB::insert('insert into post_auditions_photographies (id, post_auditions_id,image) values (?, ?, ?)', [Null, $postaudition->id, $img]);
					}
				}

				$response['status'] = "true";
				$response['message'] = "Crew Successfully updated";
				return response()->json($response);
			} else {

				$response['status'] = "false";
				$response['message'] = "Something went wrong. Please Try again.";
				return response()->json($response);
			}
		}
	}

	public function deleteProductionCrew(Request $request)
	{

		$data = $request->all();
		$rules = [
			'id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {

			$oldimg = DB::select("SELECT * FROM post_auditions_photographies WHERE post_auditions_id = " . (int)$data['id']);

			if (!empty($oldimg)) {
				foreach ($oldimg as $imgg) {
					$imgUrl = str_replace('public', '', trim($imgg->image));
					@unlink(public_path() . $imgUrl);
				}
			}

			DB::table('post_auditions_photographies')->where('post_auditions_id', '=', $data['id'])->delete();

			ProductionCrew::where('id', '=', $data['id'])->delete();
			//Auditionparticipant::where('post_audition_id', '=', $data['audition_id'])->delete();
			$response['status'] = "true";
			$response['message'] = "Production Crew Data deleted successfully";
			return response()->json($response);
		}
	}

	public function productioncrewlist(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			if (isset($data['user_type']) && $data['user_type'] == 'auditioner') {
				$postaudition = DB::table('production_crews')
					->join('categories', 'production_crews.category', '=', 'categories.id')
					->leftJoin('post_auditions_photographies', 'post_auditions_photographies.post_auditions_id', '=', 'production_crews.id')
					->select('production_crews.*', 'categories.category_name', DB::raw('GROUP_CONCAT(post_auditions_photographies.image) as photography_images'))
					->where('production_crews.status', '1')
					->orderBy('production_crews.pin_to_top', 'DESC')
					->orderBy('production_crews.id', 'DESC')
					->groupBy("production_crews.id")
					->limit(15)
					->get();
			} else {
				$postaudition = DB::table('production_crews')
					->join('categories', 'production_crews.category', '=', 'categories.id')
					->leftJoin('post_auditions_photographies', 'post_auditions_photographies.post_auditions_id', '=', 'production_crews.id')
					->select('production_crews.*', 'categories.category_name', DB::raw('GROUP_CONCAT(post_auditions_photographies.image) as photography_images'))
					->where('production_crews.user_id', $data['user_id'])
					->where('production_crews.status', '1')
					->orderBy('production_crews.pin_to_top', 'DESC')
					->orderBy('production_crews.id', 'DESC')
					->groupBy("production_crews.id")
					->limit(15)
					->get();
			}


			$postauditiondata = array();

			if (!empty($postaudition)) {
				foreach ($postaudition as $postauditionval) {

					//get role data 
					$roles = $postauditionval->roles;
					$allroles = explode(',', $roles);
					$rolesData = array();

					if (!empty($allroles)) {
						foreach ($allroles as $rol) {
							$rol = trim($rol);
							$resrole = CrewRole::where("crew_roles.id", $rol)
								->leftJoin('role_types', 'crew_roles.role_type', '=', 'role_types.id')
								->leftJoin('categories', 'crew_roles.category', '=', 'categories.id')
								->select('crew_roles.*', 'role_types.title as role_type', 'categories.category_name as category')
								->first();

							if (!empty($resrole)) {
								$rolesData[] = $resrole;
							}
						}
					}

					//get fav data
					$fav = Favourite::where("post_audition_id", $postauditionval->id)
						->where("user_id", $data['user_id'])
						->first();

					$is_fav = '0';

					if (!empty($fav)) {
						$is_fav = '1';
					}

					$applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $postauditionval->user_id)->first();
					$checkapply = false;
					if (!empty($applied)) {
						$checkapply = true;
					}

				

					$time_ago = $this->timeago($postauditionval->created_at, true);

					$postauditiondata[] = array(
						"id" => $postauditionval->id,
						"user_id" => $postauditionval->user_id,
						"title" => $postauditionval->title,
						"category" => $postauditionval->category,
						"expire_date" => date("Y-m-d H:i A", strtotime($postauditionval->expire_date)),
						"roles" => $rolesData,
						"union_type" => $postauditionval->union_type,
						"location" => $postauditionval->location,
						"description" => $postauditionval->description,
						"skills" => $postauditionval->skills,
						"status" => $postauditionval->status,
						"compensation" => $postauditionval->compensation,
						"compensation_description" => $postauditionval->compensation_description,
						"website_url" => $postauditionval->website_url,
						"category_name" => $postauditionval->category_name,
						"photography_images" => $postauditionval->photography_images ?? '',
						"created_at" => $postauditionval->created_at,
						"is_fav" => $is_fav,
						"fav_data" => $fav,
						"time_ago" => $time_ago,
						"applied" => $checkapply

					);
				}
			}

			$response['status'] = "true";
			$response['message'] = "Production Crew List";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}


	public function report(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'audition_id' => 'required',
			'description' => 'required',
			'device_type' => 'required',
			// 'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$report = new Report;
			$report->user_id = $data['user_id'];

			$report->post_audition_id = $data['audition_id'];
			$report->description = $data['description'];
			$report->created_at = date("Y-m-d");
			$report->updated_at = date("Y-m-d");

			if ($report->save()) {
				$response['status'] = "true";
				$response['message'] = "Successfully reported";
				return response()->json($response);
			} else {
				$response['status'] = "false";
				$response['message'] = "Something went wrong. Please Try again.";
				return response()->json($response);
			}
		}
	}

	public function timeago($date)
	{
		$timestamp = strtotime($date);

		$strTime = array("second", "minute", "hour", "day", "month", "year");
		$length = array("60", "60", "24", "30", "12", "10");

		$currentTime = time();
		if ($currentTime >= $timestamp) {
			$diff = time() - $timestamp;
			for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
				$diff = $diff / $length[$i];
			}

			$diff = round($diff);
			return $diff . " " . $strTime[$i] . "(s) ago ";
		}
	}



	public function productioncrewappliedlist(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postaudition = DB::table('post_audition_participants')
				->join('production_crews', 'post_audition_participants.post_audition_id', '=', 'production_crews.id')
				->leftJoin('categories', 'production_crews.category', '=', 'categories.id')
				->select('production_crews.*', 'categories.category_name')
				->where('post_audition_participants.user_id', $data['user_id'])
				->where('post_audition_participants.audition_type', '=', '2')
				->where('production_crews.status', '1')
				->orderBy('production_crews.pin_to_top', 'DESC')
				->orderBy('production_crews.id', 'DESC')
				->get();


			$postauditiondata = array();
			foreach ($postaudition as $postauditionval) {

				$rolesData = array();

				$participants = array();

				$roles = $postauditionval->roles;
				$allroles = explode(',', $roles);

				if (!empty($allroles)) {
					foreach ($allroles as $rol) {
						$rol = trim($rol);
						if (is_numeric($rol)) {
							$resrole = Role::where("roles.id", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {

								$applieds = Auditionparticipant::where(["post_audition_id" => $postauditionval->id, 'role_id' => $resrole->id])->where("user_id", $data['user_id'])->count();
								$checkapplys = false;
								if ($applieds > 0) {
									$checkapplys = true;
								}
								$resrole->applies = $checkapplys;

								// get all participants applied to this audition and role

								$participants = DB::table('post_audition_participants')
									->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id')
									->select('users.name', 'users.image', 'users.age', 'users.height', 'users.email', 'users.phone', 'users.pro_member', 'users.status')
									->where('post_audition_participants.role_id', $resrole->id)
									->where('post_audition_participants.post_audition_id', $postauditionval->id)
									->get();

								$resrole->participants = $participants;

								$rolesData[] = $resrole;

							}
						} else {
							$resrole = Role::where("roles.role_title", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {

								$applieds = Auditionparticipant::where(["post_audition_id" => $postauditionval->id, 'role_id' => $resrole->id])->where("user_id", $data['user_id'])->count();
								$checkapplys = false;
								if ($applieds > 0) {
									$checkapplys = true;
								}
								$resrole->applies = $checkapplys;

								// get all participants applied to this audition and role

								$participants = DB::table('post_audition_participants')
									->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id')
									->select('users.name', 'users.image', 'users.age', 'users.height', 'users.email', 'users.phone', 'users.pro_member', 'users.status')
									->where('post_audition_participants.role_id', $resrole->id)
									->where('post_audition_participants.post_audition_id', $postauditionval->id)
									->get();

								$resrole->participants = $participants;

								$rolesData[] = $resrole;
							}
						}
					}
				}

				$applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $data['user_id'])->first();
				$checkapply = false;
				if (!empty($applied)) {
					$checkapply = true;
				}
				$promember = DB::table('users')->select('pro_member')
					->where('id', $postauditionval->user_id)
					->where('pro_member', '1')
					->first();
				$checkpromember = false;
				if (!empty($promember)) {
					$checkpromember = true;
				}

				//get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
					->where("user_id", $data['user_id'])
					->count();

				$is_fav = '0';

				if ($fav > 0) {
					$is_fav = '1';
				}

				$time_ago = $this->timeago($postauditionval->created_at, true);

				$postauditiondata[] = array
				(
					"id" => $postauditionval->id,
					"user_id" => $postauditionval->user_id,
					"audition_title" => $postauditionval->title,
					"category" => $postauditionval->category,
					"expire_date" => date("Y-m-d H:i A", strtotime($postauditionval->expire_date)),
					"union_type" => $postauditionval->union_type,
					"location" => $postauditionval->location,
					"description" => $postauditionval->description,
					"skills" => $postauditionval->skills,
					"status" => $postauditionval->status,
					"category_name" => $postauditionval->category_name,
					"compensation" => $postauditionval->compensation,
					"compensation_description" => $postauditionval->compensation_description,
					"website_url" => $postauditionval->website_url,
					"pin_to_top" => $postauditionval->pin_to_top,
					"created_at" => $postauditionval->created_at,
					"applied" => $checkapply,
					"pro_member" => $checkpromember,
					"roles" => $rolesData,
					"participants" => $participants,
					"is_fav" => $is_fav,
					"time_ago" => $time_ago,
				);

			}

			$response['status'] = "true";
			$response['message'] = "Production Crew Applied List";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}

	public function crewParticipants(Request $request)
	{
		$data = $request->all();
		$rules = [
			'crew_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$postauditionval = DB::table('production_crews')
				->leftJoin('categories', 'production_crews.category', '=', 'categories.id')
				->select('production_crews.*', 'categories.category_name')
				->where('production_crews.id', $data['crew_id'])
				->first();

			$postauditiondata = array();

			if (!empty($postauditionval)) {
				$rolesData = array();
				$participants = array();

				$roles = $postauditionval->roles;

				$allroles = explode(',', $roles);

				if (!empty($allroles)) {
					foreach ($allroles as $rol) {
						$rol = trim($rol);
						if (is_numeric($rol)) {
							$resrole = CrewRole::where("crew_roles.id", $rol)
								->leftJoin('role_types', 'crew_roles.role_type', '=', 'role_types.id')
								->select('crew_roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {
								// get all participants applied to this audition and role

								$participants = DB::table('post_audition_participants')
									->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id')
									->select('users.*')
									->where('post_audition_participants.role_id', $resrole->id)
									->where('post_audition_participants.post_audition_id', $postauditionval->id)
									->get();

								$resrole->participants = $participants;

								$rolesData[] = $resrole;

							}
						} else {
							$resrole = Role::where("roles.role_title", $rol)
								->leftJoin('role_types', 'roles.role_type', '=', 'role_types.id')
								->select('roles.*', 'role_types.title as role_type')
								->first();
							if (!empty($resrole)) {

								// get all participants applied to this audition and role

								$participants = DB::table('post_audition_participants')
									->leftJoin('users', 'post_audition_participants.user_id', '=', 'users.id')
									->select('users.name', 'users.image', 'users.age', 'users.height', 'users.email', 'users.phone', 'users.pro_member', 'users.status')
									->where('post_audition_participants.role_id', $resrole->id)
									->where('post_audition_participants.post_audition_id', $postauditionval->id)
									->get();

								$resrole->participants = $participants;

								$rolesData[] = $resrole;
							}
						}
					}
				}



				$applied = Auditionparticipant::where("post_audition_id", $postauditionval->id)->where("user_id", $postauditionval->user_id)->first();
				$checkapply = false;
				if (!empty($applied)) {
					$checkapply = true;
				}
				$promember = DB::table('users')->select('pro_member')
					->where('id', $postauditionval->user_id)
					->where('pro_member', '1')
					->first();
				$checkpromember = false;
				if (!empty($promember)) {
					$checkpromember = true;
				}

				//get fav data
				$fav = Favourite::where("post_audition_id", $postauditionval->id)
					->count();

				$is_fav = '0';

				if ($fav > 0) {
					$is_fav = '1';
				}

				$time_ago = $this->timeago($postauditionval->created_at, true);

				$postauditiondata = array
				(
					"id" => $postauditionval->id,
					"user_id" => $postauditionval->user_id,
					"audition_title" => $postauditionval->title,
					"category" => $postauditionval->category,
					"expire_date" => date("Y-m-d H:i A", strtotime($postauditionval->expire_date)),
					"union_type" => $postauditionval->union_type,
					"location" => $postauditionval->location,
					"description" => $postauditionval->description,
					"skills" => $postauditionval->skills,
					"status" => $postauditionval->status,
					"category_name" => $postauditionval->category_name,
					"compensation" => $postauditionval->compensation,
					"compensation_description" => $postauditionval->compensation_description,
					"website_url" => $postauditionval->website_url,
					"pin_to_top" => $postauditionval->pin_to_top,
					"created_at" => $postauditionval->created_at,
					"applied" => $checkapply,
					"pro_member" => $checkpromember,
					"roles" => $rolesData,
					"participants" => $participants,
					"is_fav" => $is_fav,
					"time_ago" => $time_ago,
				);
			}

			$response['status'] = "true";
			$response['message'] = "Production crew Participants";
			$response['data'] = $postauditiondata;
			return response()->json($response);
		}
	}


	public function crewAnalytics(Request $request)
	{
		$data = $request->all();
		$rules = [
			'user_id' => 'required',
			'device_type' => 'required',
			//'device_token' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			$response['status'] = "false";
			$response['message'] = $this->validationHandle($validator->messages());
			return response()->json($response);
		} else {
			$user_id = $data['user_id'];

			$postaudition = ProductionCrew::where('production_crews.user_id', $user_id)
				->where('production_crews.expire_date', '>=', date("Y-m-d H:i:s"))
				//->with(['post_auditions_apply','post_auditions_viewed']) 
				->withCount([
					'post_auditions_apply',
					'post_auditions_viewed'
				])
				->orderBy('production_crews.id', 'asc')
				->groupBy(['production_crews.id'])
				->get();

			$totalusers = DB::table('users')
				->where('users.status', '1')
				->where('users.user_type', '1')
				->count();


			$response['status'] = "true";
			$response['message'] = "Production Crew Analytics";
			$response['postaudition'] = $postaudition;
			$response['totalusers'] = $totalusers;
			return response()->json($response);
		}
	}


}

