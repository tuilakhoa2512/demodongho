<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'user_id',
        'status',
        'payment_method',
        'total_price',

        'receiver_name',
        'receiver_email',
        'receiver_phone',
        'receiver_address',
        'province_id',
        'district_id',
        'ward_id',
    ];

    protected $casts = [
        'user_id'       => 'integer',
        'total_price'   => 'float',

        'province_id'   => 'integer',
        'district_id'   => 'integer',
        'ward_id'       => 'integer',
    ];


    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

   
    public function promotionRedemption()
    {
        return $this->hasOne(PromotionRedemption::class, 'order_id', 'id');
    }

 

    
    public function getBillDiscountAmountAttribute(): int
    {
        return (int) ($this->promotionRedemption?->discount_amount ?? 0);
    }

    
    public function getSubtotalAmountAttribute(): int
    {
        $sub = $this->promotionRedemption?->subtotal;
        if (!is_null($sub)) return (int) $sub;

        // fallback: sum details
        $sum = 0;
        foreach ($this->details as $d) {
            $sum += (int) ((float)$d->price * (int)$d->quantity);
        }
        return (int) $sum;
    }

   
    public function getGrandTotalAttribute(): int
    {
        if (!is_null($this->total_price)) {
            return (int) $this->total_price;
        }
        return max(0, $this->subtotal_amount - $this->bill_discount_amount);
    }
}
