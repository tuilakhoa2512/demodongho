<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PromotionRule;
use Carbon\Carbon;

class SaleService
{
    /**
     * Trả về Query Builder các sản phẩm đang được áp dụng promotion product
     */
    public function saleProductQuery()
    {
        $now = Carbon::now();

        // Lấy rule product đang active
        $rules = PromotionRule::where('scope', 'product')
            ->where('status', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                  ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', $now);
            })
            ->with(['targets' => function ($q) {
                $q->where('status', 1);
            }])
            ->orderByDesc('priority')
            ->get();

        // Không có rule → không có sale
        if ($rules->isEmpty()) {
            return Product::whereRaw('1 = 0');
        }

        $productIds = [];

        foreach ($rules as $rule) {
            foreach ($rule->targets as $target) {

                switch ($target->target_type) {

                    case 'all':
                        return Product::where('status', 1)
                            ->with('productImage');

                    case 'product':
                        $productIds[] = $target->target_id;
                        break;

                    case 'category':
                        Product::where('category_id', $target->target_id)
                            ->pluck('id')
                            ->each(fn ($id) => $productIds[] = $id);
                        break;

                    case 'brand':
                        Product::where('brand_id', $target->target_id)
                            ->pluck('id')
                            ->each(fn ($id) => $productIds[] = $id);
                        break;
                }
            }
        }

        $productIds = array_unique($productIds);

        if (empty($productIds)) {
            return Product::whereRaw('1 = 0');
        }

        return Product::whereIn('id', $productIds)
            ->where('status', 1)
            ->with('productImage');
    }
}
