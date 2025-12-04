<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id',
        'fullname',
        'email',
        'password',
        'phone',
        'address',
        'province_id',
        'district_id',
        'ward_id',
        'status',
        'image',
    ];

    protected $hidden = [
        'password',
    ];

    // Quan hệ: User thuộc về 1 Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    // Province
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    // District
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    // Ward
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    // Orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    // Reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    // Favorites
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id', 'id');
    }

    // Cart
    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }

    // Social Login
    public function social()
    {
        return $this->hasMany(Social::class, 'user_id', 'id');
    }
}
