<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckoutController;

// ================= FRONTEND =================
  // Checkout

Route::get('/login-checkout', [CheckoutController::class, 'login_checkout'])->name('admin.logincheckout');

Route::get('/logout-checkout', [CheckoutController::class, 'logout_checkout'])->name('admin.logoutcheckout');

Route::post('/add-user', [CheckoutController::class, 'add_user'])->name('admin.adduser');

Route::post('/login-user', [CheckoutController::class, 'login_user'])->name('admin.loginuser');

Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('admin.checkout');

Route::post('/save-checkout-user', [CheckoutController::class, 'save-checkout-user'])->name('admin.savecheckoutuser');

Route::get('/profile', [UserController::class, 'profile'])->name('profile');

Route::post('/profile-update', [UserController::class, 'profileUpdate'])->name('profile.update');


//Trang chu
Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);
Route::post('/tim-kiem', [HomeController::class, 'search']);
// ================= BACKEND - AUTH ADMIN =================

// Form đăng nhập admin
Route::get('/admin', [AdminController::class, 'index'])->name('admin.login');

// Xử lý đăng nhập
Route::post('/admin-dashboard', [AdminController::class, 'dashboard'])->name('admin.doLogin');

// Dashboard sau khi đăng nhập
Route::get('/dashboard', [AdminController::class, 'show_dashboard'])->name('admin.dashboard');

// Đăng xuất
Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');

// ================= PRODUCT TYPE =================

Route::get('/add-product-type', [ProductTypeController::class, 'add_product_type'])->name('admin.addproducttype');

Route::get('/all-product-type', [ProductTypeController::class, 'all_product_type'])->name('admin.allproducttype');

Route::post('/save-product-type', [ProductTypeController::class, 'save_product_type'])->name('admin.saveproducttype');

Route::get('/edit-product-type/{id}', [ProductTypeController::class, 'edit_product_type'])->name('admin.editproducttype');

Route::get('/delete-product-type/{id}', [ProductTypeController::class, 'delete_product_type'])->name('destroy');

Route::post('/update-product-type/{id}', [ProductTypeController::class, 'update_product_type'])->name('admin.updateproducttype');

// ================= Brand Product =================
Route::get('/add-brand-product', [BrandProductController::class, 'add_brand_product'])->name('admin.addbrandproduct');

Route::get('/all-brand-product', [BrandProductController::class, 'all_brand_product'])->name('admin.allbrandproduct');

Route::post('/save-brand-product', [BrandProductController::class, 'save_brand_product'])->name('admin.savebrandproduct');

Route::get('/edit-brand-product/{id}', [BrandProductController::class, 'edit_brand_product'])->name('admin.editbrandproduct');

Route::get('/delete-brand-product/{id}', [BrandProductController::class, 'delete_brand_product'])->name('admin.deletebrandproduct');

Route::post('/update-brand-product/{id}', [BrandProductController::class, 'update_brand_product'])->name('admin.updatebrandproduct');


// ================= KHO NHẬP (TRONG PREFIX ADMIN) =================

Route::prefix('admin')->name('admin.')->group(function () {

    // KHO NHẬP
    Route::get('/storages', [StorageController::class, 'index'])->name('storages.index');

    Route::get('/storages/create', [StorageController::class, 'create'])->name('storages.create');

    Route::post('/storages', [StorageController::class, 'store'])->name('storages.store');

    Route::delete('/storages/{id}', [StorageController::class, 'destroy']);

    Route::get('/storages/{id}/edit', [StorageController::class, 'edit']);

    Route::put('/storages/{id}',      [StorageController::class, 'update']);

    
    // QUẢN LÝ SẢN PHẨM
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/products/{id}/edit', [ProductController::class, 'edit']);

    Route::post('/products/{id}/update', [ProductController::class, 'update']);


});



    

