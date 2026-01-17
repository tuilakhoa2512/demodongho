<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use App\Services\ProductPromotionApplier;
use Illuminate\Pagination\Paginator;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    /* Register any application services.*/
    public function register(): void
    {
        //
    }

    /* Bootstrap any application services.*/
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.number-only');
        /*  SHARE CATEGORY & BRAND (CŨ)*/

        // Danh mục
        $categories = DB::table('categories')
            ->where('status', 1)
            ->get();
        View::share('category', $categories);

        // Thương hiệu
        $brands = DB::table('brands')
            ->where('status', 1)
            ->get();
        View::share('brand', $brands);

        /* GLOBAL APPLY PROMOTION (MỚI)*/

        View::composer('*', function ($view) {

            $data = $view->getData();

            // Không có product / products thì bỏ qua
            if (!isset($data['product']) && !isset($data['products'])) {
                return;
            }

            $applier = app(ProductPromotionApplier::class);

            //  Áp promotion cho 1 product
            if (isset($data['product']) && $data['product']) {
                $view->with(
                    'product',
                    $applier->apply(collect([$data['product']]))->first()
                );
            }

            //  Áp promotion cho danh sách products
            if (isset($data['products']) && $data['products']) {
                $view->with(
                    'products',
                    $applier->apply($data['products'])
                );
            }
        });

         /* CART COUNT */
        View::composer('*', function ($view) {

            $count = 0;

            if (Session::get('id')) {
                // User đã login → lấy từ DB
                $count = Cart::where('user_id', Session::get('id'))
                            ->sum('quantity');
            } else {
                // Guest → lấy từ session
                $cart = Session::get('cart', []);
                foreach ($cart as $item) {
                    $count += (int) ($item['quantity'] ?? 0);
                }
            }

            $view->with('cartCount', $count);
        });
    }
}
