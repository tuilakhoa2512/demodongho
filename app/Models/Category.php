<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // 1 danh mục có nhiều product
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
