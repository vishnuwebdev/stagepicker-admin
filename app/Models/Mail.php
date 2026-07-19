<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    // public static $from="abstractsoftwebtesting@gmail.com";
    public static $from= "noreply@acthndemail.com";
    // public static $from= "noreply@acthound.com";
    
    public static function sendMail($template,$subject,$data,$user,$files= array()){

        $res = \Mail::send($template, $data, function($message) use($user,$subject,$files) {
            $message->to($user->email, $user->full_name)->subject($subject);
            // $message->from(self::$from,'Acthound');
            $message->from(config('mail.from.address'), config('mail.from.name'));
            if(count($files) > 0) {
                foreach($files as $file) {
                    $message->attach(@url($file['document']));
                }
            }
        });

        if (count(\Mail::failures()) > 0) {
            return \Mail::failures();   // return array of failed emails
        }

        return true;
    }
    
    public static function sendMailToAdmin($template,$subject,$data,$user,$files= array()){
       
            return \Mail::send($template, $data, function($message) use($user,$subject,$files) {
            $message->to($user->email, $user->username)->subject($subject);
            // $message->from(self::$from,config('app.name')); 
            $message->from(config('mail.from.address'), config('mail.from.name'));
               if(count($files) > 0) {
                        foreach($files as $file) {
                            $message->attach(@url($file['document']));
                        }
                    }
      });
    }
}
