<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionCampaign extends Model
{
    use HasFactory;

    protected $table = 'promotion_campaigns';

    protected $fillable = [
        'name',
        'description',
        'priority',
        'start_at',
        'end_at',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'status'   => 'integer',
        'priority' => 'integer',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    /**
     * 1 Campaign có N Rule
     */
    public function rules()
    {
        return $this->hasMany(PromotionRule::class, 'campaign_id');
    }

    /**
     * Rule theo scope = product
     */
    public function productRules()
    {
        return $this->hasMany(PromotionRule::class, 'campaign_id')
            ->where('scope', 'product');
    }

    /**
     * Rule theo scope = order
     */
    public function orderRules()
    {
        return $this->hasMany(PromotionRule::class, 'campaign_id')
            ->where('scope', 'order');
    }

    /**
     * Campaign có nhiều redemption (log đã dùng)
     */
    public function redemptions()
    {
        return $this->hasMany(PromotionRedemption::class, 'campaign_id');
    }
}
