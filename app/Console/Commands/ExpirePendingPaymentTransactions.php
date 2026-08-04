<?php

namespace App\Console\Commands;

use App\Lib\StripePayment;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Last-resort safety net for the "money should never be stuck in limbo"
 * requirement. Two other paths already reconcile a payment as soon as
 * possible — the app's own sync_payment_status call right after the
 * PaymentSheet closes, and the Stripe webhook shortly after that — but
 * both depend on *something* actually reaching this server (app killed,
 * user loses signal mid-payment, webhook delivery lost). This command is
 * what guarantees an attempt doesn't sit "requires_payment"/"processing"
 * forever if both of those fail to arrive.
 *
 * For a PaymentIntent that was never confirmed, Stripe never captured any
 * funds — cancelling it (rather than "refunding") is the correct action
 * and is enough to guarantee the customer was never actually charged. If a
 * PaymentIntent *did* succeed on Stripe's side but we never heard about it
 * (both the sync call and the webhook were lost — extremely unlikely but
 * not impossible), this command re-checks with Stripe before canceling
 * anything, so it can never cancel a payment that actually went through.
 */
class ExpirePendingPaymentTransactions extends Command
{
    protected $signature = 'payments:expire-stale {--minutes=45 : How old a pending attempt must be before it is swept}';

    protected $description = 'Cancels payment attempts that have been stuck awaiting payment for too long, and cancels their linked order so no money is left in limbo.';

    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = Carbon::now()->subMinutes($minutes);

        $stale = PaymentTransaction::whereIn('status', [
                PaymentTransaction::STATUS_CREATED,
                PaymentTransaction::STATUS_REQUIRES_PAYMENT,
                PaymentTransaction::STATUS_PROCESSING,
            ])
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($stale->isEmpty()) {
            $this->info('No stale payment transactions found.');
            return 0;
        }

        $this->info("Found {$stale->count()} stale payment transaction(s) older than {$minutes} minutes.");

        $stripe = new StripePayment();

        foreach ($stale as $transaction) {
            try {
                $finalStatus = PaymentTransaction::STATUS_CANCELED;

                if ($transaction->gateway_intent_id) {
                    // Always re-check with Stripe first — never cancel
                    // blind. If it actually succeeded (webhook + sync both
                    // missed), record that instead of wrongly cancelling a
                    // completed payment.
                    $intent = $stripe->retrievePaymentIntent($transaction->gateway_intent_id);

                    if ($intent->status === 'succeeded') {
                        $finalStatus = PaymentTransaction::STATUS_SUCCEEDED;
                    } elseif (in_array($intent->status, ['canceled'], true)) {
                        $finalStatus = PaymentTransaction::STATUS_CANCELED;
                    } else {
                        // Still not settled on Stripe's side either — safe
                        // to cancel outright; no funds were captured.
                        $stripe->cancelPaymentIntent($transaction->gateway_intent_id);
                        $finalStatus = PaymentTransaction::STATUS_CANCELED;
                    }
                }

                $transaction->status = $finalStatus;
                $transaction->completed_at = now();
                if ($finalStatus === PaymentTransaction::STATUS_CANCELED && !$transaction->failure_message) {
                    $transaction->failure_message = "Payment attempt timed out after {$minutes} minutes and was automatically cancelled.";
                }
                $transaction->save();

                PaymentTransactionLog::create([
                    'payment_transaction_id' => $transaction->id,
                    'source' => PaymentTransactionLog::SOURCE_SCHEDULER,
                    'event_type' => 'expired_stale',
                    'status' => $finalStatus,
                    'raw_payload' => json_encode(['swept_after_minutes' => $minutes]),
                ]);

                $this->cascadeToPayable($transaction);

                $this->line("Transaction #{$transaction->id} -> {$finalStatus}");
            } catch (\Exception $e) {
                Log::error('ExpirePendingPaymentTransactions failed for transaction ' . $transaction->id . ': ' . $e->getMessage());
                $this->error("Transaction #{$transaction->id} failed to process: " . $e->getMessage());
            }
        }

        return 0;
    }

    /**
     * Same cancellation rule as PaymentController@cascadeToPayable (kept as
     * a small local copy rather than a shared trait, since this command's
     * only outcome is ever succeeded/canceled — no need to pull in the full
     * card-summary logic from the request-driven path).
     */
    private function cascadeToPayable(PaymentTransaction $transaction)
    {
        if ($transaction->payable_type !== 'order' || !$transaction->payable_id) {
            return;
        }

        $order = Order::find($transaction->payable_id);
        if (!$order || $order->status !== 'pending_payment') {
            return;
        }

        if ($transaction->status === PaymentTransaction::STATUS_SUCCEEDED) {
            $order->payment_status = 'approved';
            $order->status = 'placed';
        } else {
            $order->payment_status = 'cancelled';
            $order->status = 'cancelled';
        }
        $order->save();
    }
}
