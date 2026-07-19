<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model {

    public $table = "contact_requests";
	
	protected $fillable = ['name','email','message','mobile'];
	
	 
}