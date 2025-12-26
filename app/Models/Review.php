<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'status'
    ];

    // Quan hệ tới người dùng
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    // Quan hệ tới sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function toggle_review_status($id)
{
    $review = Review::findOrFail($id);

    // Đảo trạng thái
    $review->status = $review->status == 1 ? 0 : 1;
    $review->save();

    return redirect()->back()->with('success', 'Cập nhật trạng thái đánh giá thành công');
}
}
