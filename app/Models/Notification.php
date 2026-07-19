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

    protected $casts = [
        'is_read' => 'boolean',
        'custom_data' => 'array',
    ];

     public $table = "notifications";
}


