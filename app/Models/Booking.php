<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per class enrollment (see CreateBookingsTable migration doc
 * comment for the full design rationale). Plays the same role `Order` plays
 * for merchandise checkout.
 */
class Booking extends Model
{
    public $table = "bookings";

    protected $guarded = [];

    protected $casts = [
        'price_snapshot' => 'float',
        'amount' => 'float',
        'quantity' => 'integer',
    ];

    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    const MODE_ONLINE = 'online';
    const MODE_OFFLINE = 'offline';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
