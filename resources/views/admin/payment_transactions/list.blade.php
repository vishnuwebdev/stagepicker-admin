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
                                        <h4>Payment Transactions</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <form method="get" action="{{ url('admin/payment-transactions') }}" class="form-row mb-4">
                                    <div class="form-group col-md-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All</option>
                                            @foreach (['created','requires_payment','processing','succeeded','failed','canceled','refunded'] as $statusOption)
                                            <option value="{{ $statusOption }}" {{ ($filters['status'] ?? '') == $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>User ID</label>
                                        <input type="text" class="form-control" name="user_id" value="{{ $filters['user_id'] ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>From</label>
                                        <input type="date" class="form-control" name="from" value="{{ $filters['from'] ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>To</label>
                                        <input type="date" class="form-control" name="to" value="{{ $filters['to'] ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ url('admin/payment-transactions') }}" class="btn btn-secondary">Clear</a>
                                    </div>
                                </form>

                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2 table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User</th>
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
                                            @php $u = $users->get($t->user_id); @endphp
                                            <tr>
                                                <td>{{ $t->id }}</td>
                                                <td>
                                                    {{ $u ? $u->email : ('User #'.$t->user_id) }}
                                                    <br><a href="{{ url('admin/payment-transactions/user/'.$t->user_id) }}">view all attempts</a>
                                                </td>
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
