<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Merchandise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Marketplace order domain — create order (checkout), order history, order
 * detail, and return/cancel requests for the Flutter Marketplace 2.0 module
 * (platform/lib/marketplace/). Mirrors the request/response shape of the
 * other endpoints in this folder (user_id-scoped, {"status","message","data"}
 * envelope, Validator + validationHandle()) rather than introducing a new
 * convention.
 *
 * Payment is intentionally mocked here, not wired to Stripe yet (per the
 * product ask: mock data now, Stripe later). No card number is ever stored —
 * only the last 4 digits (for the on-screen "Card •••• 4242" summary) and a
 * decline simulation matching the existing client-side rule in
 * marketplace_card_payment_form.dart (a card ending in 0002 declines), moved
 * here so the approve/decline decision is server-authoritative instead of
 * purely client-side.
 */
class OrderController extends ApiController
{
    /**
     * Builds the {"status","message","data"} envelope used throughout this
     * API, with the order's items eager-loaded and a resolved absolute
     * image URL per item (mirrors how get_merchandise_details rewrites
     * `image` to a full URL before returning it).
     */
    private function serializeOrder(Order $order)
    {
        $order->load('items');
        $order->items->transform(function ($item) {
            // Matches the asset('public/...') convention used across this
            // app's Blade views (see admin/merchandise/*.blade.php) — this
            // deployment's document root is the project root, not public/,
            // so a plain url()/URL::to() path 404s and needs the extra
            // "public/" segment to resolve to a real static file.
            if (!empty($item->image) && strpos($item->image, 'http') !== 0) {
                $item->image = asset('public/admin/uploads/merchandise/' . $item->image);
            }
            return $item;
        });
        return $order;
    }

    public function create_order(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'items' => 'required',
            'shipping_full_name' => 'required',
            'shipping_phone' => 'required',
            'shipping_address_line1' => 'required',
            'shipping_city' => 'required',
            'shipping_state' => 'required',
            'shipping_postal_code' => 'required',
            'payment_method' => 'required|in:credit_card,apple_pay,google_pay',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        // `items` arrives as a JSON-encoded string, not a native array — the
        // Flutter client form-encodes this request body (see the client-side
        // comment in mp_checkout.dart@_submitRealOrder), which can't carry a
        // nested list of objects, so the cart line items are JSON-encoded
        // into this one field instead.
        $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

        if (!is_array($items) || count($items) === 0) {
            $response['status'] = "false";
            $response['message'] = "At least one item is required.";
            return response()->json($response);
        }

        foreach ($items as $item) {
            if (empty($item['title']) || empty($item['quantity'])) {
                $response['status'] = "false";
                $response['message'] = "Each item requires a title and quantity.";
                return response()->json($response);
            }
        }

        $paymentMethod = $request->payment_method;
        $cardNumber = preg_replace('/\s+/', '', (string) $request->card_number);

        // Mock approve/decline — mirrors MarketplaceCardInputData.simulatesDecline
        // on the Flutter side (a card ending in 0002 declines) so the same
        // test card reproduces the same result on both ends.
        $declined = $paymentMethod === 'credit_card' && substr($cardNumber, -4) === '0002';

        if ($declined) {
            $response['status'] = "false";
            $response['payment_status'] = "declined";
            $response['message'] = "Payment declined — check your card details and try again.";
            return response()->json($response);
        }

        $paymentSummary = 'Card';
        if ($paymentMethod === 'apple_pay') {
            $paymentSummary = 'Apple Pay';
        } else if ($paymentMethod === 'google_pay') {
            $paymentSummary = 'Google Pay';
        } else {
            $last4 = strlen($cardNumber) >= 4 ? substr($cardNumber, -4) : $cardNumber;
            $paymentSummary = 'Card •••• ' . $last4;
        }

        $subtotal = 0;
        $preparedItems = [];

        foreach ($items as $item) {
            // Re-price server-side from the catalog whenever a merchandise_id
            // is given, rather than trusting the client-submitted price —
            // the client price is only a fallback for line items that don't
            // map to a merchandise row.
            $unitPrice = isset($item['unit_price']) ? (float) $item['unit_price'] : 0;
            $title = $item['title'];
            $image = isset($item['image']) ? $item['image'] : null;
            $merchandiseId = isset($item['merchandise_id']) && $item['merchandise_id'] !== null && $item['merchandise_id'] !== ''
                ? $item['merchandise_id']
                : null;

            if ($merchandiseId !== null) {
                $merch = Merchandise::find($merchandiseId);
                if ($merch) {
                    $unitPrice = ($merch->sale_price !== null && $merch->sale_price > 0)
                        ? (float) $merch->sale_price
                        : (float) $merch->price;
                    $title = $merch->title;
                    $image = $merch->image;
                }
            }

            $quantity = (int) $item['quantity'];
            $lineTotal = $unitPrice * $quantity;
            $subtotal += $lineTotal;

            $preparedItems[] = [
                'merchandise_id' => $merchandiseId,
                'title' => $title,
                'variant_label' => isset($item['variant_label']) ? $item['variant_label'] : null,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'image' => $image,
            ];
        }

        $shippingAmount = $request->has('shipping_amount') ? (float) $request->shipping_amount : 5.00;
        $total = $subtotal + $shippingAmount;

        DB::beginTransaction();
        try {
            $order = new Order();
            $order->user_id = $request->user_id;
            $order->status = 'placed';
            $order->subtotal = $subtotal;
            $order->shipping_amount = $shippingAmount;
            $order->total = $total;
            $order->payment_method = $paymentMethod;
            $order->payment_summary = $paymentSummary;
            $order->payment_status = 'approved';
            $order->shipping_full_name = $request->shipping_full_name;
            $order->shipping_phone = $request->shipping_phone;
            $order->shipping_address_line1 = $request->shipping_address_line1;
            $order->shipping_address_line2 = $request->shipping_address_line2;
            $order->shipping_city = $request->shipping_city;
            $order->shipping_state = $request->shipping_state;
            $order->shipping_postal_code = $request->shipping_postal_code;
            $order->save();

            $order->order_code = '#ACT-' . date('Y') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            foreach ($preparedItems as $line) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->merchandise_id = $line['merchandise_id'];
                $orderItem->title = $line['title'];
                $orderItem->variant_label = $line['variant_label'];
                $orderItem->unit_price = $line['unit_price'];
                $orderItem->quantity = $line['quantity'];
                $orderItem->image = $line['image'];
                $orderItem->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $response['status'] = "false";
            $response['message'] = "Something went wrong while placing the order.";
            return response()->json($response);
        }

        $response['status'] = "true";
        $response['payment_status'] = "approved";
        $response['message'] = "Order placed successfully";
        $response['data'] = $this->serializeOrder($order);
        return response()->json($response);
    }

    public function get_orders(Request $request)
    {
        $rules = ['user_id' => 'required'];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $orders = Order::with('items')
            ->where('user_id', $request->user_id)
            ->orderBy('id', 'desc')
            ->get();

        $orders->transform(function ($order) {
            return $this->serializeOrder($order);
        });

        $response['status'] = "true";
        $response['message'] = "Orders";
        $response['data'] = ['orders' => $orders];
        return response()->json($response);
    }

    public function get_order_detail(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'order_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $order = Order::where('user_id', $request->user_id)
            ->where('order_code', $request->order_id)
            ->first();

        if (!$order) {
            $response['status'] = "false";
            $response['message'] = "Order not found";
            return response()->json($response);
        }

        $response['status'] = "true";
        $response['message'] = "Order Detail";
        $response['data'] = $this->serializeOrder($order);
        return response()->json($response);
    }

    /**
     * Return / cancel request. Resolution stays a manual, human admin-to-
     * customer follow-up for now (per the product ask) — this just gets the
     * request in front of admin (Store > Orders) instead of nowhere, which
     * is what happened before this endpoint existed.
     */
    public function request_order_return(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'order_id' => 'required',
            'reason' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $order = Order::where('user_id', $request->user_id)
            ->where('order_code', $request->order_id)
            ->first();

        if (!$order) {
            $response['status'] = "false";
            $response['message'] = "Order not found";
            return response()->json($response);
        }

        if ($order->status !== 'delivered') {
            $response['status'] = "false";
            $response['message'] = "This order is not eligible for a return.";
            return response()->json($response);
        }

        $order->status = 'return_requested';
        $order->return_reason = $request->reason;
        $order->return_description = $request->description;
        $order->return_requested_at = now();
        $order->save();

        $response['status'] = "true";
        $response['message'] = "Return request submitted. Our team will reach out to you shortly.";
        $response['data'] = $this->serializeOrder($order);
        return response()->json($response);
    }

    /**
     * Printable invoice for a single order. Returns an HTML view (styled
     * with the admin theme's existing invoice.css) rather than a generated
     * PDF, since no PDF library is currently installed in this PHP 7.3/7.4
     * project (see stagePicker/CLAUDE.md re: EOL stack) — the Flutter app
     * opens this URL in an external browser (url_launcher), where the
     * user's browser "Print > Save as PDF" produces the downloadable file.
     */
    public function order_invoice(Request $request)
    {
        $order = Order::where('user_id', $request->user_id)
            ->where('order_code', $request->order_id)
            ->first();

        if (!$order) {
            return response("Order not found.", 404);
        }

        $order = $this->serializeOrder($order);
        return \View::make('order_invoice', ['order' => $order]);
    }
}
