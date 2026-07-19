<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrewRole extends Model {

    public $table = "crew_roles";
	
	protected $fillable = ['role_title','category','role_type','skills','description'];
	
	/* public function RoleType()
    {
        return $this->hasOne('App\Models\RoleType', 'id', 'role_type');
    } */
}