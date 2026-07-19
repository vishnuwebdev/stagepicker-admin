<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {

    public $table = "reports";
	
	
	public function post_auditions() { 
		return $this->belongsTo( 'App\Models\Postaudition', 'post_audition_id', 'id' );
	}
	 
	
	
}