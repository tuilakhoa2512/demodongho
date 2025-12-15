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

    protected $casts = [
        'min_subtotal' => 'integer',
        'rate'         => 'integer',
        'status'       => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('status', 1);
    }
}
