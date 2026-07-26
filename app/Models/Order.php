<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    public $table = "orders";

    protected $guarded = [];

    public function items() {
        return $this->hasMany('App\Models\OrderItem', 'order_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
