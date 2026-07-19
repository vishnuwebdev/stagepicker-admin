<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentLogin extends Model {

    public $table = "recent_logins";
	
	public function users()
    {
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }
	
}