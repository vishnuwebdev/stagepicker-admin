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
                                            <h4>Create Category</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/categories/store')  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-4">
                                            <label for="category_name">Category Name</label>
                                            <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Enter Category Name" value="{{ old('category_name', isset($category) ? $category->category_name : '') }}">
                                            @if ($errors->has('category_name'))
                                                <span class="text-danger">{{ $errors->first('category_name') }}</span>
                                            @endif
                                        </div>
										<div class="form-group mb-4">
                                            <label for="type">Type</label>
                                            <select class="form-control" id="type" name="type">
												
												<option value="0"> Audition/Photography</option> 
														
												<option value="1"> Production Crew</option>  
												
											</select>
                                            @if ($errors->has('type'))
                                                <span class="text-danger">{{ $errors->first('type') }}</span>
                                            @endif
                                        </div>
										
                                      <button type="submit" class="btn btn-primary mt-3">Create</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
@endsection