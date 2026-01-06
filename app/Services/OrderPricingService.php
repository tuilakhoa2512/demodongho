<?php

namespace App\Services;

use App\Models\Product;

class OrderPricingService
{
    public function __construct(
        protected PromotionService $promotionService
    ) {}

    /**
     * Quote tiền đơn hàng (KHÔNG ghi DB)
     *
     * @param array $cartItems
     *  [
     *    [
     *      'product_id' => 1,
     *      'qty' => 2,
     *      'unit_price_final' => 5000000,
     *    ]
     *  ]
     */
    public function quote(array $cartItems, ?int $userId, ?string $code = null): array
    {
        // 1) Subtotal
        $subtotal = 0;
        $productIds = [];

        foreach ($cartItems as $item) {
            $pid  = (int)($item['product_id'] ?? 0);
            $unit = (float)($item['unit_price_final'] ?? 0);
            $qty  = max(1, (int)($item['qty'] ?? 1));

            if ($pid > 0) $productIds[] = $pid;
            $subtotal += $unit * $qty;
        }

        $subtotal = (int) round($subtotal);
        $productIds = array_values(array_unique($productIds));

        // 2) Lấy product để match targets (category/brand/product/all)
        $productsMap = collect();
        if (!empty($productIds)) {
            $productsMap = Product::query()
                ->whereIn('id', $productIds)
                ->get(['id', 'category_id', 'brand_id'])
                ->keyBy('id');
        }

        $cartProducts = [];
        foreach ($cartItems as $item) {
            $pid = (int)($item['product_id'] ?? 0);
            $p = $productsMap->get($pid);
            if ($p) $cartProducts[] = $p;
        }

        // 3) ✅ Dùng API public của PromotionService (không gọi protected)
        // PromotionService::calcOrderDiscount sẽ tự:
        // - validate code (nếu có)
        // - auto apply rule order (nếu không có code)
        // - tính discount_amount + final_total
        $res = $this->promotionService->calcOrderDiscount(
            $subtotal,
            $cartProducts,
            ($code !== null && trim($code) !== '') ? trim($code) : null,
            $userId
        );

        return [
            'subtotal'        => $subtotal,
            'order_promotion' => $res['rule'] ?? null,       // PromotionRule|null
            'promotion_code'  => $res['code'] ?? null,       // PromotionCode|null
            'discount_amount' => (int)($res['discount_amount'] ?? 0),
            'total'           => (int)($res['final_total'] ?? $subtotal),
            'ok'              => (bool)($res['ok'] ?? true),
            'message'         => (string)($res['message'] ?? ''),
        ];
    }
}
