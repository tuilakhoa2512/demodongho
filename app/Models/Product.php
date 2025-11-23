<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Storage;
use App\Models\ProductImage;

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

    protected $appends = [
        'main_image_url',
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
        return $this->hasOne(ProductImage::class, 'product_id', 'id');
    }


    public function getMainImageUrlAttribute()
    {
        $imageRecord = $this->productImage;

        if (!$imageRecord || !$imageRecord->main_image) {
            return asset('frontend/images/rolex1.jpg');
        }

        $mainImage = $imageRecord->main_image;

        if (strpos($mainImage, 'products/') === 0) {
            $relativePath = $mainImage;
        } else {
            $relativePath = 'products/' . $this->id . '/' . $mainImage;
        }

        return asset('storage/' . $relativePath);
    }
}
