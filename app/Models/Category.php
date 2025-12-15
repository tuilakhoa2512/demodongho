<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // Tên bảng
    protected $table = 'categories';

    // Khóa chính
    protected $primaryKey = 'id';

    // Cho phép fill
    protected $fillable = [
        'name',
        'description',
        'category_slug',
        'status',
    ];

    // Quan hệ 1 category có nhiều products
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id')->where('status',1);
    }
}
