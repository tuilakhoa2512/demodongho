<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $table = 'products';

    // ✅ giữ nguyên appends ảnh
    protected $appends = ['main_image_url', 'hover_image_url'];

    /**
     * Các cột được phép gán mass-assignment
     * NOTE:
     * - discounted_price vẫn giữ vì DB đang có cột này / form có thể còn dùng tạm
     * - Nhưng logic ưu đãi chuẩn marketplace sẽ tính runtime bằng PromotionService
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
        'discounted_price', // ⚠️ legacy field (không dùng làm logic chính nữa)
        'quantity',
        'stock_status', // selling / sold_out / stopped
        'status',       // 1 = active, 0 = inactive
    ];

    protected $casts = [
        'dial_size' => 'float',
        'price'     => 'float',
        'quantity'  => 'integer',
    ];

    /* ================== QUAN HỆ ================== */

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id')
            ->where('status', 1);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')
            ->where('status', 1);
    }

    public function storageDetail()
    {
        return $this->belongsTo(StorageDetail::class, 'storage_detail_id', 'id');
    }

    public function productImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id');
    }

    // ✅ giữ nguyên accessor lấy image_1, image_2 (nếu bạn đang dùng nơi khác)
    public function getImage1Attribute()
    {
        return optional($this->productImage)->image_1;
    }

    public function getImage2Attribute()
    {
        return optional($this->productImage)->image_2;
    }

    /* ================== ACCESSOR ẢNH ================== */

    /**
     * Ảnh chính hiển thị ngoài trang chủ / chi tiết
     * Ưu tiên image_1 trong bảng product_images
     */
    public function getMainImageUrlAttribute()
    {
        if ($this->productImage && $this->productImage->image_1) {
            return asset('storage/' . $this->productImage->image_1);
        }

        return asset('frontend/images/rolex1.jpg');
    }

    public function getHoverImageUrlAttribute()
    {
        if ($this->productImage) {
            if ($this->productImage->image_2) {
                return asset('storage/' . $this->productImage->image_2);
            }

            if ($this->productImage->image_1) {
                return asset('storage/' . $this->productImage->image_1);
            }
        }

        return $this->main_image_url;
    }

    /* ================== ƯU ĐÃI (HỆ MỚI) ==================
     * ✅ Không đặt logic promotion trong Model để tránh query ngầm + khó kiểm soát
     * ✅ Giá sale / promo lấy bằng PromotionService (runtime):
     *    app( \App\Services\PromotionService::class )->calcProductFinalPrice($product)
     *
     * ❌ Đã loại bỏ toàn bộ quan hệ & accessor của hệ cũ:
     *    discountProducts(), activeDiscountProduct(), active_discount, discount_label, getDiscountedPriceAttribute(), discounts()
     */
}
