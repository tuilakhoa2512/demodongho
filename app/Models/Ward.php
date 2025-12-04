<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ward extends Model
{
    use HasFactory;

    protected $table = 'wards';

    protected $primaryKey = 'id';

    protected $fillable = [
        'district_id',
        'code',
        'name',
        'type',
    ];

    // Ward thuộc một District
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    // Một ward có nhiều users
    public function users()
    {
        return $this->hasMany(User::class, 'ward_id', 'id');
    }
}
