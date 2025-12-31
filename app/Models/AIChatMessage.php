<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIChatMessage extends Model
{
    protected $table = 'ai_chat'; 

    protected $fillable = [
        'session_id',
        'user_id',
        'role',
        'message',
        'products'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'products' => 'array' 
    ];
}
