<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //

    public function discountBill()
{
    return $this->belongsTo(\App\Models\DiscountBill::class, 'discount_bill_id');
}

}
