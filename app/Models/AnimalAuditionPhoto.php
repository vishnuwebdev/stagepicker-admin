<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalAuditionPhoto extends Model {

    public $table = "animal_audition_photos";

    public function audition() {
        return $this->belongsTo('App\Models\AnimalAudition', 'animal_audition_id', 'id');
    }
}
