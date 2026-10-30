<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'ref_id',
        'is_read',
        'custom_data',
    ];

    // Rows are inserted from several controllers with raw DB::table()
    // inserts that don't agree on types (ref_id is an int in the invite /
    // animal-audition flows, a string or null elsewhere). Normalise what the
    // API returns so the mobile app always gets the same JSON shape.
    protected $casts = [
        'user_id' => 'integer',
        'ref_id' => 'string',
        'is_read' => 'boolean',
        'custom_data' => 'array',
    ];

     public $table = "notifications";
}


