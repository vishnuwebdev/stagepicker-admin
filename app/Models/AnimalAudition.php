<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalAudition extends Model {

    public $table = "animal_auditions";

    public function photos() {
        return $this->hasMany('App\Models\AnimalAuditionPhoto', 'animal_audition_id', 'id');
    }

    public function applications() {
        return $this->hasMany('App\Models\AnimalAuditionApplication', 'animal_audition_id', 'id');
    }

    public function species() {
        return $this->belongsTo('App\Models\AnimalSpecies', 'animal_species_id', 'id');
    }

    public function breed() {
        return $this->belongsTo('App\Models\AnimalBreed', 'animal_breed_id', 'id');
    }
}
