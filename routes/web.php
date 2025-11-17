<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\StorageController;

// ================= FRONTEND =================

Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);

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

Route::get('/delete-product-type/{id}', [ProductTypeController::class, 'delete_product_type'])->name('admin.deleteproducttype');

Route::post('/update-product-type/{id}', [ProductTypeController::class, 'update_product_type'])->name('admin.updateproducttype');


// ================= KHO NHẬP (TRONG PREFIX ADMIN) =================

Route::prefix('admin')->name('admin.')->group(function () {

    // KHO NHẬP
Route::get('/storages', [StorageController::class, 'index'])->name('storages.index');

Route::get('/storages/create', [StorageController::class, 'create'])->name('storages.create');

Route::post('/storages', [StorageController::class, 'store'])->name('storages.store');
});
