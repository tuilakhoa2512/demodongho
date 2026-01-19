<?php

namespace App\Http\Controllers;

use App\Services\SaleService;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductPromotionApplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SaleController extends Controller
{
    public function index(PromotionService $promoService, Request $request)
    {
        //  Lấy toàn bộ SP đang bán
        $products = Product::with(['productImage', 'brand', 'category'])
            ->where('status', 1)
            ->where('stock_status', 'selling')
            ->orderByDesc('id')
            ->get();

        //  Lọc sản phẩm đang giảm giá 
        $saleItems = [];

        foreach ($products as $p) {

            $pack = $promoService->calcProductFinalPrice($p);

            $final = (float)($pack['final_price'] ?? $p->price);
            $base  = (float)($p->price ?? 0);

            if ($final > 0 && $final < $base) {

                // gán runtime fields cho view
                $p->final_price = (int)$final;
                $p->promo_has_sale = 1;

                $campaign = $pack['campaign'] ?? null;
                $rule     = $pack['rule'] ?? null;

                $p->promo_name            = $campaign?->name;
                $p->promo_discount_type   = $rule?->discount_type;
                $p->promo_discount_value  = $rule ? (int)$rule->discount_value : null;
                $p->promo_label           = $pack['promo_label'] ?? null;
                $p->promo_discount_amount = (int)($pack['discount_amount'] ?? max(0, $base - $final));

                $saleItems[] = $p;
            }
        }

        // Paginate thủ công
        $perPage = 6;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $collection = new Collection($saleItems);

        $currentPageItems = $collection
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        $saleProducts = new LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.sales', compact('saleProducts'));
    }
    
}
