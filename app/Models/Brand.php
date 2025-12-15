<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'image',
        'description',
        'brand_slug',
        'status',
    ];

    // Một brand có nhiều product
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id')->where('status',1);
    }
}
