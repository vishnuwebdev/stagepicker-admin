@extends('layouts.admin')
@section('content')
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success mb-4" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                <strong>{{ $message }}</strong></button>
                            </div>
                        @endif
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                            <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                        <h4>Class add</h4>
                      </div>
                      <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/classes') }}" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                         <div class="widget-content widget-content-area">
                        <form action="{{url('admin/store_classes')}}" method="post" enctype = "multipart/form-data">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                        <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="inputEmail4">Title</label>
                        <input type="text" class="form-control" name="title"  placeholder="Title">
                        @if ($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Price</label>
                        <input type="text" class="form-control" name="price" placeholder="Price">
                        @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                         @endif 
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Tages</label>
                        <input type="text" class="form-control" name="tags" placeholder="Tages">
                        @if ($errors->has('tags'))
                        <span class="text-danger">{{ $errors->first('tags') }}</span>
                         @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">Duration</label>
                        <input type="text" class="form-control" name="duration" placeholder="Duration">
                        @if ($errors->has('tags'))
                        <span class="text-danger">{{ $errors->first('duration') }}</span>
                         @endif
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Duration Type</label>
                           <select name="duration_type" id="cars"   class="form-control" >
                           <option value="">--Select Duration--</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                            <option value="day">Day</option>
                         
                        </select> 
                        @if ($errors->has('duration_type'))
                        <span class="text-danger">{{ $errors->first('duration_type') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Image</label>
                        <input type="file" class="form-control" name="image" placeholder="Image">
                        @if ($errors->has('image'))
                        <span class="text-danger">{{ $errors->first('image') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Mode</label>
                           <select name="mode" id="class-mode" class="form-control">
                           <option value="">--Select Mode--</option>
                            <option value="online">Online (join link only)</option>
                            <option value="offline">In-Person (address only)</option>
                            <option value="both">Both (user picks at booking)</option>
                        </select>
                        @if ($errors->has('mode'))
                        <span class="text-danger">{{ $errors->first('mode') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Join Link <small class="text-muted">(required for Online / Both)</small></label>
                        <input type="text" class="form-control" name="link" placeholder="https://... course link, shown only after purchase">
                        @if ($errors->has('link'))
                        <span class="text-danger">{{ $errors->first('link') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Address <small class="text-muted">(required for In-Person / Both)</small></label>
                        <input type="text" class="form-control" name="location" placeholder="Studio address, shown only after purchase">
                        @if ($errors->has('location'))
                        <span class="text-danger">{{ $errors->first('location') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Max Seats <small class="text-muted">(blank = unlimited)</small></label>
                        <input type="number" min="1" class="form-control" name="max_seats" placeholder="e.g. 20">
                        @if ($errors->has('max_seats'))
                        <span class="text-danger">{{ $errors->first('max_seats') }}</span>
                         @endif
                        </div>
                        </div>
                        <div class="form-group">
                        <div class="form-group col-md-12">
                        <label for="inputPassword4">Description</label>
                        <textarea  type="text" class="ckeditor" name="description" id="editor1"   rows="10" cols="80"></textarea>
                        @if ($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                         @endif
                    </div>
                        </div>
                         <div class="form-group">
                        <div class="form-check">
                    </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  
                    </div>
                    </form>
                    </div>
                    </div>
                    </div>
                </div>
</div>
            </div>

</div>
<script src="../ckeditor.js"></script>
<script>
                // Replace the <textarea id="editor1"> with a CKEditor 4
                // instance, using default configuration.
                CKEDITOR.config.versionCheck = false;
                CKEDITOR.replace( 'editor1' );
            </script>>
@endsection