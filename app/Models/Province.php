<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';

    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'name',
        'type',
    ];

    // Một tỉnh có nhiều quận/huyện
    public function districts()
    {
        return $this->hasMany(District::class, 'province_id', 'id');
    }

    // Một tỉnh có nhiều user
    public function users()
    {
        return $this->hasMany(User::class, 'province_id', 'id');
    }
}
