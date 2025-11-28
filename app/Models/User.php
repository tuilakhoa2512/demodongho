<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'fullname',
        'email',
        'password',
        'phone',
        'address',
        'district',
        'ward',
        'province',
        'status',
        'image',
    ];

    protected $primaryKey = 'id';
    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    // Quan hệ Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    // Quan hệ cho Google, Social login
    public function socialAccounts()
    {
        return $this->hasMany(Social::class, 'user_id', 'id');
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

    // Cart
    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }
}
