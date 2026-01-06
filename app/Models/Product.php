<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    /**
     * ✅ appends cho URL ảnh (giữ nguyên cho view/front-end)
     */
    protected $appends = ['main_image_url', 'hover_image_url'];

    /**
     * Mass assignment
     * - discounted_price: legacy (tạm giữ nếu DB/form còn), KHÔNG dùng làm logic ưu đãi mới
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
        'discounted_price', // legacy
        'quantity',
        'stock_status', // selling / sold_out / stopped
        'status',       // 1 = active, 0 = inactive
    ];

    protected $casts = [
        'dial_size' => 'float',
        'price'     => 'float',
        'discounted_price' => 'float',
        'quantity'  => 'integer',
        'status'    => 'integer',
    ];

    /* ================== RELATIONSHIPS ================== */

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

    /* ================== IMAGE ACCESSORS ================== */

    // Nếu nơi khác đang gọi $product->image_1 / image_2 thì giữ lại
    public function getImage1Attribute()
    {
        return $this->productImage?->image_1;
    }

    public function getImage2Attribute()
    {
        return $this->productImage?->image_2;
    }

    /**
     * Ảnh chính: ưu tiên image_1, fallback ảnh default
     */
    public function getMainImageUrlAttribute(): string
    {
        $img = $this->productImage?->image_1;
        if (!empty($img)) {
            return asset('storage/' . $img);
        }
        return asset('frontend/images/rolex1.jpg');
    }

    /**
     * Ảnh hover: ưu tiên image_2, fallback image_1, cuối cùng fallback main_image_url
     */
    public function getHoverImageUrlAttribute(): string
    {
        $img2 = $this->productImage?->image_2;
        if (!empty($img2)) {
            return asset('storage/' . $img2);
        }

        $img1 = $this->productImage?->image_1;
        if (!empty($img1)) {
            return asset('storage/' . $img1);
        }

        return $this->main_image_url;
    }

    /* ================== PROMOTION (NEW SYSTEM) ==================
     *
     * ✅ KHÔNG đặt logic promotion trong Model để tránh query ngầm khó debug.
     * ✅ Khi cần giá sau ưu đãi:
     *    $pack = app(\App\Services\PromotionService::class)->calcProductFinalPrice($product);
     *    $finalPrice = $pack['final_price'];
     *
     * ❌ Không còn quan hệ / accessor của hệ cũ.
     */
}