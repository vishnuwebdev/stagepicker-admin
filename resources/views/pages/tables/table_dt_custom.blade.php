@extends('layouts.app')

@section('content')

            <div class="layout-px-spacing">

                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>User List</h4>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('forms/layouts') }}" class="btn btn-primary">Add User</a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2  table-hover">
                                        <thead>
                                            <tr>
                                                <th class="checkbox-column"> Record Id </th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Mobile No.</th>
                                                <th class="text-center">Image</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="checkbox-column"> 1 </td>
                                                <td>Jane</td>
                                                <td>Lamb</td>
                                                <td>johndoe@yahoo.com</td>
                                                <td>555-555-5555</td>
                                                <td class="text-center">
                                                    <span><img src="{{asset('storage/img/90x90.jpg')}}" class="rounded-circle profile-img" alt="avatar"></span>
                                                </td>
                                                <td class="text-center"><span class="shadow-none badge badge-primary">Approved</span></td>
                                                <td class="text-center"><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></td>
                                            </tr>
                                            <tr>
                                                <td class="checkbox-column"> 2 </td>
                                               <td>Linda</td>
                                                <td>Nelson</td>
                                                <td>linda@gmail.com</td>
                                                <td>555-555-6666</td>
                                                <td class="text-center">
                                                    <span><img src="{{asset('storage/img/90x90.jpg')}}" class="rounded-circle profile-img" alt="avatar"></span>
                                                </td>
                                                <td class="text-center"><span class="shadow-none badge badge-warning">Suspended</span></td>
                                                <td class="text-center"><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></td>
                                            </tr>
                                            <tr>
                                                <td class="checkbox-column"> 3 </td>
                                                <td>Kelly</td>
                                                <td>Young</td>
                                                <td>kelly@live.com</td>
                                                <td>777-555-5555</td>
                                                <td class="text-center">
                                                    <span><img src="{{asset('storage/img/90x90.jpg')}}" class="rounded-circle profile-img" alt="avatar"></span>
                                                </td>
                                                <td class="text-center"><span class="shadow-none badge badge-danger">Closed</span></td>
                                                <td class="text-center"><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></td>
                                            </tr>
                                            <tr>
                                                <td class="checkbox-column"> 4 </td>
                                                <td>Vincent</td>
                                                <td>Carpenter</td>
                                                <td>vinnyc@outlook.com</td>
                                                <td>555-666-5555</td>
                                                <td class="text-center">
                                                    <span><img src="{{asset('storage/img/90x90.jpg')}}" class="rounded-circle profile-img" alt="avatar"></span>
                                                </td>
                                                <td class="text-center"><span class="shadow-none badge badge-primary">Approved</span></td>
                                                <td class="text-center"><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></td>
                                            </tr>
                                            <tr>
                                                <td class="checkbox-column"> 5 </td>
                                                <td>Lila</td>
                                                <td>Perry</td>
                                                <td>lila@adobe.com</td>
                                                <td>444-444-4444</td>
                                                <td class="text-center">
                                                    <span><img src="{{asset('storage/img/90x90.jpg')}}" class="rounded-circle profile-img" alt="avatar"></span>
                                                </td>
                                                <td class="text-center"><span class="shadow-none badge badge-warning">Suspended</span></td>
                                                <td class="text-center"><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></td>
                                            </tr>
                                            <tr>
                                                <td class="checkbox-column"> 6 </td>
                                                <td>Traci</td>
                                                <td>Lopez</td>
                                                <td>traci@gmail.com</td>
                                                <td>111-111-1111</td>
                                                <td class="text-center">
                                                    <span><img src="{{asset('storage/img/90x90.jpg')}}" class="rounded-circle profile-img" alt="avatar"></span>
                                                </td>
                                                <td class="text-center"><span class="shadow-none badge badge-danger">Closed</span></td>
                                                <td class="text-center"><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></td>
                                            </tr>
                                            <tr>
                                                <td class="checkbox-column"> 7 </td>
                                                <td>Nia</td>
                                                <td>Hillyer</td>
                                                <td>niaHill@yahoo.com</td>
                                                <td>111-666-1111</td>
                                                <td class="text-center">
                                                    <span><img src="{{asset('storage/img/90x90.jpg')}}" class="rounded-circle profile-img" alt="avatar"></span>
                                                </td>
                                                <td class="text-center"><span class="shadow-none badge badge-primary">Approved</span></td>
                                                <td class="text-center"><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
@endsection