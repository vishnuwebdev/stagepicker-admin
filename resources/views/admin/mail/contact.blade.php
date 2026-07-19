@extends('admin.mail.default') 
@section('content')
<br>
Hello admin,
<br>
<br>
<div>A user sent you a contact request, details are below<br>
    
    <br>
    Name : {{$name}}<br>
    Email Address : {{$email}}<br>
    Message : {{@$messages}}<br>
    
</div>
<br>
 
@endsection  

