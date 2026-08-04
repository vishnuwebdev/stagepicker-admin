@extends('layouts.admin')
@section('content')
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Payment History — {{ $user->email }}</h4>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/payment-transactions') }}" class="btn btn-primary">Back</a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="row mb-4">
                                    <div class="col-md-3"><strong>Total attempts:</strong> {{ $summary['total'] }}</div>
                                    <div class="col-md-3"><strong>Succeeded:</strong> {{ $summary['succeeded'] }}</div>
                                    <div class="col-md-3"><strong>Failed / Cancelled:</strong> {{ $summary['failed'] }}</div>
                                    <div class="col-md-3"><strong>Still Pending:</strong> {{ $summary['pending'] }}</div>
                                </div>

                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2 table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Attempt</th>
                                                <th>For</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Method</th>
                                                <th>Date</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $t)
                                            <tr>
                                                <td>{{ $t->id }}</td>
                                                <td>#{{ $t->attempt_number }}</td>
                                                <td>{{ $t->payable_type ? ucfirst($t->payable_type).' #'.$t->payable_id : '—' }}</td>
                                                <td>${{ number_format($t->amount, 2) }} {{ strtoupper($t->currency) }}</td>
                                                <td>
                                                    @if ($t->status == 'succeeded')
                                                    <span class="btn btn-sm btn-primary">Succeeded</span>
                                                    @elseif (in_array($t->status, ['failed','canceled']))
                                                    <span class="btn btn-sm btn-danger">{{ ucfirst($t->status) }}</span>
                                                    @elseif ($t->status == 'refunded')
                                                    <span class="btn btn-sm btn-warning">Refunded</span>
                                                    @else
                                                    <span class="btn btn-sm btn-info">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $t->card_brand ? ucfirst($t->card_brand).' ••'.$t->card_last4 : ($t->payment_method_type ?: '—') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y, h:i A') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ url('admin/payment-transactions', $t->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
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
