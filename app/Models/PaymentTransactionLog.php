<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Append-only audit trail for a PaymentTransaction — one row per event seen
 * (Stripe webhook delivery, the app's post-PaymentSheet sync call, or the
 * stale-payment sweep command). Never updated or deleted; this is what lets
 * admin reconstruct exactly what happened to a payment, in order, even
 * after the parent row's `status` has moved on.
 */
class PaymentTransactionLog extends Model
{
    public $table = "payment_transaction_logs";

    protected $guarded = [];

    const SOURCE_WEBHOOK = 'webhook';
    const SOURCE_CLIENT_SYNC = 'client_sync';
    const SOURCE_SCHEDULER = 'scheduler';

    public function transaction()
    {
        return $this->belongsTo('App\Models\PaymentTransaction', 'payment_transaction_id', 'id');
    }
}
