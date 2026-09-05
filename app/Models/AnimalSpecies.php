<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalSpecies extends Model {

    public $table = "animal_species";

    // No FK constraints on these tables (see the migration's
    // docblock), and the controller mass-assigns via ::create()
    // for several of these models — open like most other models
    // in this app (Address/Booking/Order/...) rather than hand-
    // maintaining a $fillable whitelist.
    protected $guarded = [];

    public function breeds() {
        return $this->hasMany('App\Models\AnimalBreed', 'animal_species_id', 'id');
    }
}
