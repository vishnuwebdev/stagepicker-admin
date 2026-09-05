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
                                            <h4>Add Animal Species</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="{{ url('admin/animal-species/store')  }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-4">
                                            <label for="name">Species Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="e.g. Dog, Cat, Horse" value="{{ old('name') }}" required>
                                            @if ($errors->has('name'))
                                                <span class="text-danger">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group mb-4">
                                            <label>
                                                <input type="checkbox" name="status" value="1" checked> Active (visible in the mobile app dropdown)
                                            </label>
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
