<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    public $table = "roles";
	
	protected $fillable = ['role_title','role_type','gender','age_min','age_max','skills','character_description','nudity_or_bareness'];
}