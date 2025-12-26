<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIChatMessage extends Model
{
    protected $table = 'ai_chat'; 

    protected $fillable = [
        'session_id',
        'role',
        'message'
    ];
}
