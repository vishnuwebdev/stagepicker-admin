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

                        @if ($order->status == 'return_requested')
                        <div class="alert alert-warning mb-4" role="alert">
                            <strong>Return / cancel requested by customer.</strong>
                            Reason: {{ ucfirst(str_replace('_', ' ', $order->return_reason)) }}.
                            @if ($order->return_description)
                            <br>Notes: {{ $order->return_description }}
                            @endif
                            <br>Please reach out to the customer directly to resolve this, then update the order status below once handled.
                        </div>
                        @endif

                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Order {{ $order->order_code }}</h4>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/orders') }}" class="btn btn-primary">Back</a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:200px;">User ID</th>
                                        <td>{{ $order->user_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Placed On</th>
                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment</th>
                                        <td>{{ $order->payment_summary }} ({{ $order->payment_status }})</td>
                                    </tr>
                                    <tr>
                                        <th>Shipping Address</th>
                                        <td>
                                            {{ $order->shipping_full_name }}<br>
                                            {{ $order->shipping_address_line1 }}{{ $order->shipping_address_line2 ? ', '.$order->shipping_address_line2 : '' }}<br>
                                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                                            {{ $order->shipping_phone }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Items</th>
                                        <td>
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Variant</th>
                                                        <th>Qty</th>
                                                        <th>Unit Price</th>
                                                        <th>Line Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($order->items as $line)
                                                    <tr>
                                                        <td>{{ $line->title }}</td>
                                                        <td>{{ $line->variant_label }}</td>
                                                        <td>{{ $line->quantity }}</td>
                                                        <td>${{ number_format($line->unit_price, 2) }}</td>
                                                        <td>${{ number_format($line->unit_price * $line->quantity, 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Subtotal / Shipping / Total</th>
                                        <td>${{ number_format($order->subtotal, 2) }} / ${{ number_format($order->shipping_amount, 2) }} / <strong>${{ number_format($order->total, 2) }}</strong></td>
                                    </tr>
                                </table>

                                <form action="{{ url('admin/update-order-status', $order->id) }}" method="post">
                                    <input type="hidden" name="_token" value="{{ Session::token() }}">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="status">Status</label>
                                            <select name="status" class="form-control">
                                                @foreach (['placed','processing','shipped','delivered','cancelled','return_requested','returned'] as $statusOption)
                                                <option value="{{ $statusOption }}" {{ $order->status == $statusOption ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statusOption)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="tracking_number">Tracking Number</label>
                                            <input type="text" class="form-control" name="tracking_number" value="{{ $order->tracking_number }}">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Order</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
