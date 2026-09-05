<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalProfilePhoto extends Model {

    public $table = "animal_profile_photos";

    public function profile() {
        return $this->belongsTo('App\Models\AnimalProfile', 'animal_profile_id', 'id');
    }
}
