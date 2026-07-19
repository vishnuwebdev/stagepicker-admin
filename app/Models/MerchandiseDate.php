<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchandiseDate extends Model {

    public $table = "merchandise_dates";
	
	public function get_merchandise() { 
		return $this->belongsTo( 'App\Models\Merchandise','merchandise_id');
	}
}