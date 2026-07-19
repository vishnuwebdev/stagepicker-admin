@extends('layouts.admin')
@section('content')
<link href="https://designreset.com/cork/ltr/demo4/assets/css/users/account-setting.css" rel="stylesheet" type="text/css" />
            <div class="layout-px-spacing">                
                <div class="account-settings-container layout-top-spacing">
                    <div class="account-content">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success mb-4" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                <strong>{{ $message }}</strong></button>
                            </div>
                        @endif
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger mb-4" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                <strong>{{ $message }}</strong></button>
                            </div>
                        @endif
                        <div class="scrollspy-example" data-spy="scroll" data-target="#account-settings-scroll" data-offset="-100">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <form id="general-info" class="section general-info" action="{{ url('admin/updatepass') }}">
                                        @csrf
                                        <div class="info">
                                            <h6 class="">Password Change</h6>
                                            <div class="row">
                                                <div class="col-lg-11 mx-auto">
                                                    <div class="row">
                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-md-6 mt-6">
                                                            <div class="forms"> 
                                                                    <div class="form-group">
                                                                        <label for="old_password">Current Password</label>
                                                                        <input type="password" class="form-control mb-4" id="old_password" name="old_password" >
                                                                        @if ($errors->has('old_password'))
                                                                            <span class="text-danger">{{ $errors->first('old_password') }}</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="new_password">New Password</label>
                                                                        <input type="password" class="form-control mb-4" id="new_password" name="new_password" >
                                                                        @if ($errors->has('new_password'))
                                                                            <span class="text-danger">{{ $errors->first('new_password') }}</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="cnfrmpassword">Re-Enter New Password</label>
                                                                        <input type="password" class="form-control mb-4" id="cnfrmpassword" name="cnfrmpassword">
                                                                        @if ($errors->has('cnfrmpassword'))
                                                                            <span class="text-danger">{{ $errors->first('cnfrmpassword') }}</span>
                                                                        @endif
                                                                    </div>
                                                                <div class="form-group">
                                                                     <button id="multiple-messages" class="btn btn-dark">Save Changes</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
           
@endsection
