@extends('layouts.admin')
@section('content')
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                     

                 
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Class View</h4>
                                    </div>
									
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                    <h4 style="float: right !important;"><a href="{{ URL('admin/webinars') }}" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2  table-hover">
                                        <thead>
                                            <tr>
                                               <th> Sr. No.</th>
                                                <th>Webinar Name</th>
                                                <th>Link</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            
                                            <tr>
                                                <td> 1 </td>
                                                <td>{{$weninar->title }}</td>
                                                <td>
												 {{ $weninar->link }}
												</td>
                                                <td>
												 {{ $weninar->date }}
												</td>
                                                <td>
												 {{ $weninar->time }}
												</td>
                                                <td>
												 {{ $weninar->price }}
												</td>
                                                <td>
												@if($weninar->status == 1)
                                                <a href="#" class="btn btn-sm btn-primary">Active</a>
                                                    @else
                                                <a href="#" class="btn btn-sm btn-danger">inactive</a>
                                                @endif
												</td>
             
                                            </tr>
                                             
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
@endsection