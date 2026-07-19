<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;
//use Illuminate\Support\Facades\Log;
use App\Http\Traits\ValidationsTrait;
use App\Models\User;
use Illuminate\Http\Request;


class ApiController extends BaseController { 

//    use ValidationsTrait;
    
    public $status = false;
    public $user_status = false;
    public $message = '';
    public $responseData = array();
    public $code = 200;
    public $errors = '';
    public $log = '';
    public $json = true;
    public $app_status = ["status" => "testing", //production , 
        "url" => "",
        "api_version" => "1.0",
        "block" => false,
        "message" => "This is testing enviroment"
    ];

    function __construct() {        
        //\Log::info('Request Url: '. $_SERVER['REQUEST_URI']);
        //Log::info('Request params: ', Input::all());
          if (request('userid') && request('userid') != '-1') {
            $u = User::where('userid', request('userid'))->where('status', 1)->first();
            if (!$u) {
                $this->message = 'deactive';
                die;
            }
        }
    }
    
    
    function response($data){
        //return response()->json($this->arrayHandleFun($data),200); 
        $data = $this->arrayHandleFun($data);
        // This header pass for allow origin control.
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Origin: http://demo2server.com/petjournal/api");
        header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization, X-CSRF-Token');
        header('Access-Control-Allow-Credentials: true');         
        echo json_encode($data); die();
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
    
    

    function validate($validator) {
        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                if (!isset($firstError))
                    $firstError = $messages[0];
                $error[$field_name] = $messages[0];
            }
            $this->message = $firstError;
            $this->errors = $error;

            return false;
        }
        else {
            return true;
        }
    }
    

    /* function jsonView() {
        $resp = [
            'status' => $this->status,
            'user_status' => $this->user_status,
            'code' => $this->code,
            'responseData' => (object) $this->responseData,
            'requestData' => $_REQUEST,
            'msg' => $this->message,
            'errors' => $this->errors,
            'app_status' => $this->app_status,
            'log' => $this->log,
        ];
//        $this->addService($resp);
        echo json_encode($resp);
        die;
    } */

    public function addService($response) {

        $service = include base_path() . "/services/services_info.php";

        $_GET['type'] = strtok(str_replace("/", "_", str_replace("/ayna/api/", "", $_SERVER['REQUEST_URI'])), "?");
        //unset($service[$_GET['type']]);
        $data = [];
        $data[$_GET['type']]['url'] = strtok($_SERVER['REQUEST_URI'], "?");
        $data[$_GET['type']]['files'] = $_FILES;
        $data[$_GET['type']]['Method'] = $_SERVER['REQUEST_METHOD'];
        $data[$_GET['type']]['get_data'] = $_GET;
        $data[$_GET['type']]['logsss'] = "aam";
        $data[$_GET['type']]['log'] = var_export($_REQUEST, true);
        $data[$_GET['type']]['post_data'] = $_POST;
//            $response=array_keys($response['responseData']);
        $data[$_GET['type']]['response'] = array_keys($response);
        if (!isset($service[$_GET['type']]['locked']) || !$service[$_GET['type']]['locked']) {

            $data[$_GET['type']]['locked'] = false;
            $service[$_GET['type']] = $data[$_GET['type']];
        } else {
            $service[$_GET['type']]['last'] = [];
            $service[$_GET['type']]['last'] = $data[$_GET['type']];
        }
//            pr($service);die;
        $log = $service[$_GET['type']];
        $log["response_data"] = $response;

        file_put_contents(base_path() . "/services/services_info.php", "<?php return " . var_export($service, true) . " ?>");
//        echo dirname(dirname(__DIR__)) . "/Template/Api/services/log/{$_GET['type']}.php";
//        file_put_contents(dirname(dirname(__DIR__)) . "/Template/Api/services/log/{$_GET['type']}.txt", var_export($log, true)."\n\n",FILE_APPEND);
    }

    
    public function recordNotFound($type = 'obj', $msg = "Record not found")
    {
        $this->status = false;
        $this->message = $msg;
        if($type == 'array')
            $this->responseData["data"] = [];
        else
            $this->responseData["data"] = (object)[];
    }
    
    public function paramNotMatch()
    {
        $this->status = false;
        $this->message = "Invalid Credentials";
        $this->responseData["data"] = (object)[];
    }
    
    public function serverError($message = '')
    {
        $this->status = false;
        $this->message = "Server Error";
        $this->responseData["errors"] = $message;
    }
    
    
    
    
    function __destruct() {
        $this->jsonView();
    }
    function socketResponse($data) {
        header('Content-Type: application/json');

        //return response()->json(['data' => 'Resource not found'],200); 
        //$data = $this->arrayHandleFun($data);
        echo json_encode($data);
        die();
    }
   

}