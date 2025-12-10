<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountProduct extends Model
{
    protected $table = 'discount_products';

    protected $fillable = [
        'name',
        'rate',
        'status',
    ];

    /**
     * Một DiscountProduct có nhiều dòng chi tiết (áp dụng cho nhiều sản phẩm)
     */
    public function details()
    {
        return $this->hasMany(DiscountProductDetail::class, 'discount_product_id');
    }

    /**
     * Nhiều sản phẩm được giảm theo 1 chương trình
     * (belongsToMany qua bảng discount_product_details)
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_product_details', 'discount_product_id', 'product_id')
                    ->withPivot(['expiration_date', 'status'])
                    ->withTimestamps();
    }

    /**
     * Scope: chỉ lấy các chương trình giảm giá sản phẩm còn hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
