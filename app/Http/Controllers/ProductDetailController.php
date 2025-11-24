<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function show($id)
    {
        $product = Product::with('productImage', 'category', 'brand')->findOrFail($id);

        return view('pages.product_detail', compact('product'));
    }
}
