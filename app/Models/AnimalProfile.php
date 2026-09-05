<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalProfile extends Model {

    public $table = "animal_profiles";

    public function photos() {
        return $this->hasMany('App\Models\AnimalProfilePhoto', 'animal_profile_id', 'id');
    }

    public function species() {
        return $this->belongsTo('App\Models\AnimalSpecies', 'animal_species_id', 'id');
    }

    public function breed() {
        return $this->belongsTo('App\Models\AnimalBreed', 'animal_breed_id', 'id');
    }
}
