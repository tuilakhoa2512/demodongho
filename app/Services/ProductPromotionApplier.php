<?php

namespace App\Services;

use App\Services\PromotionService;
use Illuminate\Support\Collection;

class ProductPromotionApplier
{
    public function apply($products): mixed
    {
        $promoService = app(PromotionService::class);

        foreach ($products as $p) {
            $pack = $promoService->calcProductFinalPrice($p);

            $p->final_price = (float) ($pack['final_price'] ?? $p->price);
            $p->promo_has_sale = !empty($pack['has_discount']) ? 1 : 0;

            $p->promo_name  = $pack['promo_name'] ?? null;
            $p->promo_label = $pack['promo_label'] ?? null;
        }

        return $products;
    }
}
