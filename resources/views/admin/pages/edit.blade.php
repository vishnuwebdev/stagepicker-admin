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
                                            <h4>Edit Page</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/update-page',$pages->id)  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="page_id" value="<?php echo  $pages->id; ?>">
                                        <div class="form-group mb-4">
                                            <label for="title">Page Title</label>
                                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Category Name" value="{{ old('category_name', isset($pages) ? $pages->title : '') }}">
                                            @if ($errors->has('title'))
                                                <span class="text-danger">{{ $errors->first('title') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="title">Page Title</label>
								            <textarea class="form-control" id="description" name="content" required><?php echo isset($pages->content)?$pages->content:''; ?></textarea>
                                            @if ($errors->has('title'))
                                                <span class="text-danger">{{ $errors->first('title') }}</span>
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
            <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
CKEDITOR.config.versionCheck = false;
CKEDITOR.replace( 'description' );
</script>
@endsection

