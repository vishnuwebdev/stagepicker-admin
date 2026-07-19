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
                        <h4>Webinars add</h4>
                      </div>
                      <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/webinars') }}" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                         <div class="widget-content widget-content-area">
                        <form action="{{url('admin/store-weinars')}}" method="post" enctype = "multipart/form-data">
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
                        <label for="inputPassword4">Link</label>
                        <input type="link" class="form-control" name="link" placeholder="Tages">
                        @if ($errors->has('link'))
                        <span class="text-danger">{{ $errors->first('link') }}</span>
                         @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">Date</label>
                        <input type="date" class="form-control" name="date" placeholder="Duration">
                        @if ($errors->has('date'))
                        <span class="text-danger">{{ $errors->first('date') }}</span>
                         @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">Time</label>
                        <input type="time" class="form-control" name="time" placeholder="Duration">
                        @if ($errors->has('time'))
                        <span class="text-danger">{{ $errors->first('time') }}</span>
                         @endif
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Image</label>
                        <input type="file" class="form-control" name="image" placeholder="Image">
                        @if ($errors->has('image'))
                        <span class="text-danger">{{ $errors->first('image') }}</span>
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

@endsection