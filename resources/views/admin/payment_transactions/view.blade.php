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
                                        <h4>Transaction #{{ $transaction->id }} (attempt #{{ $transaction->attempt_number }})</h4>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6" style="text-align: right;">
                                        @if (!$transaction->isTerminal() && $transaction->gateway_intent_id)
                                        <form action="{{ url('admin/payment-transactions/'.$transaction->id.'/refresh') }}" method="post" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" title="Ask Stripe directly for this transaction's current status — useful while the automatic webhook isn't set up yet (see PaymentTransactionController doc comment).">
                                                <i class="fa fa-refresh" aria-hidden="true"></i> Check Status Now
                                            </button>
                                        </form>
                                        @endif
                                        <a href="{{ URL('admin/payment-transactions') }}" class="btn btn-primary">Back</a>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:220px;">User</th>
                                        <td>
                                            {{ $user ? $user->email : ('User #'.$transaction->user_id) }}
                                            &nbsp; <a href="{{ url('admin/payment-transactions/user/'.$transaction->user_id) }}">view all attempts by this user</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if ($transaction->status == 'succeeded')
                                            <span class="btn btn-sm btn-primary">Succeeded</span>
                                            @elseif (in_array($transaction->status, ['failed','canceled']))
                                            <span class="btn btn-sm btn-danger">{{ ucfirst($transaction->status) }}</span>
                                            @elseif ($transaction->status == 'refunded')
                                            <span class="btn btn-sm btn-warning">Refunded</span>
                                            @else
                                            <span class="btn btn-sm btn-info">{{ ucfirst(str_replace('_',' ',$transaction->status)) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>${{ number_format($transaction->amount, 2) }} {{ strtoupper($transaction->currency) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method</th>
                                        <td>{{ $transaction->card_brand ? ucfirst($transaction->card_brand).' •••• '.$transaction->card_last4 : ($transaction->payment_method_type ?: '—') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Stripe PaymentIntent</th>
                                        <td>{{ $transaction->gateway_intent_id ?: '—' }}</td>
                                    </tr>
                                    @if ($transaction->failure_message)
                                    <tr>
                                        <th>Failure Reason</th>
                                        <td class="text-danger">{{ $transaction->failure_message }} @if($transaction->failure_code)({{ $transaction->failure_code }})@endif</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Linked To</th>
                                        <td>
                                            @if ($order)
                                            Order {{ $order->order_code }} — <a href="{{ url('admin/view-order', $order->id) }}">view order</a>
                                            @elseif ($booking)
                                            Booking #{{ $booking->id }} ({{ $booking->title_snapshot }}) — <a href="{{ url('admin/bookings', $booking->id) }}">view booking</a>
                                            @else
                                            {{ $transaction->payable_type ? ucfirst($transaction->payable_type).' #'.$transaction->payable_id : 'Not linked to an order' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created / Completed</th>
                                        <td>
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, h:i A') }}
                                            @if ($transaction->completed_at)
                                            &rarr; {{ \Carbon\Carbon::parse($transaction->completed_at)->format('d M Y, h:i A') }}
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                                @if ($siblingAttempts->count() > 1)
                                <h5 class="mt-4">Other attempts for this same checkout</h5>
                                <p class="text-muted">Every time this customer tried to pay for this order, including this one.</p>
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Attempt</th>
                                                <th>Status</th>
                                                <th>Failure Reason</th>
                                                <th>Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($siblingAttempts as $attempt)
                                            <tr @if($attempt->id == $transaction->id) class="table-active" @endif>
                                                <td>#{{ $attempt->attempt_number }}</td>
                                                <td>{{ ucfirst(str_replace('_',' ',$attempt->status)) }}</td>
                                                <td>{{ $attempt->failure_message }}</td>
                                                <td>{{ \Carbon\Carbon::parse($attempt->created_at)->format('d M Y, h:i A') }}</td>
                                                <td>@if($attempt->id != $transaction->id)<a href="{{ url('admin/payment-transactions', $attempt->id) }}">view</a>@else (this one) @endif</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                <h5 class="mt-4">Event timeline</h5>
                                <p class="text-muted">Every status change seen for this attempt (from the app or from Stripe directly), in order — kept even after the row above moves on, for support/dispute lookups.</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>When</th>
                                                <th>Source</th>
                                                <th>Event</th>
                                                <th>Resulting Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($timeline as $log)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i:s A') }}</td>
                                                <td>{{ ucfirst(str_replace('_',' ',$log->source)) }}</td>
                                                <td>{{ $log->event_type }}</td>
                                                <td>{{ ucfirst(str_replace('_',' ',$log->status)) }}</td>
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
