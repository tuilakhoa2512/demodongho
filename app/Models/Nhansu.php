<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhanSu extends Model
{
    use HasFactory;

    protected $table = 'nhansu';

    protected $fillable = [
        'role_id',
        'fullname',
        'email',
        'password',
        'phone',
        'status',
        'created_by',
    ];

    protected $hidden = [
        'password',
    ];
}