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
                                            <h4>Edit Subscription</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/update-subscription',$subscription->id)  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="subscription_id" value="<?php echo  $subscription->id; ?>">
                                        <div class="form-group mb-4">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Subscription Title" value="{{ old('title', isset($subscription) ? $subscription->title : '') }}" required>
                                            @if ($errors->has('title'))
                                                <span class="text-danger">{{ $errors->first('title') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="amount">Amount</label>
                                            <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter Subscription Amount" value="{{ old('amount', isset($subscription) ? $subscription->amount : '') }}" required>
                                            @if ($errors->has('amount'))
                                                <span class="text-danger">{{ $errors->first('amount') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="duration">Duration in Month</label>
                                            
                                            <input type="text" class="form-control" name="duration" id="duration" placeholder="Enter Subscription duration" value="{{ old('duration', isset($subscription) ? $subscription->duration : '') }}" required>
                                            @if ($errors->has('duration'))
                                                <span class="text-danger">{{ $errors->first('duration') }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="form-group mb-4"> 
            								<div class="custom-file-container" data-upload-id="myFirstImage">
            									<label>Upload Image </label><br/> 
            									<input type="file" name="image" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
            									<br/> <br/> 
            									<?php if(!empty($subscription->image)){ ?> 
            										<img src="{{asset($subscription->image)}}" width="200px">
            									<?php } ?>
            										 
            								</div>
            							</div>
            							
                                        <div class="form-group mb-4">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" class="form-control">{{ $subscription->description }}</textarea>
                                            @if ($errors->has('description'))
                                                <span class="text-danger">{{ $errors->first('description') }}</span>
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