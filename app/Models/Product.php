<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $table = 'products';

    /**
     * Các cột được phép gán mass-assignment
     */
    protected $fillable = [
        'name',
        'description',
        'strap_material',
        'dial_size',
        'gender',
        'category_id',
        'brand_id',
        'storage_detail_id',
        'price',
        'quantity',
        'stock_status', // selling / sold_out / stopped
        'status',       // 1 = active, 0 = inactive
    ];

    
    protected $casts = [
        'dial_size'    => 'float',
        'price'        => 'float',
        'quantity'     => 'integer',
    ];

    /* ================== QUAN HỆ ================== */

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

    /* ================== ACCESSOR ẢNH ================== */

    /**
     * Ảnh chính hiển thị ngoài trang chủ / chi tiết
     * Ưu tiên image_1 trong bảng product_images
     */
    public function getMainImageUrlAttribute()
    {
        $image = $this->productImage;

        if ($image && $image->image_1) {
            // Đường dẫn lưu trong DB là "products/{id}/xxx.jpg"
            return Storage::url($image->image_1); // -> /storage/products/...
        }

        // Ảnh fallback nếu chưa có ảnh
        return asset('frontend/images/rolex1.jpg');
    }

    /**
     * Ảnh khi hover (image_2 nếu có, ngược lại dùng image_1)
     */
    public function getHoverImageUrlAttribute()
    {
        $image = $this->productImage;

        if ($image) {
            if ($image->image_2) {
                return Storage::url($image->image_2);
            }
            if ($image->image_1) {
                return Storage::url($image->image_1);
            }
        }

        // Nếu không có ảnh trong DB thì dùng luôn main_image_url
        return $this->main_image_url;
    }
}
