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
                                            <h4>Create Role</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/role/storeroletype')  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-4">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" value="{{ old('title', isset($role) ? $role->title : '') }}" required>
                                            @if ($errors->has('title'))
                                                <span class="text-danger">{{ $errors->first('title') }}</span>
                                            @endif
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
												<option value="">Select Status</option>
												<option value="1">Active</option>
												<option value="2">In Active</option> 
											</select>
                                            @if ($errors->has('status'))
                                                <span class="text-danger">{{ $errors->first('status') }}</span>
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