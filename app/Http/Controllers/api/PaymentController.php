<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Lib\StripePayment;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Generic Stripe payment endpoints — reusable by any checkout flow, not
 * just the marketplace. Mirrors the request/response envelope conventions
 * of api/OrderController.php ({"status","message","data"}, Validator +
 * validationHandle()).
 *
 * The mobile app never sends card details here. It only ever: (1) asks
 * this controller to create a PaymentIntent and gets back a client_secret,
 * (2) hands that client_secret to Stripe's native PaymentSheet, which
 * talks to Stripe directly, then (3) calls sync_payment_status so the UI
 * can show an immediate result. The Stripe webhook (StripeWebhookController)
 * is the actual source of truth in case step 3 never happens (app killed,
 * network drop) — see ExpirePendingPaymentTransactions for the last-resort
 * safety net if even the webhook is missed.
 */
class PaymentController extends ApiController
{
    /** Small helper — every response here follows the same envelope. */
    private function fail($message)
    {
        return response()->json(['status' => 'false', 'message' => $message]);
    }

    /**
     * Resolves the amount to actually charge and validates ownership when
     * the payment is tied to a domain row (currently only 'order'). The
     * amount is always re-derived server-side from the payable — never
     * trusted from the client — except for the generic/untied case where
     * there is nothing to re-derive from.
     *
     * @return array{0: float|null, 1: string|null} [amount, errorMessage]
     */
    private function resolveAmount(Request $request)
    {
        if ($request->payable_type === 'order') {
            $order = Order::where('id', $request->payable_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$order) {
                return [null, 'Order not found for this user.'];
            }
            if (!in_array($order->status, ['pending_payment', 'placed'], true)) {
                return [null, 'This order is not awaiting payment.'];
            }
            return [(float) $order->total, null];
        }

        // Generic / not tied to a domain row yet — trust the client amount
        // (used for future flows like subscriptions/contributions before
        // they get their own payable_type). Still validated as numeric >0
        // by the caller's Validator rules.
        return [(float) $request->amount, null];
    }

    public function create_payment_intent(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'amount' => 'nullable|numeric|min:0.5',
            'currency' => 'nullable|string|size:3',
            'payable_type' => 'nullable|in:order,generic',
            'payable_id' => 'nullable|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $payableType = $request->payable_type ?: 'generic';
        $payableId = $payableType === 'order' ? $request->payable_id : null;

        if ($payableType === 'order' && !$payableId) {
            return $this->fail('payable_id is required when payable_type is order.');
        }
        if ($payableType === 'generic' && !$request->amount) {
            return $this->fail('amount is required.');
        }

        list($amount, $error) = $this->resolveAmount($request);
        if ($error) {
            return $this->fail($error);
        }

        $currency = strtolower($request->currency ?: 'usd');

        // Attempt number = how many attempts already exist for this same
        // payable (or, for generic payments, this same user+description) —
        // this is what makes retries show up as their own history rows
        // instead of overwriting the previous attempt.
        $attemptNumber = PaymentTransaction::where('user_id', $request->user_id)
            ->where('payable_type', $payableType)
            ->where('payable_id', $payableId)
            ->count() + 1;

        $transaction = new PaymentTransaction();
        $transaction->user_id = $request->user_id;
        $transaction->payable_type = $payableType;
        $transaction->payable_id = $payableId;
        $transaction->attempt_number = $attemptNumber;
        $transaction->gateway = 'stripe';
        $transaction->amount = $amount;
        $transaction->currency = $currency;
        $transaction->status = PaymentTransaction::STATUS_CREATED;
        $transaction->metadata = json_encode([
            'description' => $request->description,
            'payable_type' => $payableType,
            'payable_id' => $payableId,
        ]);
        $transaction->save();

        $idempotencyKey = 'txn_' . $transaction->id . '_' . Str::random(12);
        $transaction->idempotency_key = $idempotencyKey;
        $transaction->save();

        try {
            $stripe = new StripePayment();
            $intent = $stripe->createPaymentIntent(
                (int) round($amount * 100),
                $currency,
                [
                    'transaction_id' => (string) $transaction->id,
                    'user_id' => (string) $request->user_id,
                    'payable_type' => $payableType,
                    'payable_id' => (string) $payableId,
                ],
                $idempotencyKey
            );
        } catch (\Exception $e) {
            $transaction->status = PaymentTransaction::STATUS_FAILED;
            $transaction->failure_message = $e->getMessage();
            $transaction->completed_at = now();
            $transaction->save();

            PaymentTransactionLog::create([
                'payment_transaction_id' => $transaction->id,
                'source' => PaymentTransactionLog::SOURCE_CLIENT_SYNC,
                'event_type' => 'create_intent_failed',
                'status' => $transaction->status,
                'raw_payload' => json_encode(['error' => $e->getMessage()]),
            ]);

            return $this->fail('Unable to start payment. Please try again.');
        }

        $transaction->gateway_intent_id = $intent->id;
        $transaction->status = PaymentTransaction::STATUS_REQUIRES_PAYMENT;
        $transaction->initiated_at = now();
        $transaction->save();

        PaymentTransactionLog::create([
            'payment_transaction_id' => $transaction->id,
            'source' => PaymentTransactionLog::SOURCE_CLIENT_SYNC,
            'event_type' => 'intent_created',
            'status' => $transaction->status,
            'raw_payload' => json_encode(['payment_intent_id' => $intent->id]),
        ]);

        $response['status'] = "true";
        $response['message'] = "Payment intent created";
        $response['data'] = [
            'transaction_id' => $transaction->id,
            'client_secret' => $intent->client_secret,
            'publishable_key' => config('app.stripe_publish_key'),
            'amount' => $amount,
            'currency' => $currency,
        ];
        return response()->json($response);
    }

    /**
     * Called by the app immediately after the PaymentSheet closes (success,
     * failure, or user-cancel) to reconcile our record with Stripe right
     * away, so the UI isn't stuck waiting on a webhook that may take a few
     * seconds. The webhook (and, as a last resort, the stale-payment sweep)
     * still independently do the same reconciliation, so a missed call here
     * (app killed right after payment) doesn't leave the order/transaction
     * permanently stuck.
     */
    public function sync_payment_status(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'transaction_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $transaction = PaymentTransaction::where('id', $request->transaction_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$transaction) {
            return $this->fail('Transaction not found.');
        }

        if ($transaction->isTerminal()) {
            // Already settled (e.g. the webhook beat this call here) —
            // return the current state rather than re-hitting Stripe.
            $response['status'] = "true";
            $response['message'] = "Transaction status";
            $response['data'] = $this->serializeTransaction($transaction);
            return response()->json($response);
        }

        if (!$transaction->gateway_intent_id) {
            return $this->fail('Payment was never started for this transaction.');
        }

        try {
            $stripe = new StripePayment();
            $intent = $stripe->retrievePaymentIntent($transaction->gateway_intent_id);
        } catch (\Exception $e) {
            return $this->fail('Unable to check payment status right now.');
        }

        $this->applyIntentToTransaction($transaction, $intent, PaymentTransactionLog::SOURCE_CLIENT_SYNC, 'client_sync');

        $response['status'] = "true";
        $response['message'] = "Transaction status";
        $response['data'] = $this->serializeTransaction($transaction->fresh());
        return response()->json($response);
    }

    public function get_transaction_history(Request $request)
    {
        $rules = ['user_id' => 'required'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $query = PaymentTransaction::where('user_id', $request->user_id);
        if ($request->filled('payable_type')) {
            $query->where('payable_type', $request->payable_type);
        }
        if ($request->filled('payable_id')) {
            $query->where('payable_id', $request->payable_id);
        }

        $transactions = $query->orderBy('id', 'desc')->get();

        $response['status'] = "true";
        $response['message'] = "Transaction history";
        $response['data'] = [
            'transactions' => $transactions->map(function ($t) {
                return $this->serializeTransaction($t);
            })->values(),
        ];
        return response()->json($response);
    }

    public function get_transaction_detail(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'transaction_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $transaction = PaymentTransaction::where('id', $request->transaction_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$transaction) {
            return $this->fail('Transaction not found.');
        }

        $data = $this->serializeTransaction($transaction);
        $data['timeline'] = $transaction->logs->map(function ($log) {
            return [
                'status' => $log->status,
                'event_type' => $log->event_type,
                'at' => $log->created_at,
            ];
        })->values();

        $response['status'] = "true";
        $response['message'] = "Transaction detail";
        $response['data'] = $data;
        return response()->json($response);
    }

    /**
     * Applies a retrieved Stripe PaymentIntent's state onto our local row,
     * cascading to the linked payable (currently: Order), and always
     * appending a log row so the transition is preserved regardless of
     * what overwrites `status` next. Shared by sync_payment_status,
     * StripeWebhookController, and the stale-payment sweep so all three
     * reconciliation paths behave identically.
     */
    public function applyIntentToTransaction(PaymentTransaction $transaction, $intent, $source, $eventType)
    {
        $status = $this->mapStripeStatus($intent->status);

        $transaction->status = $status;
        if (in_array($status, PaymentTransaction::TERMINAL_STATUSES, true)) {
            $transaction->completed_at = now();
        }

        if (!empty($intent->last_payment_error)) {
            $lastError = is_object($intent->last_payment_error)
                ? $intent->last_payment_error->jsonSerialize()
                : $intent->last_payment_error;
            $transaction->failure_code = $lastError['code'] ?? null;
            $transaction->failure_message = $lastError['message'] ?? null;
        }

        // Pull payment-method summary off the first charge if present
        // (this SDK/API version exposes charges as a list rather than a
        // single latest_charge reference).
        try {
            $charges = is_object($intent->charges) ? $intent->charges->data : [];
            if (!empty($charges)) {
                $charge = $charges[0];
                $details = $charge->payment_method_details ?? null;
                if ($details) {
                    $transaction->payment_method_type = $details->type ?? null;
                    if (!empty($details->card)) {
                        $transaction->card_brand = $details->card->brand ?? null;
                        $transaction->card_last4 = $details->card->last4 ?? null;
                    }
                }
            }
        } catch (\Exception $e) {
            // Non-fatal — payment method summary is informational only.
        }

        $transaction->save();

        PaymentTransactionLog::create([
            'payment_transaction_id' => $transaction->id,
            'source' => $source,
            'event_type' => $eventType,
            'status' => $status,
            'raw_payload' => json_encode($intent->jsonSerialize()),
        ]);

        $this->cascadeToPayable($transaction);
    }

    private function mapStripeStatus($stripeStatus)
    {
        switch ($stripeStatus) {
            case 'succeeded':
                return PaymentTransaction::STATUS_SUCCEEDED;
            case 'canceled':
                return PaymentTransaction::STATUS_CANCELED;
            case 'processing':
                return PaymentTransaction::STATUS_PROCESSING;
            case 'requires_payment_method':
                // Stripe moves a PaymentIntent back to this state after a
                // card is declined — from our side that attempt has failed;
                // the user retrying produces a brand new transaction row.
                return PaymentTransaction::STATUS_FAILED;
            case 'requires_action':
            case 'requires_confirmation':
            case 'requires_capture':
            default:
                return PaymentTransaction::STATUS_PROCESSING;
        }
    }

    /**
     * Keeps the linked domain row in sync so nothing is left "placed" but
     * unpaid: a succeeded payment finalizes the order, a failed/canceled
     * one cancels it outright rather than leaving it stuck — this is the
     * concrete implementation of "if a transaction can't complete, the
     * order should be cancelled" from the product ask.
     */
    private function cascadeToPayable(PaymentTransaction $transaction)
    {
        if ($transaction->payable_type !== 'order' || !$transaction->payable_id) {
            return;
        }

        $order = Order::find($transaction->payable_id);
        if (!$order) {
            return;
        }

        if ($transaction->status === PaymentTransaction::STATUS_SUCCEEDED) {
            $order->payment_status = 'approved';
            if ($order->status === 'pending_payment') {
                $order->status = 'placed';
            }
            // Fill in the on-screen "Card •••• 4242" / "Apple Pay" summary
            // now that Stripe has told us which payment method was
            // actually used — create_order can't know this in advance
            // since the app never sends raw card data there anymore.
            if ($transaction->card_brand && $transaction->card_last4) {
                $order->payment_summary = ucfirst($transaction->card_brand) . ' •••• ' . $transaction->card_last4;
            } elseif ($transaction->payment_method_type === 'card') {
                $order->payment_summary = 'Card';
            } elseif ($transaction->payment_method_type) {
                $order->payment_summary = ucwords(str_replace('_', ' ', $transaction->payment_method_type));
            }
            $order->save();
        } elseif (in_array($transaction->status, [PaymentTransaction::STATUS_FAILED, PaymentTransaction::STATUS_CANCELED], true)) {
            // Only cancel the order if it's still waiting on this payment —
            // don't undo an order that a different, later attempt already
            // succeeded on.
            if ($order->status === 'pending_payment') {
                $order->payment_status = $transaction->status === 'failed' ? 'declined' : 'cancelled';
                $order->status = 'cancelled';
                $order->save();
            }
        }
    }

    private function serializeTransaction(PaymentTransaction $t)
    {
        return [
            'id' => $t->id,
            'payable_type' => $t->payable_type,
            'payable_id' => $t->payable_id,
            'attempt_number' => $t->attempt_number,
            'amount' => (float) $t->amount,
            'currency' => $t->currency,
            'status' => $t->status,
            'payment_method_type' => $t->payment_method_type,
            'card_brand' => $t->card_brand,
            'card_last4' => $t->card_last4,
            'failure_message' => $t->failure_message,
            'created_at' => $t->created_at,
            'completed_at' => $t->completed_at,
        ];
    }
}
