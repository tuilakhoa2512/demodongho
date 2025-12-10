<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountBill extends Model
{
    protected $table = 'discount_bills';

    protected $fillable = [
        'name',
        'min_subtotal',
        'rate',
        'status',
    ];

    /**
     * Scope: chỉ lấy các ưu đãi bill đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Tìm mã ưu đãi bill phù hợp nhất cho một subtotal
     *  - min_subtotal <= subtotal
     *  - status = 1
     *  - chọn ưu đãi có min_subtotal lớn nhất / hoặc rate cao nhất
     *
     * Ở đây mình chọn cách:
     *  - ưu tiên min_subtotal lớn nhất
     *  - nếu trùng thì rate lớn hơn sẽ tốt hơn
     */
    public static function bestForSubtotal(int $subtotal): ?self
    {
        return self::active()
            ->where('min_subtotal', '<=', $subtotal)
            ->orderByDesc('min_subtotal')
            ->orderByDesc('rate')
            ->first();
    }
}
