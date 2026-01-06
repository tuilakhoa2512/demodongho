<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionCode extends Model
{
    use HasFactory;

    protected $table = 'promotion_codes';

    protected $fillable = [
        'rule_id',
        'code',
        'min_subtotal',
        'max_discount',
        'max_uses',
        'max_uses_per_user',
        'start_at',
        'end_at',
        'status',
    ];

    protected $casts = [
        'min_subtotal'        => 'integer',
        'max_discount'        => 'integer',
        'max_uses'            => 'integer',
        'max_uses_per_user'   => 'integer',
        'status'              => 'integer',
        'start_at'            => 'datetime',
        'end_at'              => 'datetime',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    /**
     * Code thuộc về 1 Rule
     */
    public function rule()
    {
        return $this->belongsTo(PromotionRule::class, 'rule_id');
    }

    /**
     * Log đã dùng code
     */
    public function redemptions()
    {
        return $this->hasMany(PromotionRedemption::class, 'code_id');
    }
}
