<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Services\PromotionService;
use App\Services\ProductPromotionApplier;

class HomeController extends Controller
{
    /**
     * Gắn runtime promo fields để view dùng:
     * - final_price
     * - promo_has_sale
     * - promo_name
     * - promo_label
     */
    private function attachProductPromos($products, PromotionService $promoService)
    {
        foreach ($products as $p) {
            $pack = $promoService->calcProductFinalPrice($p);

            // dùng float để không mất phần thập phân
            $p->final_price    = (float) ($pack['final_price'] ?? $p->price);
            $p->promo_has_sale = !empty($pack['promotion']) ? 1 : 0;

            $promo = $pack['promotion'] ?? null;
            $p->promo_name  = $promo?->name;
            $p->promo_label = $pack['promo_label'] ?? null;
        }

        return $products;
    }

    public function index(Request $request, PromotionService $promoService)
    {
        $cate_pro  = DB::table('categories')->where('status', 1)->orderBy('id', 'asc')->get();
        $brand_pro = DB::table('brands')->where('status', 1)->orderBy('id', 'asc')->get();
    
        // ===== GIỮ NGUYÊN QUERY GỐC, CHỈ ĐỔI TÊN BIẾN =====
        $query = Product::with(['productImage','category','brand'])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->whereHas('category', fn($q) => $q->where('status', 1))
            ->whereHas('brand', fn($q) => $q->where('status', 1));
    
        // ===== CHỈ THÊM LỌC TẦM GIÁ (NẾU CÓ) =====
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
    
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }
    
        // ===== PAGINATE GIỮ QUERY STRING =====
        $all_product = $query
            ->inRandomOrder()
            ->paginate(6)
            ->appends($request->query());
    
        // ===== GIỮ NGUYÊN RECOMMENDED =====
        $recommended_products = Product::with(['productImage','category','brand'])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();
    
        // ===== GIỮ NGUYÊN PROMOTION =====
        $all_product = $this->attachProductPromos($all_product, $promoService);
        $recommended_products = $this->attachProductPromos($recommended_products, $promoService);
    
        return view('pages.home')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('all_product', $all_product)
            ->with('recommended_products', $recommended_products);
    }
    

    public function search(Request $request, PromotionService $promoService)
    {
        $keywords = trim($request->keywords);

        $cate_pro  = DB::table('categories')->where('status', 1)->orderBy('id')->get();
        $brand_pro = DB::table('brands')->where('status', 1)->orderBy('id')->get();

        // FAVORITE
        $favorite_ids = [];
        if (Session::has('id')) {
            $favorite_ids = DB::table('favorites')
                ->where('user_id', Session::get('id'))
                ->pluck('product_id')
                ->toArray();
        } else {
            $favorite_ids = Session::get('favorite_guest', []);
        }

        $search_product = Product::with(['productImage','category','brand'])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->where('name', 'LIKE', "%{$keywords}%")
            ->whereHas('category', fn($q) => $q->where('status', 1))
            ->whereHas('brand', fn($q) => $q->where('status', 1))
            ->orderByDesc('id')
            ->paginate(6)
            ->appends(['keywords' => $keywords]);

        $search_product = $this->attachProductPromos($search_product, $promoService);

        // FE search: trả về pages.home (tạm dùng chung layout)
        return view('pages.home', [
            'category'             => $cate_pro,
            'brand'                => $brand_pro,
            'all_product'          => $search_product,       // dùng chung vòng lặp
            'recommended_products' => collect(),             // hoặc bạn query lại nếu muốn
            'keywords'             => $keywords,
            'favorite_ids'         => $favorite_ids,
        ]);
    }

    public function filterPrice(Request $request, PromotionService $promoService)
    {
        $min = (float) $request->get('min_price', 0);
        $max = (float) $request->get('max_price', 100000000);
        $max = min($max, 100000000);

        $cate_pro  = DB::table('categories')->where('status', 1)->get();
        $brand_pro = DB::table('brands')->where('status', 1)->get();

        $applier = app(ProductPromotionApplier::class);
        // NOTE: đang lọc theo price gốc (nhanh). Nếu muốn lọc theo final_price mình sẽ nâng cấp sau.
        $all_product = Product::with(['productImage','category','brand'])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->whereBetween('price', [$min, $max])
            ->paginate(6)
            ->appends(['min_price' => $min, 'max_price' => $max]);

        $recommended_products = Product::with(['productImage','category','brand'])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->latest()
            ->take(6)
            ->get();

        $all_product = $this->attachProductPromos($all_product, $promoService);
        $recommended_products = $this->attachProductPromos($recommended_products, $promoService);

        return view('pages.home', [
            'all_product'          => $all_product,
            'category'             => $cate_pro,
            'brand'                => $brand_pro,
            'recommended_products' => $recommended_products,
        ]);
    }
}
