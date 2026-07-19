@extends('layouts.admin')
@section('content')
<div class="containers">
                <div class="container">
                    <div class="row">
                        <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4>Edit Role</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/update-role',$role->id)  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="role_id" value="<?php echo  $role->id; ?>">
                                        <div class="form-group mb-4">
                                            <label for="role_title">Role Title</label>
                                            <input type="text" class="form-control" name="role_title" id="role_title" placeholder="Enter Role Title" value="{{ old('role_title', isset($role) ? $role->role_title : '') }}" required>
                                            @if ($errors->has('role_title'))
                                                <span class="text-danger">{{ $errors->first('role_title') }}</span>
                                            @endif
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="role_type">Role Type</label>
                                            <select class="form-control" id="role_type" name="role_type">
												<option value="">Select Role Type</option> 
												@if ($roletype->count())
                    
													@foreach($roletype as $tag)
														<option value="{{ $tag->id }}"  
														@if($role->role_type == $tag->id) selected @endif > {{ $tag->title }}</option>    
													@endforeach
													
												@endif
												
											</select>
                                            @if ($errors->has('role_type'))
                                                <span class="text-danger">{{ $errors->first('role_type') }}</span>
                                            @endif
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="gender">Gender</label>
                                            <select class="form-control" id="gender" name="gender">
												<option value="">Select Gender</option> 
												 
												<option value="Male"  
														@if($role->gender == 'Male') selected @endif > Male</option> 
														
												<option value="Female"  
														@if($role->gender == 'Female') selected @endif > Female</option>  
												
											</select>
                                            @if ($errors->has('gender'))
                                                <span class="text-danger">{{ $errors->first('gender') }}</span>
                                            @endif
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="age_min">Age Min</label>
                                            <input type="number" class="form-control" name="age_min" id="age_min" placeholder="Enter Age Min" max="100" value="{{ old('age_min', isset($role) ? $role->age_min : '') }}">
                                            @if ($errors->has('age_min'))
                                                <span class="text-danger">{{ $errors->first('age_min') }}</span>
                                            @endif
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="age_max">Age Max</label>
                                            <input type="number" class="form-control" name="age_max" id="age_max" placeholder="Enter Age Max" max="100" value="{{ old('age_max', isset($role) ? $role->age_max : '') }}">
                                            @if ($errors->has('age_max'))
                                                <span class="text-danger">{{ $errors->first('age_max') }}</span>
                                            @endif
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="skills">Skills</label>
                                            <input type="text" class="form-control" name="skills" id="skills" placeholder="Enter Skills" value="{{ old('skills', isset($role) ? $role->skills : '') }}">
                                            @if ($errors->has('skills'))
                                                <span class="text-danger">{{ $errors->first('skills') }}</span>
                                            @endif
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="character_description">Character Description</label>
                                            <textarea name="character_description" class="form-control">{{$role->character_description}}</textarea>
                                        </div>
										
										
										<div class="form-group mb-4">
                                            <label for="nudity_or_bareness">Nudity/Bareness</label>
                                            <select class="form-control" id="nudity_or_bareness" name="nudity_or_bareness">
												  
												<option value="1"  
														@if($role->nudity_or_bareness == '1') selected @endif > Yes</option> 
														
												<option value="0"  
														@if($role->nudity_or_bareness == '0') selected @endif > No</option>  
												
											</select>
                                            @if ($errors->has('nudity_or_bareness'))
                                                <span class="text-danger">{{ $errors->first('nudity_or_bareness') }}</span>
                                            @endif
                                        </div>
										
                                      <button type="submit" class="btn btn-primary mt-3">Update</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
@endsection