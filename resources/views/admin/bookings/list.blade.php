@extends('layouts.admin')
@section('content')
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success mb-4" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger mb-4" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Bookings</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Booking #</th>
                                                <th>User</th>
                                                <th>Class</th>
                                                <th>Mode</th>
                                                <th>Qty</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Booked</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bookings as $item)
                                            <tr>
                                                <td>#{{ $item->id }}</td>
                                                <td>
                                                    {{ optional($users->get($item->user_id))->email ?: ('User #'.$item->user_id) }}
                                                </td>
                                                <td>{{ $item->title_snapshot }}</td>
                                                <td>{{ $item->attended_mode ? ucfirst($item->attended_mode) : '—' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->amount, 2) }}</td>
                                                <td>
                                                    @if ($item->status == 'confirmed')
                                                    <span class="btn btn-sm btn-primary">Confirmed</span>
                                                    @elseif ($item->status == 'cancelled')
                                                    <span class="btn btn-sm btn-danger">Cancelled</span>
                                                    @else
                                                    <span class="btn btn-sm btn-info">Pending Payment</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ url('admin/bookings', $item->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
