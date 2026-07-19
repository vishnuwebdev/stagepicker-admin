@extends('admin.mail.default') 
@section('content')
<br>
Hello,
<br>
<br>
<div>You have made a forgot password request. Please use this otp for change password<br>
    <br>
    <b>Otp : </b>  {{$otp}}
</div>
<br>
 
@endsection  

