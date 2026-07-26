<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model {

    public $table = "addresses";

    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
