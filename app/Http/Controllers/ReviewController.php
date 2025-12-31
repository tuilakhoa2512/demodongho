<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    // Lưu review (user dùng session, KHÔNG dùng auth)
    public function store(Request $request, $product_id)
    {
        $userId = session('id'); // user id lưu trong session

        if (!$userId) {
            return back()->with('error', 'Bạn cần đăng nhập để đánh giá');
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // ================== CHẶN ĐÁNH GIÁ TRÙNG ==================
        $hasReviewed = Review::where('product_id', $product_id)
            ->where('user_id', $userId)
            ->exists();

        if ($hasReviewed) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi, không thể đánh giá thêm.');
        }
        // =========================================================

        DB::transaction(function () use ($request, $product_id, $userId) {
            Review::create([
                'product_id' => $product_id,
                'user_id'    => $userId,
                'rating'     => (int) $request->rating,
                'comment'    => $request->comment,
                'status'     => 1,
            ]);
        });

        return back()->with('success', 'Đánh giá đã được gửi');
    }

    // Lấy danh sách review cho sản phẩm
    public function getReviews($product_id)
    {
        $reviews = Review::with('user')
            ->where('product_id', $product_id)
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->get();

        $averageRating = round(
            Review::where('product_id', $product_id)
                ->where('status', 1)
                ->avg('rating'),
            1
        );

        return view('product.reviews', compact('reviews', 'averageRating'));
    }
}
