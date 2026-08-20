<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditionInviteParticipant extends Model
{
    public $table = "audition_invite_participants";

    protected $fillable = [
        'audition_invite_id',
        'user_id',
        'post_audition_participant_id',
        'status',
        'responded_at',
    ];

    // pending | accepted | declined | expired
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    const STATUS_EXPIRED = 'expired';

    public function invite()
    {
        return $this->belongsTo('App\Models\AuditionInvite', 'audition_invite_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
