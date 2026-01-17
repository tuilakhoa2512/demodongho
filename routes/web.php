<?php

use App\Http\Controllers\AIChatController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProvinceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\StorageDetailController;
use App\Http\Controllers\Admin\PromotionCampaignController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PromotionRedemptionController;
use App\Http\Controllers\Admin\PromotionTargetController;
use App\Http\Controllers\Admin\PromotionCodeController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\VNPayController;
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
// form đổi mật khẩu
Route::get('/doi-mat-khau', [UserController::class, 'showChangePassword'])
    ->name('profile.changePassword.form');

// xử lý đổi mật khẩu
Route::post('/doi-mat-khau', [UserController::class, 'changePassword'])
    ->name('profile.changePassword');

Route::get('/register', [CheckoutController::class, 'register'])->name('user.register');

Route::get('/payment', [CheckoutController::class, 'payment'])->name('user.payment'); 

//Login user google
Route::get('/login-user-google', [CheckoutController::class, 'login_user_google']);
Route::get('/user/google/callback', [CheckoutController::class, 'callback_user_google']);

// Form nhập email
Route::get('/quen-mat-khau', [ForgotPasswordController::class, 'showForgotForm'])
    ->name('password.forgot');

// Gửi OTP
Route::post('/quen-mat-khau', [ForgotPasswordController::class, 'sendOtp'])
    ->name('password.sendOtp');

// Form nhập OTP + mật khẩu mới
Route::get('/dat-lai-mat-khau', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');

// Xử lý đổi mật khẩu
Route::post('/dat-lai-mat-khau', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.update');

//Trang chu
Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);
Route::post('/tim-kiem', [HomeController::class, 'search']);
Route::get('/tim-kiem', [HomeController::class, 'search'])->name('search');

//Route::get('/product/{id}', [App\Http\Controllers\ProductDetailController::class, 'show']);

//Review
// Lưu review 
Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store');

// Hiển thị danh sách review
Route::get('/review/{product}', [ReviewController::class, 'getReviews'])->name('review.list');



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
Route::get('/danh-muc-san-pham/{category_slug}', [ProductTypeController::class, 'show_category_home'])->name('category.show');
Route::get('/thuong-hieu-san-pham/{brand_slug}', [BrandProductController::class, 'show_brand_home'])->name('brand.show');
Route::get('/danh-muc/{slug}', [ProductTypeController::class, 'showCategory'])->name('category.show');


// Giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');           
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');    
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update'); 
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');


// Payment
Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment', [PaymentController::class, 'placeOrder'])->name('payment.place');
Route::post('/payment/apply-code', [PaymentController::class, 'applyCode'])->name('payment.applyCode');
Route::post('/payment/remove-code', [PaymentController::class, 'removeCode'])->name('payment.removeCode');
Route::post('/payment/apply-promo', [PaymentController::class, 'applyPromo'])->name('payment.applyPromo');
Route::get('/payment/success/{order_code}', [PaymentController::class, 'success'])->name('payment.success');
  //VNPAY
  Route::get('/vnpay/create/{order_code}', [VNPayController::class, 'create'])->name('vnpay.create');
  Route::get('/vnpay/return', [VNPayController::class, 'vnpayReturn'])->name('vnpay.return');
  Route::get('/vnpay/ipn', [VNPayController::class, 'ipn'])->name('vnpay.ipn');  

// My Orders
Route::get('/my-orders', [MyOrderController::class, 'index']);
Route::get('/my-orders/{order_code}', [MyOrderController::class, 'show'])->name('myorders.show');;

//AI Chat
Route::post('/ai-chat', [AIChatController::class, 'chat']);
Route::get('/ai-chat/history', [AIChatController::class, 'history']);
Route::delete('/ai-chat/clear', [AIChatController::class, 'clear']);


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

//Review
Route::get(
  '/toggle-review-status/{id}',[AdminUserController::class, 'toggle_review_status'])->name('admin.review.toggle');

Route::get('/all-reviews-user', [AdminUserController::class, 'all_reviews_user'])->name('admin.reviewuser.index');
//Quản lý user khách hàng
  Route::get('/all-admin-user', [AdminUserController::class, 'all_admin_user'])->name('admin.users.index');
  // NHÂN SỰ
  Route::get('/all-staff-user', [AdminStaffController::class, 'all_staff_user'])->name('admin.staff.index');
  Route::get('/staff/unactive/{id}', [AdminStaffController::class, 'unactive_staff'])->name('admin.staff.unactive');
  Route::get('/staff/active/{id}', [AdminStaffController::class, 'active_staff'])->name('admin.staff.active');

  Route::get('/add-admin-user', [AdminUserController::class, 'add_admin_user'])->name('admin.users.create');
  Route::post('/store-admin-user', [AdminUserController::class, 'store_admin_user'])->name('admin.users.store');
  Route::get('/unactive-admin-user/{id}', [AdminUserController::class, 'unactive_admin_user'])->name('admin.users.unactive');
  Route::get('/active-admin-user/{id}', [AdminUserController::class, 'active_admin_user'])->name('admin.users.active');
  Route::post('/admin/staff/update-role/{id}',[AdminStaffController::class, 'update_staff_role'])->name('admin.staff.updateRole');


  //Sale
  Route::get('/sales', [SaleController::class, 'index'])->name('sales.product');
  Route::get('/product/{id}', [ProductDetailController::class, 'show'])->name('product.detail');

// ================= TRONG PREFIX ADMIN =================

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


    // ======== PROMOTIONS (campaigns -> rules -> targets/codes) ========

    // Campaigns 
    Route::get('/promotions', [\App\Http\Controllers\Admin\PromotionCampaignController::class, 'index'])
        ->name('promotions.index');

    Route::get('/promotions/create', [\App\Http\Controllers\Admin\PromotionCampaignController::class, 'create'])
        ->name('promotions.create');

    Route::post('/promotions', [\App\Http\Controllers\Admin\PromotionCampaignController::class, 'store'])
        ->name('promotions.store');

    Route::get('/promotions/{id}/edit', [\App\Http\Controllers\Admin\PromotionCampaignController::class, 'edit'])
        ->name('promotions.edit');

    Route::put('/promotions/{id}', [\App\Http\Controllers\Admin\PromotionCampaignController::class, 'update'])
        ->name('promotions.update');

    Route::patch('/promotions/{id}/toggle-status', [\App\Http\Controllers\Admin\PromotionCampaignController::class, 'toggleStatus'])
        ->name('promotions.toggle-status');


    // Rules (thuộc Campaign)
    Route::post('/promotions/{id}/rules', [\App\Http\Controllers\Admin\PromotionRuleController::class, 'store'])
        ->name('promotions.rules.store');

    Route::put('/promotions/{id}/rules/{ruleId}', [\App\Http\Controllers\Admin\PromotionRuleController::class, 'update'])
        ->name('promotions.rules.update');

    Route::patch('/promotions/{id}/rules/{ruleId}/toggle-status', [\App\Http\Controllers\Admin\PromotionRuleController::class, 'toggleStatus'])
        ->name('promotions.rules.toggle-status');

    Route::delete('/promotions/{id}/rules/{ruleId}', [\App\Http\Controllers\Admin\PromotionRuleController::class, 'destroy'])
        ->name('promotions.rules.destroy');


    // Targets (thuộc Rule)
    Route::post('/promotions/{id}/rules/{ruleId}/targets', [\App\Http\Controllers\Admin\PromotionTargetController::class, 'store'])
        ->name('promotions.targets.store');

    Route::patch('/promotions/{id}/rules/{ruleId}/targets/{targetId}/toggle-status', [\App\Http\Controllers\Admin\PromotionTargetController::class, 'toggleStatus'])
        ->name('promotions.targets.toggle-status');

    Route::delete('/promotions/{id}/rules/{ruleId}/targets/{targetId}', [\App\Http\Controllers\Admin\PromotionTargetController::class, 'destroy'])
        ->name('promotions.targets.destroy');


    // Codes (thuộc Rule)
    Route::post('/promotions/{id}/rules/{ruleId}/codes', [\App\Http\Controllers\Admin\PromotionCodeController::class, 'store'])
        ->name('promotions.codes.store');

    Route::put('/promotions/{id}/rules/{ruleId}/codes/{codeId}', [\App\Http\Controllers\Admin\PromotionCodeController::class, 'update'])
        ->name('promotions.codes.update');

    Route::patch('/promotions/{id}/rules/{ruleId}/codes/{codeId}/toggle-status', [\App\Http\Controllers\Admin\PromotionCodeController::class, 'toggleStatus'])
        ->name('promotions.codes.toggle-status');

    Route::delete('/promotions/{id}/rules/{ruleId}/codes/{codeId}', [\App\Http\Controllers\Admin\PromotionCodeController::class, 'destroy'])
        ->name('promotions.codes.destroy');


    // Redemptions (log)
    Route::get('/promotion-redemptions', [\App\Http\Controllers\Admin\PromotionRedemptionController::class, 'index'])
        ->name('promotion-redemptions.index');

    Route::get('/promotion-redemptions/{id}', [\App\Http\Controllers\Admin\PromotionRedemptionController::class, 'show'])
        ->name('promotion-redemptions.show');

    //Quan Ly Don Hang
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');

    Route::get('/orders/{order_code}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');

    Route::post('/orders/{order_code}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

});
