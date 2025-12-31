<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountProductDetail extends Model
{
    protected $table = 'discount_product_details';

   
    public $incrementing = false;
    protected $primaryKey = null; 

    protected $fillable = [
        'discount_product_id',
        'product_id',
        'expiration_date',
        'status',
    ];

    /**
     * Quan hệ tới DiscountProduct
     */
    public function discountProduct()
    {
        return $this->belongsTo(DiscountProduct::class, 'discount_product_id');
    }

    /**
     * Quan hệ tới Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Kiểm tra bản ghi này có còn hiệu lực hay không
     *  - status = 1
     *  - expiration_date null hoặc >= hôm nay
     */
    public function isActive(): bool
    {
        if ($this->status != 1) {
            return false;
        }

        if (is_null($this->expiration_date)) {
            return true;
        }

        return $this->expiration_date >= date('Y-m-d');
    }
}
