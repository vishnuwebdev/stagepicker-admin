<?php
namespace App\Lib;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
/**
 * 
 * 
 * This Library use for image upload and resizing.
 *  
 * 
 **/

class Uploader
{
    
    public static function doUpload($file,$path,$thumb=false,$pre=false,$mime=null){
        $response = [];
        $image = $file;
        $file = $pre.time().rand(100000,10000000).'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path().$path;
        //dd($destinationPath);
        if($uploaded = $image->move($destinationPath, $file)){
			if($thumb==true){
				if(strstr($mime, "image/")){
                $thumbPath = public_path().$path.'thumb/'.$file;
                chmod($uploaded->getRealPath(), 0777);
                if (!file_exists(public_path().$path.'thumb/')) {
                    mkdir(public_path().$path.'thumb/', 0777, true);
                }
                /*$cropInfo = Image::make($uploaded->getRealPath())->resize(250, 300);
                $cropInfo->orientate();
                $cropInfo->save($thumbPath);*/
				}
            }
            $response['status']     = true;
            $response['file']       = "public".$path.$file;
            $response['thumb_path'] = "public".$path.'thumb/'.$file;
            $response['file_name']  = $file;
            $response['path']       = $path;
        }else{
            $response['status']     = false;
        }
        return $response;

    }
    
      public static function doProfileUpload($userId = '',$file,$path,$thumb=false,$pre=false,$mime=null){
        $response = [];
        $image = $file;
        $file = $userId.'.'.$image->getClientOriginalExtension();
        //~ $destinationPath = public_path().$path;
        $path = '/uploads/user/';
        $destinationPath = public_path().$path;
        //dd($destinationPath);
        if($uploaded = $image->move($destinationPath, $file)){
            if($thumb==true){
				if(strstr($mime, "image/")){
                 $thumbPath = public_path().$path.'thumb/'.$file;
                chmod($uploaded->getRealPath(), 0777);
				if (!file_exists(public_path().$path.'thumb/')) {
                    mkdir(public_path().$path.'thumb/', 0777, true);
                }
                /*$cropInfo = Image::make($uploaded->getRealPath())->resize(100, 100);
                $cropInfo->orientate();
                $cropInfo->save($thumbPath);*/
				}
            }
            $response['status']     = true;
            $response['file']       = "public".$path.$file;
            $response['thumb_path'] = "public".$path.'thumb/'.$file;
            $response['file_name']  = $file;
            $response['path']       = $path;
        }else{
            $response['status']     = false;
        }
        return $response;

    }
    
}
