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
    // public function boot(): void
    // {
    //     $categories = DB::table('categories')->get();
    //     View::share('category', $categories);

    //     $brands = DB::table('brands')->get();
    //     View::share('brand', $brands);
        
    // }
}
