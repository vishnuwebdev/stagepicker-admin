@extends('admin.mail.default') 
@section('content')
<br>
Hi {{$name}},
<br>
<br>
<div>
You have successfully registered with Acthound, please verify your email and signing in using the credentials at sign up.

 <br />
    <b>Otp : </b> {{$otp}}
</div>
<br>
 
@endsection  

