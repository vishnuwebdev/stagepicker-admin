<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {

    public $table = "order_items";

    protected $guarded = [];

    public function order() {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function merchandise() {
        return $this->belongsTo('App\Models\Merchandise', 'merchandise_id', 'id');
    }
}
