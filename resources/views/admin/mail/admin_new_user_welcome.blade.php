@extends('admin.mail.default') 
@section('content')
<br>
Hello,
<br>
<br>
<div><?php
        if($update) {

             echo 'Your Login Details has been changed. New login credentials is as follows.<br>';
        } else {
           echo 'You have successfully registered with Acthound as Staff. Login credentials is as follows.<br>';
        }
     ?>
    <br>
     Email : {{$email}}
     <br>
     Password : {{$password}}
     <br>
    <a href="{{$link}}">Login Here</a>
</div>
<br>
 
@endsection  

