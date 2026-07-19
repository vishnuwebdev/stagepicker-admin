@extends('admin.mail.default') 
@section('content')
<br>
Hello,
<br>
<br>
<div>You have made a forgot password request. Please click below link to change password<br>
    <br>
    <a href="{{$link}}">Click Here</a>
</div>
<br>
 
@endsection  

