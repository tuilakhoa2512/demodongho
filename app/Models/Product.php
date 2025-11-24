<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage as FileStorage;
use App\Models\Storage as StorageModel;          

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
        return $this->belongsTo(StorageModel::class, 'storage_id', 'id');
    }

    public function productImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id');
    }

    public function getMainImageUrlAttribute()
    {
        $img = $this->productImage;

        if ($img && $img->image_1) {
            return FileStorage::url($img->image_1);
        }

        return asset('frontend/images/noimage.jpg');
    }


    public function getHoverImageUrlAttribute()
    {
        $img = $this->productImage;

        if ($img && $img->image_2) {
            return FileStorage::url($img->image_2);
        }

        return $this->main_image_url;
    }
}
