@extends('admin.mail.default') 
@section('content')
<br>
Hello {{$name}},
<br>
<br>
<div>Administrator Suspended your account<br>
    <br>
    <b>Reason : </b>  {{$reason}}
</div>
<br>
 
@endsection  

