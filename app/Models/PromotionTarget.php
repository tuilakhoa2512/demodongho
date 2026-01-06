<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionTarget extends Model
{
    use HasFactory;

    protected $table = 'promotion_targets';

    protected $fillable = [
        'rule_id',
        'target_type',
        'target_id',
        'status',
    ];

    protected $casts = [
        'target_id' => 'integer',
        'status'    => 'integer',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    /**
     * Target thuộc về 1 Rule
     */
    public function rule()
    {
        return $this->belongsTo(PromotionRule::class, 'rule_id');
    }

    /* ===================== HELPER ===================== */

    public function isAll(): bool
    {
        return $this->target_type === 'all';
    }

    public function isProduct(): bool
    {
        return $this->target_type === 'product';
    }

    public function isCategory(): bool
    {
        return $this->target_type === 'category';
    }

    public function isBrand(): bool
    {
        return $this->target_type === 'brand';
    }
}
