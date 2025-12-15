<?php

namespace App\Providers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    // Danh mục — nếu bạn có cột status thì cũng nên thêm where
    $categories = DB::table('categories')
                     ->where('status', 1)  // nếu có cột này
                    ->get();
    View::share('category', $categories);

    // Thương hiệu — CHỈ LẤY BRAND HIỆN
    $brands = DB::table('brands')
                ->where('status', 1)
                ->get();
    View::share('brand', $brands);
}

}
