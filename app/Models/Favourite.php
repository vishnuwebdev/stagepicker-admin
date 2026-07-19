<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model {

    public $table = "favourites";
    
    public function audition() { 
		return $this->belongsTo( 'App\Models\Postaudition', 'post_audition_id', 'id' );
	}
	
}