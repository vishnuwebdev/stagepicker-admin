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
                                            <h4>Edit Career</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/update-career',$career->id)  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="career_id" value="<?php echo  $career->id; ?>">
                                        <div class="form-group mb-4">
                                            <label for="name">Career Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter career Name" value="{{ old('name', isset($career) ? $career->name : '') }}">
                                            @if ($errors->has('name'))
                                                <span class="text-danger">{{ $errors->first('name') }}</span>
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