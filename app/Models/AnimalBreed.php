<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalBreed extends Model {

    public $table = "animal_breeds";

    public function species() {
        return $this->belongsTo('App\Models\AnimalSpecies', 'animal_species_id', 'id');
    }
}
