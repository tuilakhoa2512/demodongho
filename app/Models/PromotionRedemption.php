<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionRedemption extends Model
{
    use HasFactory;

    protected $table = 'promotion_redemptions';

    protected $fillable = [
        'campaign_id',
        'rule_id',
        'code_id',
        'user_id',
        'order_id',
        'code',
        'subtotal',
        'discount_amount',
        'final_total',
        'status',
    ];

    protected $casts = [
        'campaign_id'     => 'integer',
        'rule_id'         => 'integer',
        'code_id'         => 'integer',
        'user_id'         => 'integer',
        'order_id'        => 'integer',
        'subtotal'        => 'integer',
        'discount_amount' => 'integer',
        'final_total'     => 'integer',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function campaign()
    {
        return $this->belongsTo(PromotionCampaign::class, 'campaign_id');
    }

    public function rule()
    {
        return $this->belongsTo(PromotionRule::class, 'rule_id');
    }

    public function code()
    {
        return $this->belongsTo(PromotionCode::class, 'code_id');
    }

    // nếu bạn muốn
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
