<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Postaudition;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;
use \Session;
use DB;
use Socialite;
use Illuminate\Support\Facades\Auth;
class AuditionExpireCheckController extends Controller {

    /**
     * about rander about page
     * @param  none
     * @return none
     */
    
  // delete lead afeter 7 days
  public function checkexpireaudition(){
    $postaudition =   DB::select("SELECT * FROM post_auditions WHERE STR_TO_DATE(expire_date, '%d-%m-%Y') < CURRENT_TIME");
    
    foreach($postaudition as $postauditionval){
        $expirepostaudition = Postaudition::find($postauditionval->id);
        $expirepostaudition->status = '0';
        $expirepostaudition->save();
    }
          
  }
  public function notifybeforeexpire(){
      
    $postaudition =   DB::select("SELECT * FROM post_auditions WHERE status = '1' ");
    
    
          
  }
  
  
  

}
