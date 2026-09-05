<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalAuditionApplication extends Model {

    public $table = "animal_audition_applications";

    // No FK constraints on these tables (see the migration's
    // docblock), and the controller mass-assigns via ::create()
    // for several of these models — open like most other models
    // in this app (Address/Booking/Order/...) rather than hand-
    // maintaining a $fillable whitelist.
    protected $guarded = [];

    public function audition() {
        return $this->belongsTo('App\Models\AnimalAudition', 'animal_audition_id', 'id');
    }

    // Rows in the join table (animal_audition_application_profiles),
    // useful when you need the join row's own id/timestamps.
    public function applicationProfiles() {
        return $this->hasMany('App\Models\AnimalAuditionApplicationProfile', 'animal_audition_application_id', 'id');
    }

    // The actual AnimalProfile records submitted with this application.
    public function profiles() {
        return $this->belongsToMany(
            'App\Models\AnimalProfile',
            'animal_audition_application_profiles',
            'animal_audition_application_id',
            'animal_profile_id'
        );
    }
}
