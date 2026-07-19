<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchandiseImage extends Model {

    public $table = "merchandise_images";
	
	public function get_merchandise() { 
		return $this->belongsTo( 'App\Models\Merchandise','merchandise_id');
	}
}