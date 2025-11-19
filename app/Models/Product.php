<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    
    protected $fillable = [
        'name',
        'description',
        'strap_material',
        'dial_size', 
        'gender',
        'category_id',
        'brand_id',
        'storage_id',
        'price',   
        'quantity',
       
    ];

    

    // QUAN HỆ

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

 
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

   
    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

     public function productImage()
    {
        return $this->hasOne(ProductImage::class);
    }
}
