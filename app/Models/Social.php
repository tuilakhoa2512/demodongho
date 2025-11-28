<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'provider_user_id',
        'provider',
        'provider_user_email',
        'user_id',
    ];

    protected $primaryKey = 'id';
    protected $table = 'social';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
