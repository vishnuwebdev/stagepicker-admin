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
                        <h4>Edit Merchandise</h4>
                      </div>
                      <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/merchandise') }}" class="btn btn-primary">Back</a></h4>
                                    </div>
                                </div>
                            </div>
                    <div class="widget-content widget-content-area">
                        <form action="{{url('admin/update-merchandise',$merchandise->id)}}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                        <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ $merchandise->title }}">
                        @if ($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-3">
                        <label for="price">Price</label>
                        <input type="text" class="form-control" name="price" value="{{ $merchandise->price }}">
                        @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                         @endif
                    </div>
                        <div class="form-group col-md-3">
                        <label for="sale_price">Sale Price (optional)</label>
                        <input type="text" class="form-control" name="sale_price" value="{{ $merchandise->sale_price }}">
                        @if ($errors->has('sale_price'))
                        <span class="text-danger">{{ $errors->first('sale_price') }}</span>
                         @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="size">Available Sizes</label>
                        <input type="text" class="form-control" name="size" placeholder="e.g. S, M, L, XL" value="{{ $merchandise->size }}">
                        <small class="text-muted">Comma-separated list of sizes shown to the customer.</small>
                        @if ($errors->has('size'))
                        <span class="text-danger">{{ $errors->first('size') }}</span>
                         @endif
                    </div>
                        <div class="form-group col-md-3">
                        <label for="status">Status</label>
                           <select name="status" class="form-control">
                            <option value="1" {{ $merchandise->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $merchandise->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="image">Main Image</label>
                        @if ($merchandise->image)
                        <div class="mb-2"><img src="{{ asset('public/admin/uploads/merchandise/'.$merchandise->image) }}" width="70" height="70" style="object-fit:cover;"></div>
                        @endif
                        <input type="file" class="form-control" name="image">
                        <small class="text-muted">Leave empty to keep the current image.</small>
                        @if ($errors->has('image'))
                        <span class="text-danger">{{ $errors->first('image') }}</span>
                         @endif
                        </div>
                        <div class="form-group col-md-6">
                        <label for="images">Add More Gallery Images</label>
                        <input type="file" class="form-control" name="images[]" multiple>
                        <small class="text-muted">Optional. Existing gallery images are managed below.</small>
                        </div>
                        </div>
                        <div class="form-group">
                        <div class="form-group col-md-12">
                        <label for="description">Description</label>
                        <textarea type="text" class="ckeditor" name="description" id="editor1" rows="10" cols="80">{{ $merchandise->description }}</textarea>
                        @if ($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                         @endif
                    </div>
                        </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
</div>
                    @if (count($images) > 0)
                    <div class="widget-content widget-content-area mt-3">
                        <label>Existing Gallery Images</label>
                        <div class="row">
                            @foreach ($images as $img)
                            <div class="col-md-2 text-center mb-3">
                                <img src="{{ asset('public/admin/uploads/merchandise/'.$img->image) }}" width="80" height="80" style="object-fit:cover;"><br>
                                <a onclick="return confirm('Remove this image?')" href="{{ url('admin/delete-merchandise-image',$img->id) }}" class="btn btn-sm btn-danger mt-1">Remove</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    </div>
                    </div>
                </div>
            </div>
<script src="../ckeditor.js"></script>
<script>
                CKEDITOR.config.versionCheck = false;
                CKEDITOR.replace( 'editor1' );
            </script>
@endsection
