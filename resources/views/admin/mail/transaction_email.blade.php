@extends('admin.mail.default') 
@section('content')
<br>
Hi {{$name}},
<br>
<br>
<div>
You have successfully transfer amount.
<p>Transaction Type : {{$transaction_type}}</p>
<p>Amount : ${{$amount}}</p>
<p>Transaction Refrence No. : {{$transaction_refrence}}</p>

 <br />

</div>
<br>
 
@endsection  

