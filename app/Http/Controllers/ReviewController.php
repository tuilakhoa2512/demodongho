<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
class ReviewController extends Controller
{
    // Lưu review mới (guest)
    public function store(Request $request, $product_id)
{
    $userId = session('id'); // ✅ ĐÚNG KEY

    if (!$userId) {
        return back()->with('error', 'Bạn cần đăng nhập để đánh giá');
    }

    $request->validate([
        'rating'  => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:1000',
    ]);

    DB::table('reviews')->insert([
        'product_id' => $product_id,
        'user_id'    => $userId, // ✅ sẽ là 2
        'rating'     => $request->rating,
        'comment'    => $request->comment,
        'status'     => 1,
        'created_at'=> now(),
        'updated_at'=> now(),
    ]);

    return back()->with('success', 'Đánh giá đã được gửi');
}


    // Lấy danh sách review cho sản phẩm
    public function getReviews($product_id)
    {
        $reviews = Review::where('product_id', $product_id)
                         ->where('status', 1)
                         ->orderBy('created_at', 'desc')
                         ->get();

        $averageRating = round(Review::where('product_id', $product_id)->where('status', 1)->avg('rating'), 1);

        return view('product.reviews', compact('reviews', 'averageRating'));
    }
}
