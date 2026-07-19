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
                        <h4>Class Update</h4>
                      </div>
                      <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/classes') }}" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                         <div class="widget-content widget-content-area">
                        <form action="{{url('admin/update-classes',$category->id)}}" method="post" enctype = "multipart/form-data">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                        <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="inputEmail4">Title</label>
                        <input type="text" class="form-control" name="title"  value="{{ $category->title }}" >
                        @if ($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Price</label>
                        <input type="text" class="form-control" name="price"value="{{ $category->price }}">
                        @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                         @endif 
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Tages</label>
                        <input type="text" class="form-control" name="tags" value="{{ $category->tags }}">
                        @if ($errors->has('tags'))
                        <span class="text-danger">{{ $errors->first('tags') }}</span>
                         @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">Duration</label>
                        <input type="text" class="form-control" name="duration"value="{{ $category->duration }}">
                        @if ($errors->has('tags'))
                        <span class="text-danger">{{ $errors->first('duration') }}</span>
                         @endif
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Duration Type</label>
                           <select name="duration_type" id="cars"   class="form-control" >
                           <option value="">---Select----</option>
                            <option value="month" {{ (isset($category->duration_type)&& $category->duration_type=='month')?'selected':''}}>Month</option>
                            <option value="year"  {{ (isset($category->duration_type)&& $category->duration_type=='year')?'selected':''}}>Year</option>
                            <option value="day" {{ (isset($category->duration_type)&& $category->duration_type=='day')?'selected':''}}>Day</option>
                         
                        </select> 
                        @if ($errors->has('duration_type'))
                        <span class="text-danger">{{ $errors->first('duration_type') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Image</label>
                        <input type="file" class="form-control" name="image"  value="{{$category->image}}">
                        {{$category->image}}
                        @if ($errors->has('image'))
                        <span class="text-danger">{{ $errors->first('image') }}</span>
                         @endif
                        </div>
                        </div>
                        <div class="form-group">
                        <div class="form-group col-md-12">
                        <label for="inputPassword4">Description</label>
                        <textarea  type="text" class="ckeditor" name="description" id="editor1"   rows="10" cols="80"> {{ $category->description }}</textarea>
                        @if ($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                         @endif
                    </div>
                        </div>
                         <div class="form-group">
                        <div class="form-check">
                    </div>
                    </div>
                    <input type="hidden" placeholder="Enter Password" name="id" id="psw" value = "{{ $category->id }}" >
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