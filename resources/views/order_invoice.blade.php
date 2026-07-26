<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Reuses the admin theme's existing invoice stylesheet (compiled from
         resources/sass/assets/apps/invoice.scss) so this standalone page
         looks consistent with the admin panel without pulling in its full
         layout/sidebar — this is meant to be opened directly in a mobile
         browser from the Flutter app and printed/saved as a PDF. --}}
    <link rel="stylesheet" href="{{ asset('public/assets/css/apps/invoice.css') }}">
    <style>
        body { background: #fff; padding: 24px; }
        .invoice-print-actions { text-align: right; margin-bottom: 16px; }
        .invoice-print-actions button {
            padding: 8px 18px; border-radius: 6px; border: 1px solid #4361ee;
            background: #4361ee; color: #fff; font-size: 14px; cursor: pointer;
        }
        @media print { .invoice-print-actions { display: none; } }
    </style>
</head>
<body>
    <div class="invoice-print-actions">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="content-section">
        <div class="row inv--head-section">
            <div class="col-sm-6 col-12">
                <h3 class="in-heading">INVOICE</h3>
            </div>
            <div class="col-sm-6 col-12 align-self-center text-sm-right">
                <div class="company-info">
                    <h5 class="inv-brand-name">Acthound Casting</h5>
                </div>
            </div>
        </div>

        <div class="row inv--detail-section">
            <div class="col-sm-7 align-self-center">
                <p class="inv-to">Shipping To</p>
            </div>
            <div class="col-sm-5 align-self-center text-sm-right order-sm-0 order-1">
                <p class="inv-detail-title">From: Acthound Casting / StagePicker</p>
            </div>

            <div class="col-sm-7 align-self-center">
                <p class="inv-customer-name">{{ $order->shipping_full_name }}</p>
                <p class="inv-street-addr">
                    {{ $order->shipping_address_line1 }}{{ $order->shipping_address_line2 ? ', '.$order->shipping_address_line2 : '' }},
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}
                </p>
                <p class="inv-email-address">{{ $order->shipping_phone }}</p>
            </div>
            <div class="col-sm-5 align-self-center text-sm-right order-2">
                <p class="inv-list-number"><span class="inv-title">Invoice Number: </span> <span class="inv-number">{{ $order->order_code }}</span></p>
                <p class="inv-created-date"><span class="inv-title">Order Date: </span> <span class="inv-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</span></p>
                <p class="inv-due-date"><span class="inv-title">Payment: </span> <span class="inv-date">{{ $order->payment_summary }}</span></p>
            </div>
        </div>

        <div class="row inv--product-table-section">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Item</th>
                                <th class="text-right" scope="col">Qty</th>
                                <th class="text-right" scope="col">Unit Price</th>
                                <th class="text-right" scope="col">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->title }}{{ $item->variant_label ? ' — '.$item->variant_label : '' }}</td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-5 col-12 order-sm-0 order-1">
                <div class="inv--payment-info">
                    <h6 class="inv-title">Order Status</h6>
                    <p>{{ ucwords(str_replace('_', ' ', $order->status)) }}</p>
                    @if ($order->tracking_number)
                    <p class="inv-subtitle">Tracking: {{ $order->tracking_number }}</p>
                    @endif
                </div>
            </div>
            <div class="col-sm-7 col-12 order-sm-1 order-0">
                <div class="inv--total-amounts text-sm-right">
                    <div class="row">
                        <div class="col-sm-8 col-7"><p>Subtotal:</p></div>
                        <div class="col-sm-4 col-5"><p>${{ number_format($order->subtotal, 2) }}</p></div>
                        <div class="col-sm-8 col-7"><p>Shipping:</p></div>
                        <div class="col-sm-4 col-5"><p>${{ number_format($order->shipping_amount, 2) }}</p></div>
                        <div class="col-sm-8 col-7 grand-total-title"><h4>Total:</h4></div>
                        <div class="col-sm-4 col-5 grand-total-amount"><h4>${{ number_format($order->total, 2) }}</h4></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
