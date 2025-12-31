<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class HomeController extends Controller
{
    private function discountEagerLoadClosure()
    {
        $today = now()->toDateString();

        return function ($q) use ($today) {
            $q->where('discount_products.status', 1)
              ->wherePivot('status', 1)
              ->where(function ($qq) use ($today) {
                  $qq->whereNull('discount_product_details.expiration_date')
                     ->orWhere('discount_product_details.expiration_date', '>=', $today);
              })
              ->orderByDesc('discount_products.rate');
        };
    }

    public function index()
    {
        $cate_pro  = DB::table('categories')->where('status', 1)->orderBy('id', 'asc')->get();
        $brand_pro = DB::table('brands')->where('status', 1)->orderBy('id', 'asc')->get();

        $all_product = Product::with([
                'productImage',
                'category',
                'brand',
                'discountProducts' => $this->discountEagerLoadClosure(),
            ])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->whereHas('brand', function ($q) {
                $q->where('status', 1);
            })
            ->inRandomOrder()
            ->paginate(6);

            
        $recommended_products = Product::with([
                'productImage',
                'discountProducts' => $this->discountEagerLoadClosure(),
            ])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('pages.home')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('all_product', $all_product)
            ->with('recommended_products', $recommended_products);
    }

    public function search(Request $request)
{
    $keywords = trim($request->keywords);

    $cate_pro  = DB::table('categories')->where('status', 1)->orderBy('id')->get();
    $brand_pro = DB::table('brands')->where('status', 1)->orderBy('id')->get();

    // ===== FAVORITE =====
    $favorite_ids = [];
    if (Session::has('id')) {
        $favorite_ids = DB::table('favorites')
            ->where('user_id', Session::get('id'))
            ->pluck('product_id')
            ->toArray();
    }

    $search_product = Product::with([
            'productImage',
            'category',
            'brand',
            'discountProducts' => $this->discountEagerLoadClosure(),
        ])
        ->where('name', 'LIKE', "%{$keywords}%")
        ->where('quantity', '>', 0)
        ->whereHas('category')
        ->whereHas('brand')
        ->orderBy('id', 'desc')
        ->get();

    return view('admin.products.search', compact(
        'cate_pro',
        'brand_pro',
        'search_product',
        'keywords',
        'favorite_ids'
    ));
}


    // Hàm lọc giá
    public function filterPrice(Request $request)
    {
        $min = (float) $request->get('min_price', 0);
        $max = (float) $request->get('max_price', 100000000);

        $max = min($max, 100000000);

        $cate_pro = DB::table('categories')->where('status', 1)->get();
        $brand_pro = DB::table('brands')->where('status', 1)->get();

        // Filter: eager-load discount để trang home hiển thị đúng giá sau ưu đãi
        $all_product = Product::with([
                'productImage',
                'category',
                'brand',
                'discountProducts' => $this->discountEagerLoadClosure(),
            ])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->whereBetween('price', [$min, $max])
            ->paginate(6)
            ->appends([
                'min_price' => $min,
                'max_price' => $max,
            ]);

        $recommended_products = Product::with([
                'productImage',
                'discountProducts' => $this->discountEagerLoadClosure(),
            ])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->latest()
            ->take(6)
            ->get();

        return view('pages.home', [
            'all_product'          => $all_product,
            'category'             => $cate_pro,
            'brand'                => $brand_pro,
            'recommended_products' => $recommended_products,
        ]);
    }
}
