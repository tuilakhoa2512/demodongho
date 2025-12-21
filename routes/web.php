<?php

use App\Http\Controllers\CompareController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProvinceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\StorageDetailController;
use App\Http\Controllers\Admin\DiscountProductController;
use App\Http\Controllers\Admin\DiscountProductDetailController;
use App\Http\Controllers\Admin\DiscountBillController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\DB;



use App\Models\Category;

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

Route::get('/register', [CheckoutController::class, 'register'])->name('user.register');

Route::get('/payment', [CheckoutController::class, 'payment'])->name('user.payment'); 

//Login user google
Route::get('/login-user-google', [AdminController::class, 'login_user_google']);
Route::get('/user/google/callback', [AdminController::class, 'callback_user_google']);

//Trang chu
Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);
Route::post('/tim-kiem', [HomeController::class, 'search']);
Route::get('/product/{id}', [App\Http\Controllers\ProductDetailController::class, 'show']);


/// Tỉnh - Huyện - Xã
Route::prefix('location')->group(function () {

  // API load huyện theo tỉnh
  Route::get('/districts/{province_id}', [UserController::class, 'getDistricts']);

  // API load xã theo huyện
  Route::get('/wards/{district_id}', [UserController::class, 'getWards']);

  // Nếu bạn vẫn muốn giữ API theo code
  Route::get('/provinces', [LocationController::class, 'provinces'])->name('location.provinces');
  Route::get('/provinces/{code}/districts', [LocationController::class, 'districts'])->name('location.districts');
  Route::get('/districts/{code}/wards', [LocationController::class, 'wards'])->name('location.wards');
});


// Danh mục sản phẩm trang chủ
// Route::get('/danh-muc-san-pham/{id}', [ProductsaveTypeController::class, 'show_category_home']);
// Route::get('/thuong-hieu-san-pham/{id}', [BrandProductController::class, 'show_brand_home']);
Route::get('/danh-muc-san-pham/{category_slug}', [ProductTypeController::class, 'show_category_home'])->name('category.show');
Route::get('/thuong-hieu-san-pham/{brand_slug}', [BrandProductController::class, 'show_brand_home'])->name('brand.show');
Route::get('/danh-muc/{slug}', [ProductTypeController::class, 'showCategory'])->name('category.show');


// Giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');           
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');    
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update'); 
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Order
Route::get('/payment', [OrderController::class, 'showPaymentForm'])->name('payment.show');
Route::post('/payment', [OrderController::class, 'placeOrder'])->name('payment.place');


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

Route::get('/unactive-product-type/{id}', [ProductTypeController::class, 'unactive_product_type'])->name('admin.unactivecate');

Route::get('/active-product-type/{id}', [ProductTypeController::class, 'active_product_type'])->name('admin.activecate');

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

Route::get('/unactive-brand-product/{id}', [BrandProductController::class, 'unactive_brand_product'])->name('admin.unactivebrand');

Route::get('/active-brand-product/{id}', [BrandProductController::class, 'active_brand_product'])->name('admin.activebrand');

// ================= YÊU THÍCH ================= //
  // Thêm yêu thích
Route::get('/yeu-thich/add/{id}', [FavoriteController::class, 'addFavorite'])
->name('favorite.add');

// Danh sách yêu thích
Route::get('/yeu-thich', [FavoriteController::class, 'index'])
->name('favorite.index');

// Xoá yêu thích
Route::get('/yeu-thich-xoa/{id}', [FavoriteController::class, 'removeFavorite'])
->name('favorite.remove');

Route::get('/yeu-thich/toggle/{id}', [FavoriteController::class, 'toggle'])
    ->name('favorite.toggle');

// ================= So sánh =================
    // Trang so sánh
// Hiển thị trang so sánh 2 sản phẩm
Route::get('/so-sanh', [CompareController::class, 'view'])->name('compare.view');
Route::get('/contact-us', function () {
  return view('pages.contact');
})->name('contact.us');
Route::post('/contact-send', [ContactController::class, 'send'])
    ->name('contact.send');

// Thêm sản phẩm vào so sánh
Route::get('/so-sanh/add/{id}', [CompareController::class, 'add'])->name('compare.add');

// Xoá sản phẩm trong slot sp1 hoặc sp2
Route::get('/so-sanh/remove/{slot}', [CompareController::class, 'remove'])->name('compare.remove');
Route::get('/so-sanh/chon/{slot}', [CompareController::class, 'select'])->name('compare.select');
Route::get('/so-sanh/xoa-tat-ca', [CompareController::class, 'clear'])->name('compare.clear');
Route::get('/filter-price', [HomeController::class, 'filterPrice']);


//Quản lý user khách hàng


  Route::get('/all-admin-user', [AdminUserController::class, 'all_admin_user'])->name('admin.users.index');
  Route::get('/add-admin-user', [AdminUserController::class, 'add_admin_user'])->name('admin.users.create');
  Route::post('/store-admin-user', [AdminUserController::class, 'store_admin_user'])->name('admin.users.store');
  Route::get('/unactive-admin-user/{id}', [AdminUserController::class, 'unactive_admin_user'])->name('admin.users.unactive');
  Route::get('/active-admin-user/{id}', [AdminUserController::class, 'active_admin_user'])->name('admin.users.active');

// ================= LÔ HÀNG + CHI TIẾT KHO (TRONG PREFIX ADMIN) =================


Route::prefix('admin')->name('admin.')->group(function () {

    // ======== KHO NHẬP (Lô Hàng) ========
    Route::get('/storages', [StorageController::class, 'index'])->name('storages.index');

    Route::get('/storages/create', [StorageController::class, 'create'])->name('storages.create');

    Route::post('/storages', [StorageController::class, 'store'])->name('storages.store');

    Route::get('/storages/{id}/edit', [StorageController::class, 'edit'])->name('storages.edit');

    Route::put('/storages/{id}', [StorageController::class, 'update'])->name('storages.update');

      // status ẩn/hiện
    Route::patch('/storages/{id}/toggle-status', [StorageController::class, 'toggleStatus'])->name('storages.toggle-status');

    // ======== QUẢN LÝ KHO HÀNG (STORAGE DETAILS) ========

      //xem toàn bộ sản phẩm của tất cả lô
    Route::get('/storage-details', [StorageDetailController::class, 'index'])->name('storage-details.index');

      // Danh sách kho theo 1 lô cụ thể
      // GET /admin/storages/{storageId}/details
    Route::get('/storages/{storageId}/details', [StorageDetailController::class, 'indexByStorage'])->name('storage-details.by-storage');

    Route::get('/storages/{storageId}/details/create', [StorageDetailController::class, 'create'])->name('storage-details.create');

    Route::post('/storages/{storageId}/details', [StorageDetailController::class, 'store'])->name('storage-details.store');

    Route::get('/storage-details/{id}/edit', [StorageDetailController::class, 'edit'])->name('storage-details.edit');

    Route::put('/storage-details/{id}', [StorageDetailController::class, 'update'])->name('storage-details.update');

    Route::patch('/storage-details/{id}/toggle-status', [StorageDetailController::class, 'toggleStatus'])->name('storage-details.toggle-status');

     // Lọc kho tổng theo trạng thái stock_status
    Route::get('/storage-details/status/pending',  [StorageDetailController::class, 'listPending'])->name('storage-details.pending');
    Route::get('/storage-details/status/selling',  [StorageDetailController::class, 'listSelling'])->name('storage-details.selling');
    Route::get('/storage-details/status/sold-out', [StorageDetailController::class, 'listSoldOut'])->name('storage-details.sold-out');
    Route::get('/storage-details/status/stopped',  [StorageDetailController::class, 'listStopped'])->name('storage-details.stopped');
    
    // QUẢN LÝ SẢN PHẨM
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');

    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    Route::patch('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');


    //  DISCOUNT PRODUCT 
    Route::get('/discount-products', [DiscountProductController::class, 'index'])->name('discount-products.index');

    Route::get('/discount-products/create', [DiscountProductController::class, 'create'])->name('discount-products.create');

    Route::post('/discount-products', [DiscountProductController::class, 'store'])->name('discount-products.store');

    Route::get('/discount-products/{id}/edit', [DiscountProductController::class, 'edit'])->name('discount-products.edit');

    Route::put('/discount-products/{id}', [DiscountProductController::class, 'update'])->name('discount-products.update');

    Route::patch('/discount-products/{id}/toggle-status', [DiscountProductController::class, 'toggleStatus'])->name('discount-products.toggle-status');

        //  DISCOUNT PRODUCT DETAIL (pivot) 
    Route::get('/discount-products/{id}/products', [DiscountProductDetailController::class, 'index'])->name('discount-products.products.index');

    Route::post('/discount-products/{id}/products/attach', [DiscountProductDetailController::class, 'attach'])->name('discount-products.products.attach');

    Route::put('/discount-products/{id}/products/{productId}', [DiscountProductDetailController::class, 'updateExpiration'])->name('discount-products.products.update');

    Route::patch('/discount-products/{id}/products/{productId}/toggle', [DiscountProductDetailController::class, 'toggle'])->name('discount-products.products.toggle');


        // DISCOUNT BILL
    Route::get('/discount-bills', [\App\Http\Controllers\Admin\DiscountBillController::class, 'index'])->name('discount-bills.index');

    Route::get('/discount-bills/create', [\App\Http\Controllers\Admin\DiscountBillController::class, 'create'])->name('discount-bills.create');

    Route::post('/discount-bills', [\App\Http\Controllers\Admin\DiscountBillController::class, 'store'])->name('discount-bills.store');

    Route::get('/discount-bills/{id}/edit', [\App\Http\Controllers\Admin\DiscountBillController::class, 'edit'])->name('discount-bills.edit');

    Route::put('/discount-bills/{id}', [\App\Http\Controllers\Admin\DiscountBillController::class, 'update'])->name('discount-bills.update');

    Route::patch('/discount-bills/{id}/toggle-status', [\App\Http\Controllers\Admin\DiscountBillController::class, 'toggleStatus'])->name('discount-bills.toggle-status');

});



    

