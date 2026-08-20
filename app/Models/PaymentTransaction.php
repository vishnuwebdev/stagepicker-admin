<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per payment *attempt* against the Stripe gateway. See the
 * create_payment_transactions_tables migration's doc comment for the full
 * design rationale (why retries are new rows, why status lives here while
 * the full event trail lives in PaymentTransactionLog, etc.).
 *
 * Deliberately NOT named after the pre-existing `App\Models\Transaction`
 * class (which — despite its filename — actually defines `class
 * Subscription` bound to the legacy `transactions` table used by
 * UserController@subscription/addstripeammount). That table predates this
 * feature and is left untouched; this is a separate, clean table so the two
 * don't get confused.
 */
class PaymentTransaction extends Model
{
    public $table = "payment_transactions";

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'attempt_number' => 'integer',
    ];

    const STATUS_CREATED = 'created';
    const STATUS_REQUIRES_PAYMENT = 'requires_payment';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REFUNDED = 'refunded';

    // Statuses that mean "money isn't settled and this attempt is done" —
    // used by the stale-payment sweep to know what's still eligible to be
    // auto-canceled, and by the UI to decide whether "retry" should show.
    const TERMINAL_STATUSES = [
        self::STATUS_SUCCEEDED,
        self::STATUS_FAILED,
        self::STATUS_CANCELED,
        self::STATUS_REFUNDED,
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany('App\Models\PaymentTransactionLog', 'payment_transaction_id', 'id')
            ->orderBy('id', 'asc');
    }

    /**
     * Resolves the linked domain row for this attempt (`order` and
     * `booking` are the real payable_types today). Kept as a plain lookup
     * rather than a formal morphTo so payable_type strings stay simple and
     * greppable.
     */
    public function payable()
    {
        if ($this->payable_type === 'order' && $this->payable_id) {
            return Order::find($this->payable_id);
        }
        if ($this->payable_type === 'booking' && $this->payable_id) {
            return Booking::find($this->payable_id);
        }
        return null;
    }

    public function isTerminal()
    {
        return in_array($this->status, self::TERMINAL_STATUSES, true);
    }
}
