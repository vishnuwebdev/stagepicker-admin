<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class AdminController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $setLayout = 'Admin';
    
    function __construct(){

     $action     = \Route::getFacadeRoot()->currentRouteAction();
     $actionParam= explode("\\",$action);
     $route      = explode("@",$actionParam[count($actionParam)-1]);
     $controller = str_replace("Controller","",$route[0]);   
     $this->setLayout=$this->setLayout.".{$controller}.";
        
    }
    function make($path,$arry=[]){
        
//        echo ($this->setLayout.$path);
  
      $arry['pageContent']= \View::make($this->setLayout.$path,$arry);
		return \View::make("Admin.layout.default",$arry);
    }
     public function getWhere($data, $where='') {
        if(!empty($where) && is_array($where)){
            foreach ($where as $key=>$val){
                if(!empty($val)){
                    $data->where($key, 'like', "%".$val."%");
                }                
            }
        }
        return $data;
    }
}
 
