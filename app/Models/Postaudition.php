<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postaudition extends Model {

    public $table = "post_auditions";
	
	
	public function post_auditions_photographies() { 
		return $this->hasMany( 'App\Models\PostauditionPhotography', 'post_auditions_id', 'id' );
	}
	
	public function post_auditions_apply() { 
		return $this->hasMany( 'App\Models\Auditionparticipant', 'post_audition_id', 'id' );
	}
	
	
	public function post_auditions_viewed() { 
		return $this->hasMany( 'App\Models\AuditionView', 'post_audition_id', 'id' );
	}
	
	public function favourite() { 
		return $this->hasMany( 'App\Models\Favourite', 'post_audition_id', 'id' );
	}
	
	
}