<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalAuditionApplicationProfile extends Model {

    public $table = "animal_audition_application_profiles";

    public function application() {
        return $this->belongsTo('App\Models\AnimalAuditionApplication', 'animal_audition_application_id', 'id');
    }

    public function profile() {
        return $this->belongsTo('App\Models\AnimalProfile', 'animal_profile_id', 'id');
    }
}
