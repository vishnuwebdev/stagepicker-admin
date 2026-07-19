@extends('admin.mail.default') 
@section('content')
<br>
Hello {{$name}},
<br>
<br>
<div>Report Status:
<br /> 
<br /> 
    <table border="1" cellpadding="5" cellspacing="0" style="border 1px solid #ccc;">
        <thead>
            <tr>
            <th>New Leads</th>
                <th>Good leads</th>
                <th>Can't Help Now</th>
                <th>Sold</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                    <td><?php echo ($leadData['newLead'])?$leadData['newLead']:'N/A'; ?></td>
                <td><?php echo ($leadData['followLead'])?$leadData['followLead']:'N/A'; ?></td>
                <td><?php echo ($leadData['cantHelpLead'])?$leadData['cantHelpLead']:'N/A'; ?></td>
                <td><?php echo ($leadData['soldLead'])?$leadData['soldLead']:'N/A'; ?></td>
            </tr>
        </tbody>
    </table>
<br />
    <a href="{{$link}}">Click here</a>
</div>
<br>
 
@endsection  

