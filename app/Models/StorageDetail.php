<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageDetail extends Model
{
    protected $table = 'storage_details';

    protected $fillable = [
        'storage_id',
        'product_name',
        'import_quantity',
        'stock_status',
        'note',
        'status',
    ];

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(
            \App\Models\Product::class,
            'storage_detail_id', 
            'id'
        );
    }

    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'pending'  => 'Chưa Bán',
            'selling'  => 'Đang Bán',
            'sold_out' => 'Bán Hết',
            'stopped'  => 'Ngừng Bán',
            default    => 'Không xác định',
        };
    }
}
