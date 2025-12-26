<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function show($id)
    {
        // Lấy sản phẩm
        $product = Product::with('productImage', 'category', 'brand')
                          ->findOrFail($id);

        // Lấy review + user
        $reviews = Review::with('user')
            ->where('product_id', $id)
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->get();

        // Tính điểm trung bình
        $averageRating = round(
            Review::where('product_id', $id)
                ->where('status', 1)
                ->avg('rating'),
            1
        );

        return view(
            'pages.product_detail',
            compact('product', 'reviews', 'averageRating')
        );
    }
}
