<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model {

    public $table = "merchandise";
	
	public function get_image() { 
		return $this->hasMany( 'App\Models\MerchandiseImage','merchandise_id','id');
	}
	
	public function get_date() { 
		return $this->hasMany( 'App\Models\MerchandiseDate','merchandise_id','id');
	}
	
}