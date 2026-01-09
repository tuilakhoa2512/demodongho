<?php

namespace App\Http\Controllers;

use App\Services\SaleService;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductPromotionApplier;

class SaleController extends Controller
{
    public function index(
        SaleService $saleService,
        ProductPromotionApplier $applier
    ) {
        // LẤY ĐÚNG SẢN PHẨM ĐANG CÓ PROMOTION
        $query = $saleService->saleProductQuery()
            ->where('stock_status', 'selling');
    
        $saleProducts = $query->paginate(6);
    
        // ÁP DỤNG PROMOTION
        $saleProducts->setCollection(
            $applier->apply($saleProducts->getCollection())
        );
    
        return view('pages.sales', compact('saleProducts'));
    }
    
}
