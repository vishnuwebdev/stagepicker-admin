@extends('admin.mail.default') 
@section('content')
<br>
Hello {{$name}},
<br>
<br>
<div>Reminder Status:
<br />
<br />
<br /> 
Lead Name: {{$leadData->name}}
<br /> 
Phone Number: {{$leadData->phone_number}}
<br /> 
Reminder Time : {{$leadData->date}} {{$leadData->time}} 
<br />
<br />
    <a href="{{$link}}">Click here</a>
</div>
<br>
 
@endsection  

