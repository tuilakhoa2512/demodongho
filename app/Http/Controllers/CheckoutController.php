<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

session_start();
class CheckoutController extends Controller
{
    public function login_checkout(){
        $cate_pro = DB::table('categories') ->where('status', 1)->orderby('id','asc')->get();
        $brand_pro = DB::table('brands') ->where('status', 1)->orderby('id','asc')->get();
        return view('pages.checkout.login_checkout')
        ->with('category', $cate_pro)
        ->with('brand', $brand_pro);
    }
    public function add_user(Request $request){
        $data = array();
        $data['fullname'] = $request->fullname;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);
        $data['phone'] = $request->phone;

        $data['role_id'] = 2;
        $id = DB::table('users')->insertGetId($data);

        Session::put('id',$id);
        Session::put('fullname',$request->fullname);
        return Redirect::to('/checkout');
    }

    public function checkout(){
        $cate_pro = DB::table('categories')->where('status', 1)->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->where('status', 1)->orderby('id','asc')->get();        
        return view('pages.checkout.show_checkout')->with('category', $cate_pro)
        ->with('brand', $brand_pro);
    }
    public function logout_checkout(){
        Session::flush();
        return Redirect::to('/login-checkout');
    }
    public function login_user(Request $request)
{
    // Validate
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Lấy user theo email
    $user = DB::table('users')->where('email', $request->email)->first();

    if (!$user) {
        return redirect('/login-checkout')->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    // Không cho admin login vào trang khách hàng
    if ($user->role_id == 1) {
        return redirect('/login-checkout')->with('error', 'Tài khản này không được phép đăng nhập.');
    }

    // USER BỊ KHOÁ / ẨN
    if ($user->status == 0) {
        return redirect('/login-checkout')
            ->with('error', 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ quản trị viên.');
    }

    // Kiểm tra mật khẩu
    if (!Hash::check($request->password, $user->password)) {
        return redirect('/login-checkout')->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    // Đăng nhập thành công thì lưu session khách hàng
    Session::put('id', $user->id);
    Session::put('fullname', $user->fullname);
    Session::put('role_id', $user->role_id);
    // ===== GỘP YÊU THÍCH GUEST → DB =====
    $guest_favorites = Session::get('favorite_guest', []);

    if (!empty($guest_favorites)) {
        foreach ($guest_favorites as $product_id) {
            DB::table('favorites')->updateOrInsert(
                [
                    'user_id'    => $user->id,
                    'product_id' => $product_id
                ],
                [
                    'created_at' => now()
                ]
            );
        }

        // Xóa yêu thích tạm
        Session::forget('favorite_guest');
    }

    return Redirect::to('/trang-chu');
}
public function payment(){
    $cate_pro = DB::table('categories')->where('status', 1)->orderby('id','asc')->get();
    $brand_pro = DB::table('brands')->where('status', 1)->orderby('id','asc')->get();
    return view('pages.checkout.payment')->with('category', $cate_pro)
    ->with('brand', $brand_pro);    
}
public function register()
{
    return view('pages.checkout.register'); 
}
}