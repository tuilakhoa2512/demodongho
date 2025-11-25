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
        $cate_pro = DB::table('categories')->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->orderby('id','asc')->get();
        return view('pages.checkout.login_checkout')->with('category', $cate_pro)
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
        $cate_pro = DB::table('categories')->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->orderby('id','asc')->get();        
        return view('pages.checkout.show_checkout')->with('category', $cate_pro)
        ->with('brand', $brand_pro);
    }
    public function logout_checkout(){
        Session::flush();
        return Redirect::to('/login-checkout');
    }
    public function login_user(Request $request){
        // Kiểm tra dữ liệu đầu vào
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);
    

    // Tìm người dùng trong cơ sở dữ liệu theo email
    $user = DB::table('users')->where('email', $request->email)->first();

    // Kiểm tra xem người dùng có tồn tại, có role_id khác 1, và mật khẩu có khớp không
    if ($user) {
        // Kiểm tra role_id
        if ($user->role_id == 1) {
            return redirect()->route('admin.logincheckout')->withErrors(['email' => 'Tài khoản này không được phép đăng nhập.']);
        }

        if (Hash::check($request->password, $user->password)) {
            // Lưu ID người dùng vào session
            Session::put('id', $user->id);
            return redirect()->route('admin.checkout');
        } else {
            // Mật khẩu không khớp
            return redirect()->route('admin.logincheckout')->withErrors(['email' => 'Thông tin đăng nhập không chính xác.']);
        }
    } else {
        // Không tìm thấy người dùng
        return redirect()->route('admin.logincheckout')->withErrors(['email' => 'Thông tin đăng nhập không chính xác.']);
    }
}
public function register()
{
    return view('pages.checkout.register'); // view mới
}
}