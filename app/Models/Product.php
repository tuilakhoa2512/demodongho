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

    public function discountProducts()
    {
        return $this->belongsToMany(\App\Models\DiscountProduct::class, 'discount_product_details', 'product_id', 'discount_product_id')
            ->withPivot(['expiration_date', 'status'])
            ->withTimestamps();
    }

    // ================== DISCOUNT (ƯU ĐÃI) ==================

    /**
     * Lấy 1 ưu đãi đang áp dụng cho sản phẩm (nếu có)
     * Quy tắc:
     * - discount_products.status = 1 (chương trình đang hoạt động)
     * - pivot.status = 1 (chi tiết đang áp dụng)
     * - expiration_date null hoặc >= hôm nay
     * - Nếu có nhiều ưu đãi cùng lúc (trường hợp hiếm), lấy ưu đãi có rate cao nhất
     */
    public function activeDiscountProduct()
    {
        $today = now()->toDateString();

        return $this->discountProducts()
            ->where('discount_products.status', 1)
            ->wherePivot('status', 1)
            ->where(function ($q) use ($today) {
                $q->whereNull('discount_product_details.expiration_date')
                ->orWhere('discount_product_details.expiration_date', '>=', $today);
            })
            ->orderByDesc('discount_products.rate');
    }

    /** Accessor: ưu đãi đang áp dụng (object) */
    public function getActiveDiscountAttribute()
    {
        return $this->activeDiscountProduct()->first();
    }

    /** Accessor: text hiển thị ưu đãi */
    public function getDiscountLabelAttribute()
    {
        $d = $this->active_discount;
        if (!$d) return null;

        return "{$d->name} ({$d->rate}%)";
    }

    /** Accessor: giá sau ưu đãi (float|null) */
    public function getDiscountedPriceAttribute()
    {
        $d = $this->active_discount;
        if (!$d) return null;

        $rate = (float) $d->rate;
        $price = (float) $this->price;

        $newPrice = $price * (100 - $rate) / 100;

        // nếu bạn muốn làm tròn tiền:
        return round($newPrice, 0);
    }


}
