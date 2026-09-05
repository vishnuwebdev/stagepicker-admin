<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalAuditionPhoto extends Model {

    public $table = "animal_audition_photos";

    // No FK constraints on these tables (see the migration's
    // docblock), and the controller mass-assigns via ::create()
    // for several of these models — open like most other models
    // in this app (Address/Booking/Order/...) rather than hand-
    // maintaining a $fillable whitelist.
    protected $guarded = [];

    public function audition() {
        return $this->belongsTo('App\Models\AnimalAudition', 'animal_audition_id', 'id');
    }
}
