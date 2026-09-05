<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalSpecies extends Model {

    public $table = "animal_species";

    public function breeds() {
        return $this->hasMany('App\Models\AnimalBreed', 'animal_species_id', 'id');
    }
}
