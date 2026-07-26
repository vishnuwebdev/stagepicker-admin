@extends('layouts.admin')
@section('content')
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">

                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Merchandise Detail</h4>
                                    </div>

                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                    <h4 style="float: right !important;"><a href="{{ URL('admin/merchandise') }}" class="btn btn-primary">Back</a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:200px;">Title</th>
                                        <td>{{ $merchandise->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td>{{ $merchandise->price }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sale Price</th>
                                        <td>{{ $merchandise->sale_price }}</td>
                                    </tr>
                                    <tr>
                                        <th>Available Sizes</th>
                                        <td>{{ $merchandise->size }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if ($merchandise->status == 1)
                                            <span class="btn btn-sm btn-primary">Active</span>
                                            @else
                                            <span class="btn btn-sm btn-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{!! $merchandise->description !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Main Image</th>
                                        <td>
                                            @if ($merchandise->image)
                                            <img src="{{ asset('public/admin/uploads/merchandise/'.$merchandise->image) }}" width="100" height="100" style="object-fit:cover;">
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Gallery Images</th>
                                        <td>
                                            @foreach ($images as $img)
                                            <img src="{{ asset('public/admin/uploads/merchandise/'.$img->image) }}" width="80" height="80" style="object-fit:cover; margin-right:8px;">
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
