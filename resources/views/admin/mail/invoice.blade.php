@extends('admin.mail.default') 
@section('content')
<br>
Hi {{$name}},
<br>
<br>
<div>
<p>Your transaction has been created.</p>    
<p>Thanks for lodging a new transaction! We will begin processing it shortly</p>
<br />
<p>Client Name : {{$client_name}}</p>
<p>Transaction Type : {{$transaction_type}}</p>
<p>Currency : {{$currency}}</p>
<p>Amount : {{$amount}}</p>
<p>Transaction Created : {{$transaction_date}}</p>
<p>Transaction Refrence No. : {{$transaction_refrence}}</p>
<p>Status : {{$status}}</p>
<br />

</div>
<br>
 
@endsection  

