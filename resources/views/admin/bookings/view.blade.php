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
                                        <h4>Booking #{{ $booking->id }}</h4>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/bookings') }}" class="btn btn-primary">Back</a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:220px;">User</th>
                                        <td>
                                            {{ $user ? $user->email : ('User #'.$booking->user_id) }}
                                            &nbsp; <a href="{{ url('admin/payment-transactions/user/'.$booking->user_id) }}">view payment attempts by this user</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Class</th>
                                        <td>
                                            {{ $booking->title_snapshot }}
                                            @if ($class)
                                            &nbsp; <a href="{{ url('admin/view-classes', $class->id) }}">view class</a>
                                            &nbsp; <a href="{{ url('admin/bookings/class/'.$class->id.'/attendees') }}">view all attendees</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if ($booking->status == 'confirmed')
                                            <span class="btn btn-sm btn-primary">Confirmed</span>
                                            @elseif ($booking->status == 'cancelled')
                                            <span class="btn btn-sm btn-danger">Cancelled</span>
                                            @else
                                            <span class="btn btn-sm btn-info">Pending Payment</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Attending As</th>
                                        <td>{{ $booking->attended_mode ? ucfirst($booking->attended_mode) : '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Quantity / Unit Price / Amount</th>
                                        <td>{{ $booking->quantity }} &times; ${{ number_format($booking->price_snapshot, 2) }} = <strong>${{ number_format($booking->amount, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Booked On</th>
                                        <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    @if ($booking->cancelled_at)
                                    <tr>
                                        <th>Cancelled On</th>
                                        <td>{{ \Carbon\Carbon::parse($booking->cancelled_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    @endif
                                </table>

                                <p class="text-muted">Cancelling here is bookkeeping only — it does not touch Stripe or issue a refund. Refunds are handled directly in the Stripe dashboard.</p>

                                <form action="{{ url('admin/bookings/'.$booking->id.'/status') }}" method="post">
                                    <input type="hidden" name="_token" value="{{ Session::token() }}">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="status">Status</label>
                                            <select name="status" class="form-control">
                                                @foreach (['pending_payment','confirmed','cancelled'] as $statusOption)
                                                <option value="{{ $statusOption }}" {{ $booking->status == $statusOption ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statusOption)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Booking</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
