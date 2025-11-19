<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function getImagesAttribute()
    {
        return collect([
            $this->image_1,
            $this->image_2,
            $this->image_3,
            $this->image_4,
        ])->filter()->values()->all();
    }

    // ưu tiên ảnh 1 làm avt
    public function getMainImageAttribute()
    {
        return $this->image_1 ?? $this->images[0] ?? null;
    }

  
}
