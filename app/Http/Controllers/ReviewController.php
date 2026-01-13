<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use App\Helpers\BadWordFilter;
use Carbon\Carbon;

class ReviewController extends Controller
{
    // ================================
    // LƯU ĐÁNH GIÁ
    // ================================
    public function store(Request $request, $product_id)
    {
        $userId = session('id');

        if (!$userId) {
            return back()->with('success', 'Bạn cần đăng nhập để đánh giá.');
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // ===== CHẶN ĐÁNH GIÁ TRÙNG =====
        $hasReviewed = Review::where('product_id', $product_id)
            ->where('user_id', $userId)
            ->exists();

        if ($hasReviewed) {
            return back()->with('success', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        // ===== KIỂM TRA ĐƠN HOÀN THÀNH =====
        $orderDetail = OrderDetail::where('product_id', $product_id)
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('status', 'success'); // chỉ đơn hoàn thành
            })
            ->orderByDesc('id')
            ->first();

        if (!$orderDetail) {
            return back()->with('success', 'Bạn chỉ có thể đánh giá khi đơn hàng đã hoàn thành.');
        }

        // ===== KIỂM TRA THỜI GIAN (2 PHÚT - KHÔNG SỐ THẬP PHÂN) =====
        $completedAt    = Carbon::parse($orderDetail->order->updated_at);
        $secondsPassed  = $completedAt->diffInSeconds(now());
        $waitSeconds    = 2 * 60; // 2 phút

        if ($secondsPassed < $waitSeconds) {
            $remainMinutes = ceil(($waitSeconds - $secondsPassed) / 60);

            return back()->with(
                'success',
                "Bạn cần đợi thêm {$remainMinutes} phút sau khi đơn hoàn thành để đánh giá."
            );
        }

        // ===== LỌC TỪ NGỮ TỤC TĨU =====
        $cleanComment = BadWordFilter::filter($request->comment);

        // ===== LƯU REVIEW =====
        DB::transaction(function () use ($product_id, $userId, $request, $cleanComment) {
            Review::create([
                'product_id' => $product_id,
                'user_id'    => $userId,
                'rating'     => (int) $request->rating,
                'comment'    => $cleanComment,
                'status'     => 1, // hiển thị
            ]);
        });

        return back()->with('success', 'Đánh giá của bạn đã được gửi.');
    }

    // ================================
    // LẤY REVIEW HIỂN THỊ
    // ================================
    public function getReviews($product_id)
    {
        $reviews = Review::with('user')
            ->where('product_id', $product_id)
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->paginate(5);

        $averageRating = round(
            Review::where('product_id', $product_id)
                ->where('status', 1)
                ->avg('rating'),
            1
        );

        return view('product.reviews', compact('reviews', 'averageRating'));
    }

    // ================================
    // CHECK QUYỀN ĐÁNH GIÁ (DÙNG CHO BLADE)
    // ================================
    public function canUserReview(int $userId, int $productId): array
    {
        $orderDetail = OrderDetail::where('product_id', $productId)
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('status', 'success');
            })
            ->orderByDesc('id')
            ->first();

        if (!$orderDetail) {
            return [
                'can'    => false,
                'reason' => 'not_completed'
            ];
        }

        $completedAt   = Carbon::parse($orderDetail->order->updated_at);
        $secondsPassed = $completedAt->diffInSeconds(now());

        if ($secondsPassed < 120) {
            return [
                'can'             => false,
                'reason'          => 'wait_2_minutes',
                'remain_minutes'  => ceil((120 - $secondsPassed) / 60),
            ];
        }

        return ['can' => true];
    }
}
