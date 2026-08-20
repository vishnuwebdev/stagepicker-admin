<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditionInvite extends Model
{
    public $table = "audition_invites";

    protected $fillable = [
        'post_audition_id',
        'producer_id',
        'title',
        'location',
        'audition_date',
        'start_time',
        'end_time',
    ];

    public function post_audition()
    {
        return $this->belongsTo('App\Models\Postaudition', 'post_audition_id', 'id');
    }

    public function producer()
    {
        return $this->belongsTo('App\Models\User', 'producer_id', 'id');
    }

    public function participants()
    {
        return $this->hasMany('App\Models\AuditionInviteParticipant', 'audition_invite_id', 'id');
    }
}
