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
                                    <h4 style="float: right !important;"><a href="{{ URL('admin/classes') }}" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2  table-hover">
                                        <thead>
                                            <tr>
                                                <th> Sr. No.</th>
                                                <th>Class Name</th>
                                                <th>Price</th>
                                                <th>Duration</th>
                                                <th>Duration Type</th>
                                                <th>Mode</th>
                                                <th>Link / Address</th>
                                                <th>Max Seats</th>
                                                <th>Status</th>
                                                <th>Attendees</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($category as $key => $categoryval) { ?>
                                            <tr>
                                                <td> 1 </td>
                                                <td>{{ $categoryval->title }}</td>
                                                <td>
												 {{ $categoryval->price }}
												</td>
                                                <td>
												 {{ $categoryval->duration }}
												</td>
                                                <td>
												 {{ $categoryval->duration_type }}
												</td>
                                                <td>{{ ucfirst($categoryval->mode ?? 'online') }}</td>
                                                <td>
												 {{ $categoryval->link }}
												 @if($categoryval->link && $categoryval->location)<br>@endif
												 {{ $categoryval->location }}
												</td>
                                                <td>{{ $categoryval->max_seats ?: 'Unlimited' }}</td>
                                                <td>
												@if($categoryval->status == 1)
                                                <a href="#" class="btn btn-sm btn-primary">Active</a>
                                                    @else
                                                <a href="#" class="btn btn-sm btn-danger">inactive</a>
                                                @endif
												</td>
                                                <td>
                                                <a href="{{ url('admin/bookings/class/'.$categoryval->id.'/attendees') }}" class="btn btn-sm btn-primary">View Attendees</a>
                                                </td>

                                            </tr>
                                              <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
@endsection