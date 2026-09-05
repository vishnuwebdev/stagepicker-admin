<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalAuditionApplicationProfile extends Model {

    public $table = "animal_audition_application_profiles";

    // No FK constraints on these tables (see the migration's
    // docblock), and the controller mass-assigns via ::create()
    // for several of these models — open like most other models
    // in this app (Address/Booking/Order/...) rather than hand-
    // maintaining a $fillable whitelist.
    protected $guarded = [];

    public function application() {
        return $this->belongsTo('App\Models\AnimalAuditionApplication', 'animal_audition_application_id', 'id');
    }

    public function profile() {
        return $this->belongsTo('App\Models\AnimalProfile', 'animal_profile_id', 'id');
    }
}
