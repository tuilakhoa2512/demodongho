<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionRule extends Model
{
    use HasFactory;

    protected $table = 'promotion_rules';

    protected $fillable = [
        'campaign_id',
        'scope',
        'discount_type',
        'discount_value',
        'min_order_subtotal',
        'max_discount_amount',
        'max_uses',
        'max_uses_per_user',
        'start_at',
        'end_at',
        'status',
        'priority',
    ];

    protected $casts = [
        'discount_value'       => 'integer',
        'min_order_subtotal'   => 'integer',
        'max_discount_amount'  => 'integer',
        'max_uses'             => 'integer',
        'max_uses_per_user'    => 'integer',
        'priority'             => 'integer',
        'status'               => 'integer',
        'start_at'             => 'datetime',
        'end_at'               => 'datetime',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    /**
     * Rule thuộc về 1 Campaign
     */
    public function campaign()
    {
        return $this->belongsTo(PromotionCampaign::class, 'campaign_id');
    }

    /**
     * Rule có nhiều Target
     */
    public function targets()
    {
        return $this->hasMany(PromotionTarget::class, 'rule_id');
    }

    /**
     * Rule có nhiều Code
     */
    public function codes()
    {
        return $this->hasMany(PromotionCode::class, 'rule_id');
    }

    /**
     * Log đã dùng rule
     */
    public function redemptions()
    {
        return $this->hasMany(PromotionRedemption::class, 'rule_id');
    }

    /* ===================== HELPER ===================== */

    public function isProductScope(): bool
    {
        return $this->scope === 'product';
    }

    public function isOrderScope(): bool
    {
        return $this->scope === 'order';
    }
}
