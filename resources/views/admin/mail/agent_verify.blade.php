@extends('admin.mail.default') 
@section('content')
<br>
Hi {{$name}},
<br>
<br>
<div>
You have successfully registered with Invesil, please verify your email by clicking the link below and signing in using the credentials at sign up. <a href="{{$link}}">Click here</a> <br />
<br />
Your Login Detais:- </br>
<b> Email : </b> {{$email}}
<br />
<b> Password : </b> {{$password}}
</div>
<br>
 
@endsection  

