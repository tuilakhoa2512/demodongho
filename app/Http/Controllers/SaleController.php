<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $saleProducts = Product::where('status', 1)
            ->whereHas('discounts', function ($q) use ($today) {
                $q->where('discount_products.status', 1)
                  ->where('discount_product_details.status', 1)
                  ->where(function ($q2) use ($today) {
                      $q2->whereNull('discount_product_details.expiration_date')
                         ->orWhere('discount_product_details.expiration_date', '>=', $today);
                  });
            })
            ->with(['productImage', 'discounts'])
            ->paginate(9);

        return view('pages.sales', compact('saleProducts'));
    }
}
