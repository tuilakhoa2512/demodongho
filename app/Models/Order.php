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

        // ===== receiver fields =====
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

    // ===================== Relationships =====================

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * ✅ NEW promotion system:
     * 1 đơn hàng thường có 0 hoặc 1 redemption (ghi nhận ưu đãi đã áp dụng)
     * Bảng: promotion_redemptions
     * FK theo DB của bạn: order_id
     */
    public function promotionRedemption()
    {
        return $this->hasOne(PromotionRedemption::class, 'order_id', 'id');
    }

    // ===================== Helpers (optional) =====================

    /**
     * Số tiền giảm hóa đơn (snapshot) theo hệ mới.
     * Nếu chưa có redemption => 0.
     */
    public function getBillDiscountAmountAttribute(): int
    {
        return (int) ($this->promotionRedemption?->discount_amount ?? 0);
    }

    /**
     * Tổng trước giảm (subtotal) nếu redemption có lưu subtotal.
     * Nếu chưa có => fallback từ order_details.
     */
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

    /**
     * Tổng sau giảm:
     * Ưu tiên total_price (đã chốt lúc đặt).
     * Nếu thiếu total_price thì tự tính: subtotal - discount.
     */
    public function getGrandTotalAttribute(): int
    {
        if (!is_null($this->total_price)) {
            return (int) $this->total_price;
        }
        return max(0, $this->subtotal_amount - $this->bill_discount_amount);
    }
}
