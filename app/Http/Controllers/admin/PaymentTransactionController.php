<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\PaymentController;
use App\Lib\StripePayment;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin visibility into Stripe payment attempts (Store > Payment
 * Transactions in the sidebar). Mostly read-only — no refund/void action
 * lives here; refunds are performed directly in the Stripe dashboard per
 * the product ask. The one write action is `refreshStatus`: a manual
 * "check with Stripe right now" button for a specific stuck transaction.
 *
 * That button exists instead of the Stripe webhook for day 1 — the webhook
 * (StripeWebhookController) needs the backend reachable over HTTPS and a
 * matching endpoint registered in the Stripe dashboard before it does
 * anything, neither of which is set up yet. Until that's done, a
 * transaction that doesn't resolve on its own (app killed mid-payment,
 * dropped connection right after paying) will still self-heal within ~45
 * minutes via the ExpirePendingPaymentTransactions scheduled sweep — this
 * button is just for when admin wants that answer immediately instead of
 * waiting, e.g. a customer emails asking "did my payment go through?".
 * Enabling the webhook later needs zero code changes here — it's a
 * dashboard configuration step once HTTPS is in place.
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

    /**
     * "Check status now" — re-queries Stripe directly for this one
     * transaction's PaymentIntent and reconciles it on the spot, using the
     * exact same reconciliation logic as the webhook and the app's own
     * sync call (PaymentController@applyIntentToTransaction), so all three
     * paths always agree on what a given Stripe status means and always
     * cascade to the linked order the same way. See the class doc comment
     * for why this exists instead of relying on the webhook for now.
     *
     * A no-op (with a friendly message) if the transaction has already
     * reached a terminal status or never got as far as creating a Stripe
     * PaymentIntent — nothing to check in either case.
     */
    public function refreshStatus($id)
    {
        $transaction = PaymentTransaction::find($id);
        if (!$transaction) {
            return redirect('admin/payment-transactions')->with('error', 'Transaction not found');
        }

        if ($transaction->isTerminal()) {
            return redirect('admin/payment-transactions/' . $id)
                ->with('success', 'This transaction already reached a final status (' . $transaction->status . ') — nothing to refresh.');
        }

        if (!$transaction->gateway_intent_id) {
            return redirect('admin/payment-transactions/' . $id)
                ->with('error', 'Payment was never started for this transaction — no PaymentIntent to check.');
        }

        try {
            $stripe = new StripePayment();
            $intent = $stripe->retrievePaymentIntent($transaction->gateway_intent_id);
        } catch (\Exception $e) {
            return redirect('admin/payment-transactions/' . $id)
                ->with('error', 'Could not reach Stripe to check this transaction: ' . $e->getMessage());
        }

        $previousStatus = $transaction->status;
        $controller = new PaymentController();
        $controller->applyIntentToTransaction(
            $transaction,
            $intent,
            PaymentTransactionLog::SOURCE_ADMIN_MANUAL,
            'admin_manual_check'
        );

        $transaction = $transaction->fresh();
        $message = $transaction->status === $previousStatus
            ? 'Checked with Stripe — status is unchanged (' . $transaction->status . ').'
            : 'Updated from Stripe — status is now ' . $transaction->status . '.';

        return redirect('admin/payment-transactions/' . $id)->with('success', $message);
    }
}
