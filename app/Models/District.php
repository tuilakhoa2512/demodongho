<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $primaryKey = 'id';

    protected $fillable = [
        'province_id',
        'code',
        'name',
        'type',
    ];

    // District thuộc 1 Province
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    // Một district có nhiều wards
    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_id', 'id');
    }

    // Một district có nhiều users
    public function users()
    {
        return $this->hasMany(User::class, 'district_id', 'id');
    }
}
