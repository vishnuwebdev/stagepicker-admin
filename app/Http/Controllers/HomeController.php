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
use App\FireStore\FireStoreApiClient;
use App\FireStore\FireStoreDocument;

class HomeController extends Controller {

    /**
     * about rander about page
     * @param  none
     * @return none
     */
    
  // delete lead afeter 7 days
  
  public function terms(Request $request) {
          echo "terms";
    exit;
    }
  
  public function privacy(){
    echo "privacy";
    exit;
          
  }
  
    public function firestoretest(){
        $firestore = new FireStoreApiClient(
                             'stage-picker', 'AIzaSyB21YVALjKHU_Q3S76qT9vdH908esWbTJI'
                            );
        
        //To Add New Document Code Start 
        
        /* $document = new FireStoreDocument();
        $document->setString('name','test');
        $document->setInteger('test',1);
        print_r( $firestore->addDocument('tobetest',$document)); */
        
        //To Add New Document Code End 
        
        // To Delete Any Document From Firestore 
        
        // $firestore->deleteDocument('tobetest', '1-2')
        
        // To Add New Document Code End 
        echo "firestoretest";
        exit;
              
    }
  
  

}
