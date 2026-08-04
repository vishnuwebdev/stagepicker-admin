<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin visibility into Stripe payment attempts (Store > Payment
 * Transactions in the sidebar). Read-only by design — no refund/void
 * action lives here; refunds are performed directly in the Stripe
 * dashboard per the product ask, and this screen exists purely so admin
 * can answer "what happened to this customer's payment" without leaving
 * the app: every attempt (succeeded, failed, cancelled, still pending) is
 * listed, per-user and per-checkout, with the full event timeline behind
 * each one.
 *
 * Follows the same plain-Eloquent, \View::make() pattern as OrderController
 * in this folder.
 */
class PaymentTransactionController extends AdminController
{
    public function list(Request $request)
    {
        $query = PaymentTransaction::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->orderBy('id', 'desc')->get();

        // Bulk-fetch the users referenced on this page instead of an N+1
        // lookup per row.
        $userIds = $transactions->pluck('user_id')->unique()->filter()->values();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $data['transactions'] = $transactions;
        $data['users'] = $users;
        $data['filters'] = $request->only(['status', 'user_id', 'from', 'to']);
        return \View::make('admin/payment_transactions/list', $data);
    }

    public function view($id)
    {
        $transaction = PaymentTransaction::find($id);
        if (!$transaction) {
            return redirect('admin/payment-transactions')->with('error', 'Transaction not found');
        }

        // Every attempt made for the same checkout (order) — this is the
        // "did they try before and fail" history for this one purchase.
        $siblingAttempts = PaymentTransaction::where('user_id', $transaction->user_id)
            ->where('payable_type', $transaction->payable_type)
            ->where('payable_id', $transaction->payable_id)
            ->orderBy('id', 'asc')
            ->get();

        $data['transaction'] = $transaction;
        $data['user'] = User::find($transaction->user_id);
        $data['order'] = $transaction->payable_type === 'order' ? Order::find($transaction->payable_id) : null;
        $data['siblingAttempts'] = $siblingAttempts;
        $data['timeline'] = $transaction->logs;
        return \View::make('admin/payment_transactions/view', $data);
    }

    /**
     * Every payment attempt a single user has ever made, newest first —
     * the per-user "attempted transaction history" view called out in the
     * product ask, separate from the per-checkout view above.
     */
    public function userHistory($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return redirect('admin/payment-transactions')->with('error', 'User not found');
        }

        $transactions = PaymentTransaction::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        $data['user'] = $user;
        $data['transactions'] = $transactions;
        $data['summary'] = [
            'total' => $transactions->count(),
            'succeeded' => $transactions->where('status', PaymentTransaction::STATUS_SUCCEEDED)->count(),
            'failed' => $transactions->whereIn('status', [PaymentTransaction::STATUS_FAILED, PaymentTransaction::STATUS_CANCELED])->count(),
            'pending' => $transactions->whereIn('status', [PaymentTransaction::STATUS_CREATED, PaymentTransaction::STATUS_REQUIRES_PAYMENT, PaymentTransaction::STATUS_PROCESSING])->count(),
        ];
        return \View::make('admin/payment_transactions/user_history', $data);
    }
}
