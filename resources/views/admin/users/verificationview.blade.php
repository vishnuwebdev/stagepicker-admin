@extends('layouts.admin')
@section('content')
    <div class="right-content">
        <div class="container">
            <div class="row layout-top-spacing layout-spacing">
                <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <!-- <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Company Verification Detail</h4>
                                </div>                                                                
                            </div>
                        </div> -->
                        <div class="widget-content widget-content-area">
                            <div class="stage-profile-details-section profile-details-main-part">
                                <div class="row">
                                    <div class="col-md-12 information-text">
                                        <div class="profile-img profile-images-part">
                                            <?php if(!empty($userDetail->image)){ ?> 
                                            <img src="<?php echo URL::to($userDetail->image); ?>" style="height: 100px;">
                                            <?php }else{ ?>
                                                <img src="{{asset('public/assets/img/stagepicker-logo.png') }}">
                                            <?php } ?>
                                        </div>
                                        <h4>Company Information </h4> 
                                        <div class="profile-information">
                                            <div class="row">
                                                <div class="col-md-6">
                                                        <p><strong>Name : </strong>{{ $userDetail->name }} ( <?php if($userDetail->user_type == 1){ echo "Auditioners"; }elseif($userDetail->user_type == 2){ echo "Producer"; } ?> ) </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Email : </strong>{{ $userDetail->email }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                        <p><strong>Phone : </strong>{{ $userDetail->phone }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Company Verification Information</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Standard Verification Information</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                        <div class="card-body">
                                            @if($companyverification)
                                                <p><strong>Legal Name : </strong>{{ $companyverification->legal_name }}</p>
                                                <p><strong>Email : </strong>{{ $companyverification->email }}</p>
                                                <p><strong>Phone : </strong>{{ $companyverification->phone_no }}</p>
                                                <p><strong>Company Website : </strong>{{ $companyverification->company_website }}</p>
                                                <p><strong>Business Registration : </strong>{{ $companyverification->business_registration }}</p>
                                                <p><strong>Industry of Affiliation : </strong>{{ $companyverification->industry }}</p>
                                                <p><strong>Statement : </strong>{{ $companyverification->statement }}</p>
                                                <p>
                                                    <strong>Business Document : </strong>
                                                    <a class="btn btn-outline-info" href="{{ url($companyverification->document) }}" target="_blank">
                                                        Business Document
                                                    </a>
                                                </p>
                                                <?php if($companyverification->status == 1){  ?>
                                                    <p><strong>Verification Status : </strong>Approve</p>
                                                <?php }else if($companyverification->status == 0){ ?>
                                                    <p><strong>Verification Status : </strong>Pending</p>
                                                <?php }else if($companyverification->status == 2){ ?>
                                                    <p><strong>Verification Status : </strong>Reject</p>
                                                <?php }else{ ?>
                                                    <div class="profile-img">
                                                        <p><strong>Verification Status : </strong></p>
                                                        <form action="{{ url('admin/update-verification',$companyverification->id)  }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="verification_id" value="<?php echo  $companyverification->id; ?>">
                                                            <input type="hidden" name="verification_type" value="c">
                                                            <div class="form-group mb-4">
                                                                <select class="form-control" name="verification_status">
                                                                    <option value="0" <?php if($companyverification->status == 0){ echo "selected";} ?> >Pending</option>
                                                                    <option value="1" <?php if($companyverification->status == 1){ echo "selected";} ?> >Approve</option>
                                                                    <option value="2" <?php if($companyverification->status == 2){ echo "selected";} ?> >Reject</option>
                                                                </select>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary mt-3">Update</button>
                                                        </form>
                                                    </div>
                                                <?php } ?>
                                            @else
                                                <p><strong> Not Record Found</strong></p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                        <div class="card-body">
                                            @if($standardVerification)
                                                <p>
                                                    <strong>Legal Name : </strong>{{ $standardVerification->legal_name }}
                                                </p>
                                                <p><strong>Email : </strong>{{ $standardVerification->email }}</p>
                                                <p><strong>Phone : </strong>{{ $standardVerification->phone_no }}</p>
                                                <p><strong>Statement : </strong>{{ $standardVerification->statement }}</p>
                                                <p><strong>Photo : </strong></p>
                                                <div class="row">
                                                    <div class="col-md-4 profile-img profile-images-part">
                                                        <p><strong>Front Photo : </strong></p>
                                                        <?php if(!empty($standardVerification->front_photo)){ ?>
                                                            <a href="{{ URL::to($standardVerification->front_photo) }}" target="_blank">
                                                            <img src="<?php echo URL::to($standardVerification->front_photo); ?>" style="height: 100px;">
                                                            </a>
                                                        <?php }else{ ?>
                                                                <img src="{{asset('public/assets/img/stagepicker-logo.png') }}">
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-md-4 profile-img profile-images-part">
                                                        <p><strong>Back Photo : </strong></p>
                                                        <?php if(!empty($standardVerification->back_photo)){ ?> 
                                                            <a href="{{ URL::to($standardVerification->back_photo) }}" target="_blank">
                                                            <img src="<?php echo URL::to($standardVerification->back_photo); ?>" style="height: 100px;">
                                                            </a>
                                                        <?php }else{ ?>
                                                            <img src="{{asset('public/assets/img/stagepicker-logo.png') }}">
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 profile-img profile-images-part">
                                                        <p><strong>Real Time Photo : </strong></p>
                                                        <?php if(!empty($standardVerification->real_time_photo)){ ?> 
                                                            <a href="{{ URL::to($standardVerification->real_time_photo) }}" target="_blank">
                                                                <img src="<?php echo URL::to($standardVerification->real_time_photo); ?>" style="height: 100px;">
                                                            </a>
                                                        <?php }else{ ?>
                                                            <img src="{{asset('public/assets/img/stagepicker-logo.png') }}">
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <p>
                                                    <strong>Document : </strong>
                                                    <a class="btn btn-outline-info" href="{{ url($standardVerification->document) }}" target="_blank">
                                                        View
                                                    </a>
                                                </p>
                                                <p>
                                                    <strong>Resume : </strong>
                                                    <a class="btn btn-outline-info" href="{{ url($standardVerification->resume) }}" target="_blank">View</a>
                                                </p>
                                                <p>
                                                    <strong>Portfolio Link : </strong>
                                                    {{ $standardVerification->portfolio }}
                                                </p>
                                                <p>
                                                    <strong>Agency Affiliation : </strong>{{ $standardVerification->agency }}
                                                </p>
                                                <?php  if($standardVerification->status == 1){  ?>
                                                    <p><strong>Verification Status : </strong>Approve</p>
                                                <?php }else if($standardVerification->status == 0){ ?>
                                                    <p><strong>Verification Status : </strong>Pending</p>
                                                <?php }else if($standardVerification->status == 2){ ?>
                                                        <p><strong>Verification Status : </strong>Reject</p>
                                                <?php }else{ ?>
                                                    <div class="profile-img">
                                                        <p><strong>Verification Status : </strong></p>
                                                        <form action="{{ url('admin/update-verification',$standardVerification->id)  }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="verification_id" value="<?php echo  $standardVerification->id; ?>">
                                                            <input type="hidden" name="verification_type" value="s">
                                                            <div class="form-group mb-4">
                                                                <select class="form-control" name="verification_status">
                                                                    <option value="0" <?php if($standardVerification->status == 0){ echo "selected";} ?> >Pending</option>
                                                                    <option value="1" <?php if($standardVerification->status == 1){ echo "selected";} ?> >Approve</option>
                                                                    <option value="2" <?php if($standardVerification->status == 2){ echo "selected";} ?> >Reject</option>
                                                                </select>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary mt-3">Update</button>
                                                        </form>
                                                </div>
                                                <?php } ?>
                                            @else
                                                <p><strong> Not Record Found</strong></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection