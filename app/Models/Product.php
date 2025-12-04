<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'storage_detail_id', 
        'category_id',
        'brand_id',
        'name',
        'description',
        'strap_material',
        'dial_size',
        'gender',
        'price',
        'quantity',
        'status',            // 1 = active, 0 = inactive 
    ];

   

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

   
    public function storageDetail()
    {
        return $this->belongsTo(StorageDetail::class, 'storage_detail_id', 'id');
    }


    public function productImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id');
    }



    // Ảnh chính
    public function getMainImageUrlAttribute()
    {
        // main_image là accessor bên ProductImage
        $path = optional($this->productImage)->main_image;

        if ($path) {
            return Storage::url($path); // link /storage/...
        }

        return asset('frontend/images/rolex1.jpg');
    }

    // Ảnh khi hover
    public function getHoverImageUrlAttribute()
    {
        $images = optional($this->productImage)->images ?? [];

        if (count($images) >= 2) {
            return Storage::url($images[1]);   // ảnh 2
        }

        if (count($images) === 1) {
            return Storage::url($images[0]);   // fallback ảnh 1
        }

        return $this->main_image_url;
    }
}
