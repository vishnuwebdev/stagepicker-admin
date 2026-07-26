<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Admin visibility + management for marketplace orders (Store > Orders in
 * the sidebar). Follows the same plain-Eloquent, redirect-with-flash-message
 * pattern as MerchandiseController in this folder.
 *
 * Cancel/return requests are surfaced here (see the "Return Requested"
 * banner on the detail view) but resolution stays a manual, human
 * admin-to-customer follow-up for now — this controller lets admin change
 * status/tracking once that conversation has happened, it doesn't automate
 * the conversation itself.
 */
class OrderController extends AdminController
{
    public function list(Request $request)
    {
        $data['orders'] = Order::orderBy('id', 'desc')->get();
        return \View::make('admin/orders/list', $data);
    }

    public function view($id)
    {
        $order = Order::with('items')->find($id);
        if (!$order) {
            return redirect('admin/orders')->with('error', 'Order not found');
        }
        $data['order'] = $order;
        return \View::make('admin/orders/view', $data);
    }

    public function updateStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|in:placed,processing,shipped,delivered,cancelled,return_requested,returned',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $order = Order::find($id);
        if (!$order) {
            return redirect('admin/orders')->with('error', 'Order not found');
        }

        $order->status = $request->status;
        $order->tracking_number = $request->tracking_number;
        $order->save();

        return redirect('admin/view-order/' . $id)->with('success', 'Order updated successfully');
    }
}
