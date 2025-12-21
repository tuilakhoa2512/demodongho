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

        'discount_bill_id',
        'discount_bill_rate',
        'discount_bill_value',

        // ===== receiver fields (mới thêm) =====
        'receiver_name',
        'receiver_email',
        'receiver_phone',
        'receiver_address',
        'province_id',
        'district_id',
        'ward_id',
    ];

    protected $casts = [
        'user_id'             => 'integer',
        'total_price'         => 'float',
        'discount_bill_id'    => 'integer',
        'discount_bill_rate'  => 'integer',
        'discount_bill_value' => 'integer',

        'province_id' => 'integer',
        'district_id' => 'integer',
        'ward_id'     => 'integer',
    ];

    // ===== Relationships =====
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function user()
    {
        // bạn đang login theo Session::get('id'), nhưng quan hệ vẫn cần cho admin/xem đơn
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function discountBill()
    {
        return $this->belongsTo(\App\Models\DiscountBill::class, 'discount_bill_id', 'id');
    }
}
