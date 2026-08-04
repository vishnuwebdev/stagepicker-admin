<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Generic payment-gateway transaction ledger — backs the reusable Stripe
 * integration used by the Flutter app's marketplace checkout today, and
 * intended to be reused by any future money-taking flow (subscriptions,
 * contributions, etc.) via `payable_type` / `payable_id` rather than a
 * marketplace-only table.
 *
 * Design choices, spelled out because this table holds money-movement
 * history and needs to be trustworthy:
 *
 * - **One row per attempt, never overwritten into a different attempt.**
 *   If a user retries a declined/abandoned payment, a NEW row (with a new
 *   Stripe PaymentIntent and incrementing `attempt_number`) is created
 *   rather than reusing the old one. This is what lets both the user and
 *   admin see a full attempt history ("tried, failed, tried again,
 *   succeeded") instead of only the final state.
 * - **`status` transitions are updated in place *within* a single attempt**
 *   (created -> requires_payment -> processing -> succeeded|failed|canceled)
 *   as Stripe reports progress via webhook or the app's post-PaymentSheet
 *   sync call — every transition is additionally appended to
 *   `payment_transaction_logs` so nothing is lost even if a later update
 *   overwrites the row's current status.
 * - **`payable_type`/`payable_id`** is a lightweight polymorphic reference
 *   (not a formal Eloquent morph, to keep this simple/greppable) — e.g.
 *   payable_type = 'order', payable_id = orders.id for the merchandise
 *   checkout flow this ships with first.
 * - **No refund workflow is modeled here on purpose** (per product ask —
 *   refunds are handled manually by admin directly in the Stripe
 *   dashboard). `status` does include 'refunded' purely so the refund
 *   webhook (charge.refunded) can keep this table's history accurate when
 *   admin does refund something out-of-band — it is a read-only reflection
 *   of that action, not a feature this app exposes.
 */
class CreatePaymentTransactionsTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');

                // Lightweight polymorphic reference — e.g. 'order' / orders.id.
                // Nullable so this table can also back a payment that isn't
                // tied to any domain row yet (kept flexible for reuse).
                $table->string('payable_type')->nullable();
                $table->unsignedBigInteger('payable_id')->nullable();

                // Which retry number this is for the same payable — 1 on the
                // first attempt, incremented on every subsequent create-intent
                // call for the same payable_type+payable_id+user.
                $table->unsignedInteger('attempt_number')->default(1);

                $table->string('gateway')->default('stripe');
                // Stripe PaymentIntent id (pi_...) — the source of truth we
                // reconcile against via webhook + sync + the expiry sweep.
                $table->string('gateway_intent_id')->nullable();
                $table->string('gateway_customer_id')->nullable();

                $table->decimal('amount', 10, 2);
                $table->string('currency', 8)->default('usd');

                // created -> requires_payment -> processing ->
                // succeeded | failed | canceled | refunded
                $table->string('status')->default('created');

                // card | apple_pay | google_pay — filled once Stripe reports
                // which payment method the PaymentSheet actually used.
                $table->string('payment_method_type')->nullable();
                $table->string('card_brand')->nullable();
                $table->string('card_last4', 4)->nullable();

                $table->string('failure_code')->nullable();
                $table->text('failure_message')->nullable();

                // Passed to Stripe's create-PaymentIntent call so a retried
                // HTTP request (flaky network, double-tap) can't accidentally
                // create two PaymentIntents for the same attempt.
                $table->string('idempotency_key')->nullable()->unique();

                // Free-form context snapshot (order_code, item titles, app
                // version, etc.) — for admin/debugging, not used in logic.
                $table->text('metadata')->nullable();

                $table->timestamp('initiated_at')->nullable();
                $table->timestamp('completed_at')->nullable();

                $table->timestamps();

                $table->index('user_id');
                $table->index(['payable_type', 'payable_id']);
                $table->index('gateway_intent_id');
                $table->index('status');
            });
        }

        // Full audit trail of every status-changing event seen for a
        // transaction (webhook deliveries, the app's client-side sync call,
        // and the stale-payment sweep command) — kept even after the parent
        // row's status is overwritten, so admin can always answer "what
        // exactly happened to this payment, in order."
        if (!Schema::hasTable('payment_transaction_logs')) {
            Schema::create('payment_transaction_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payment_transaction_id');

                // webhook | client_sync | scheduler
                $table->string('source');
                // e.g. payment_intent.succeeded, client_sync, expired_stale
                $table->string('event_type')->nullable();
                $table->string('status');
                // Raw Stripe event/PaymentIntent payload (JSON) for debugging
                // disputes/support tickets — never contains full card numbers,
                // Stripe itself never sends those.
                $table->longText('raw_payload')->nullable();

                $table->timestamps();

                $table->index('payment_transaction_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('payment_transaction_logs');
        Schema::dropIfExists('payment_transactions');
    }
}
