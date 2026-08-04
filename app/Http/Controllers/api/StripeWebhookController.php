<?php

namespace App\Http\Controllers\Api;

use App\Lib\StripePayment;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Stripe webhook receiver — the source of truth for payment finalization.
 *
 * The app's own sync_payment_status call (PaymentController) gives the user
 * an instant result in the common case, but it only runs if the app is
 * still alive and online right after the PaymentSheet closes. This
 * endpoint is what guarantees a transaction/order gets finalized even if
 * that call never happens — Stripe retries webhook delivery on its own for
 * hours if this endpoint is briefly down, which the client-side call
 * cannot do.
 *
 * Registered as a public, unauthenticated POST route (Stripe calls it
 * directly, no user session exists) — trust comes entirely from verifying
 * the `Stripe-Signature` header against STRIPE_WEBHOOK_SECRET, never from
 * the payload alone.
 */
class StripeWebhookController extends BaseController
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('app.stripe_webhook_secret');

        if (empty($endpointSecret)) {
            // Fail closed — without a configured secret we cannot tell a
            // real Stripe event from a forged POST, so refuse to process
            // rather than silently trusting unverified input.
            \Log::error('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured.');
            return response('Webhook not configured', 500);
        }

        try {
            $event = StripePayment::constructWebhookEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Error\SignatureVerification $e) {
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            return response('Webhook error', 400);
        }

        $object = $event->data->object;

        switch ($event->type) {
            case 'payment_intent.succeeded':
            case 'payment_intent.payment_failed':
            case 'payment_intent.canceled':
                $this->reconcilePaymentIntent($object, $event->type);
                break;

            case 'charge.refunded':
                $this->recordRefund($object);
                break;

            default:
                // Not a type we act on — acknowledge so Stripe stops
                // retrying it, but nothing to reconcile.
                break;
        }

        return response('OK', 200);
    }

    private function reconcilePaymentIntent($intent, $eventType)
    {
        $transaction = PaymentTransaction::where('gateway_intent_id', $intent->id)->first();
        if (!$transaction) {
            // Nothing local to reconcile against (e.g. a PaymentIntent
            // created outside this flow) — acknowledge and move on.
            return;
        }

        if ($transaction->isTerminal()) {
            // Already finalized (most likely by sync_payment_status just
            // moments earlier) — still log the webhook delivery for the
            // audit trail, but don't re-run the payable cascade.
            PaymentTransactionLog::create([
                'payment_transaction_id' => $transaction->id,
                'source' => PaymentTransactionLog::SOURCE_WEBHOOK,
                'event_type' => $eventType,
                'status' => $transaction->status,
                'raw_payload' => json_encode($intent->jsonSerialize()),
            ]);
            return;
        }

        $controller = new PaymentController();
        $controller->applyIntentToTransaction($transaction, $intent, PaymentTransactionLog::SOURCE_WEBHOOK, $eventType);
    }

    /**
     * Refunds are performed by admin directly in the Stripe dashboard (this
     * app doesn't expose a refund action — see product ask). This handler
     * only keeps our own transaction history honest when that happens, so
     * admin/user-facing history doesn't silently disagree with Stripe.
     */
    private function recordRefund($charge)
    {
        $paymentIntentId = is_object($charge) ? ($charge->payment_intent ?? null) : null;
        if (!$paymentIntentId) {
            return;
        }

        $transaction = PaymentTransaction::where('gateway_intent_id', $paymentIntentId)->first();
        if (!$transaction) {
            return;
        }

        $transaction->status = PaymentTransaction::STATUS_REFUNDED;
        $transaction->save();

        PaymentTransactionLog::create([
            'payment_transaction_id' => $transaction->id,
            'source' => PaymentTransactionLog::SOURCE_WEBHOOK,
            'event_type' => 'charge.refunded',
            'status' => $transaction->status,
            'raw_payload' => json_encode($charge->jsonSerialize()),
        ]);
    }
}
