@extends('layouts.admin')
@section('content')
    <div class="right-content">
        <div class="container">
            <div class="row layout-top-spacing layout-spacing">
                <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
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
                                                <form action="{{ url('admin/update-verification-data')  }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="verification_id" value="<?php echo  $companyverification->id; ?>">
                                                    <input type="hidden" name="verification_type" value="c">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                            <label for="legal_name">Legal Name</label>
                                                            <input type="text" class="form-control" name="legal_name" id="legal_name" placeholder="Enter Legal Name" value="{{ old('legal_name', isset($companyverification) ? $companyverification->legal_name : '') }}">
                                                            @if ($errors->has('legal_name'))
                                                                <span class="text-danger">{{ $errors->first('legal_name') }}</span>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                            <label for="email">email</label>
                                                            <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ old('email', isset($companyverification) ? $companyverification->email : '') }}">
                                                            @if ($errors->has('email'))
                                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                            <label for="phone_no">Phone</label>
                                                            <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="Enter Phone" value="{{ old('phone_no', isset($companyverification) ? $companyverification->phone_no : '') }}">
                                                            @if ($errors->has('phone_no'))
                                                                <span class="text-danger">{{ $errors->first('phone_no') }}</span>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                            <label for="company_website">Company Website</label>
                                                            <input type="text" class="form-control" name="company_website" id="company_website" placeholder="Enter company ebsite" value="{{ old('company_website', isset($companyverification) ? $companyverification->company_website : '') }}">
                                                            @if ($errors->has('company_website'))
                                                                <span class="text-danger">{{ $errors->first('company_website') }}</span>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                            <label for="business_registration">Business Registration</label>
                                                            <input type="text" class="form-control" name="business_registration" id="business_registration" placeholder="Enter Business registration" value="{{ old('business_registration', isset($companyverification) ? $companyverification->business_registration : '') }}">
                                                            @if ($errors->has('business_registration'))
                                                                <span class="text-danger">{{ $errors->first('business_registration') }}</span>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                            <label for="industry">Industry of Affiliation</label>
                                                            <input type="text" class="form-control" name="industry" id="industry" placeholder="Enter industry" value="{{ old('industry', isset($companyverification) ? $companyverification->industry : '') }}">
                                                            @if ($errors->has('industry'))
                                                                <span class="text-danger">{{ $errors->first('industry') }}</span>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                            <label for="statement">Statement</label>
                                                            <input type="text" class="form-control" name="statement" id="statement" placeholder="Enter Phone" value="{{ old('statement', isset($companyverification) ? $companyverification->statement : '') }}">
                                                            @if ($errors->has('statement'))
                                                                <span class="text-danger">{{ $errors->first('statement') }}</span>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                                <label for="document">Business Document</label>
                                                                <input type="file" class="form-control" name="document" id="document" />
                                                                @if (File::exists($companyverification->document))
                                                                    <a class="btn btn-outline-info" href="{{ url($companyverification->document) }}" target="_blank">
                                                                        View
                                                                    </a>
                                                                @endif
                                                                @if ($errors->has('document'))
                                                                    <span class="text-danger">{{ $errors->first('document') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                                <label for="document">Verification Status</label>
                                                                <select class="form-control" name="verification_status">
                                                                    <option value="0" <?php if($companyverification->status == 0){ echo "selected";} ?> >Pending</option>
                                                                    <option value="1" <?php if($companyverification->status == 1){ echo "selected";} ?> >Approve</option>
                                                                    <option value="2" <?php if($companyverification->status == 2){ echo "selected";} ?> >Reject</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                                                </form>
                                            @else
                                                <p>
                                                    <strong> Not Record Found</strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                        <div class="card-body">
                                            @if($standardVerification)
                                            <form action="{{ url('admin/update-verification-data')  }}" method="POST" enctype="multipart/form-data">
                                                @csrf    
                                                <input type="hidden" name="verification_id" value="<?php echo  $standardVerification->id; ?>">
                                                <input type="hidden" name="verification_type" value="s">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="legal_name">Legal Name</label>
                                                        <input type="text" class="form-control" name="legal_name" id="legal_name" placeholder="Enter Legal Name" value="{{ old('legal_name', isset($standardVerification) ? $standardVerification->legal_name : '') }}">
                                                        @if ($errors->has('legal_name'))
                                                            <span class="text-danger">{{ $errors->first('legal_name') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="email">email</label>
                                                        <input type="text" readonly class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ old('email', isset($standardVerification) ? $standardVerification->email : '') }}">
                                                        @if ($errors->has('email'))
                                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="phone_no">Phone</label>
                                                        <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="Enter Phone" value="{{ old('phone_no', isset($standardVerification) ? $standardVerification->phone_no : '') }}">
                                                        @if ($errors->has('phone_no'))
                                                            <span class="text-danger">{{ $errors->first('phone_no') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="statement">Statement</label>
                                                        <input type="text" class="form-control" name="statement" id="statement" placeholder="Enter Statement" value="{{ old('statement', isset($standardVerification) ? $standardVerification->statement : '') }}">
                                                        @if ($errors->has('statement'))
                                                            <span class="text-danger">{{ $errors->first('statement') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="document">Document</label>
                                                        <input type="file" class="form-control" name="document" id="document" />
                                                         @if (File::exists($standardVerification->document))
                                                            <a class="btn btn-outline-info" href="{{ url($standardVerification->document) }}" target="_blank">
                                                                View
                                                            </a>
                                                        @endif
                                                        @if ($errors->has('document'))
                                                            <span class="text-danger">{{ $errors->first('document') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="resume">Resume</label>
                                                        <input type="file" class="form-control" name="resume" id="resume" />
                                                        @if (File::exists($standardVerification->resume))
                                                            <a class="btn btn-outline-info" href="{{ url($standardVerification->resume) }}" target="_blank">View</a>
                                                        @endif
                                                        @if ($errors->has('resume'))
                                                            <span class="text-danger">{{ $errors->first('resume') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="agency">Agency</label>
                                                        <input type="text" class="form-control" name="agency" id="agency" placeholder="Enter agency" value="{{ old('agency', isset($standardVerification) ? $standardVerification->agency : '') }}">
                                                        @if ($errors->has('agency'))
                                                            <span class="text-danger">{{ $errors->first('agency') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                        <label for="portfolio">Portfolio</label>
                                                        <input type="text" class="form-control" name="portfolio" id="portfolio" placeholder="Enter portfolio" value="{{ old('portfolio', isset($standardVerification) ? $standardVerification->portfolio : '') }}">
                                                        @if ($errors->has('portfolio'))
                                                            <span class="text-danger">{{ $errors->first('portfolio') }}</span>
                                                        @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 profile-img">
                                                        <div class="form-group mb-4">
                                                            <p><strong>Front Photo : </strong></p>
                                                            <input type="file" class="form-control" name="front_photo" id="front_photo" />
                                                            @if (File::exists($standardVerification->front_photo))
                                                                <a href="{{ URL::to($standardVerification->front_photo) }}" target="_blank">
                                                                    <img src="<?php echo URL::to($standardVerification->front_photo); ?>" style="height: 100px;">
                                                                </a>
                                                            @endif
                                                                   
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 profile-img">
                                                        <div class="form-group mb-4">
                                                            <p><strong>Back Photo : </strong></p>
                                                            <input type="file" class="form-control" name="back_photo" id="back_photo" />
                                                             @if (File::exists($standardVerification->back_photo))
                                                                <a href="{{ URL::to($standardVerification->back_photo) }}" target="_blank">
                                                                    <img src="<?php echo URL::to($standardVerification->back_photo); ?>" style="height: 100px;" />
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 profile-img">
                                                        <div class="form-group mb-4">
                                                            <p><strong>Real Time Photo : </strong></p>
                                                            <input type="file" class="form-control" name="real_time_photo" id="real_time_photo" />
                                                            @if (File::exists($standardVerification->real_time_photo))
                                                                <a href="{{ URL::to($standardVerification->real_time_photo) }}" target="_blank">
                                                                    <img src="<?php echo URL::to($standardVerification->real_time_photo); ?>" style="height: 100px;" />
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group mb-4">
                                                            <label for="document">Verification Status</label>
                                                            <select class="form-control" name="verification_status">
                                                                <option value="0" <?php if($standardVerification->status == 0){ echo "selected";} ?> >Pending</option>
                                                                <option value="1" <?php if($standardVerification->status == 1){ echo "selected";} ?> >Approve</option>
                                                                <option value="2" <?php if($standardVerification->status == 2){ echo "selected";} ?> >Reject</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                            
                                                    
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-3">Update</button>
                                            </form>
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