<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductTypeController;
use Illuminate\Support\Facades\Route;

//Frontend
Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);


//Backend
// Route::get('/admin', [AdminController::class, 'index']);
// Route::get('/dashboard', [AdminController::class, 'show_dashboard']);
// Route::get('/logout', [AdminController::class, 'logout']);
// Route::post('/admin-dashboard', [AdminController::class, 'dashboard']);


// Form đăng nhập admin
Route::get('/admin', [AdminController::class, 'index'])->name('admin.login');

// Xử lý đăng nhập
Route::post('/admin-dashboard', [AdminController::class, 'dashboard'])->name('admin.doLogin');

// Dashboard sau khi đăng nhập
Route::get('/dashboard', [AdminController::class, 'show_dashboard'])->name('admin.dashboard');

// Đăng xuất
Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');

//Product Type
Route::get('/add-product-type', [\App\Http\Controllers\Admin\ProductTypeController::class, 'add_product_type'])->name('admin.addproducttype');
Route::get('/all-product-type', [\App\Http\Controllers\Admin\ProductTypeController::class, 'all_product_type'])->name('admin.allproducttype');
Route::post('/save-product-type', [\App\Http\Controllers\Admin\ProductTypeController::class, 'save_product_type'])->name('admin.saveproducttype');