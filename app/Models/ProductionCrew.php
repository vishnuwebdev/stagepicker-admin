<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionCrew extends Model {

    public $table = "production_crews";
	 
	public function post_auditions_apply() { 
		return $this->hasMany( 'App\Models\Auditionparticipant', 'post_audition_id', 'id' )->where('audition_type',2);
	}
	 
	public function post_auditions_viewed() { 
		return $this->hasMany( 'App\Models\AuditionView', 'post_audition_id', 'id' )->where('audition_type',2);
	}
	
	public function favourite() { 
		return $this->hasMany( 'App\Models\Favourite', 'post_audition_id', 'id' );
	}
	
	public function category() { 
		return $this->hasMany( 'App\Models\Category', 'id', 'category' );
	}
	
	
}