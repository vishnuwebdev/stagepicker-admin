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
                                            <h4>Edit Skill</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/update-skill',$skill->id)  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="skill_id" value="<?php echo  $skill->id; ?>">
                                        <div class="form-group mb-4">
                                            <label for="skill_name">Skill Name</label>
                                            <input type="text" class="form-control" name="skill_name" id="skill_name" placeholder="Enter skill Name" value="{{ old('skill_name', isset($skill) ? $skill->skill_name : '') }}">
                                            @if ($errors->has('skill_name'))
                                                <span class="text-danger">{{ $errors->first('skill_name') }}</span>
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