<?php

namespace App\Http\Controllers;

use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Social;
use App\Product;
use App\Statistic;
use App\Visitors;
use Carbon\Carbon;
use App\Models\User;
class AdminController extends Controller
{
    public function AuthLogin(){
        $id = Session::get('id');
            if($id){
                return Redirect::to('dashboard');
            }else{
                return Redirect::to('admin')->send();
            }
        }    

    public function index()
    {
        return view('pages.admin_login'); 
    }

    public function show_dashboard()
    {
        // Nếu chưa đăng nhập admin, chuyển về trang login
        if (!Session::has('admin_id')) {
            return Redirect::to('/admin');
        }

        return view('pages.admin_layout');
    }

    public function dashboard(Request $request)
    {
        // Validate
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Tìm user theo email
        $user = User::where('email', $request->email)->first();

        // Nếu không có user, hoặc sai mật khẩu, hoặc không phải admin
        if (
            !$user ||
            !Hash::check($request->password, $user->password) ||
            $user->role_id != 1 // admin là role_id = 1
        ) {        
            Session::flash('message', 'Tài khoản hoặc mật khẩu sai!');
            return Redirect::to('/admin')->withInput($request->only('email'));
        }

        Session::put('admin_id', $user->id);
        Session::put('admin_name', $user->fullname);

        return Redirect::to('/dashboard');
    }
    //Đăng xuất admin
    public function logout()
    {
        $this->AuthLogin();
        Session::forget('admin_id');
        Session::forget('admin_name');
        Session::flush(); 

        return Redirect::to('/admin');
    }

    public function login_google()
    {
        return Socialite::driver('google')->redirect();
    }

public function callback_google()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        // Tìm user theo email
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            // Tạo user mới
            $user = User::create([
                'fullname' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => bcrypt('google_default'),
                'role_id' => 2,
            ]);
        }
       
        Auth::login($user);

        // Chỉ user role_id = 2 mới vào client
        if ($user->role_id != 2) {
            Auth::logout();
            return redirect('/login-checkout')->with('error', 'Tài khoản của bạn không được phép đăng nhập ở đây!');
        }

        return redirect('/')->with('message', 'Đăng nhập Google thành công!');

    } catch (\Exception $e) {
        return redirect('/login-checkout')->with('error', 'Đăng nhập Google thất bại!');
    }
}

public function login_user_google()
{
    return Socialite::driver('google')->redirect();
}

public function callback_user_google()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Tìm hoặc tạo tài khoản social
    $socialAccount = $this->findOrCreateUser($googleUser, 'google');

    // Lấy user liên kết
    $user = $socialAccount->user;

    // Lưu session
    Session::put('id', $user->id);
    Session::put('image', $user->image);
    Session::put('fullname', $user->fullname);

    return redirect('/trang-chu')
        ->with('message', 'Đăng nhập Google <span style="color:red">'.$user->email.'</span> thành công!');
}


public function findOrCreateUser($googleUser, $provider)
{
    //Tìm trong bảng social trước
    $social = Social::where('provider_user_id', $googleUser->id)
                    ->where('provider', strtoupper($provider))
                    ->first();

    if ($social) {
        return $social;
    }

    //Nếu không có thì tìm user theo email
    $user = User::where('email', $googleUser->email)->first();

    //Nếu user chưa tồn tại thì tạo mới
    if (!$user) {
        $user = User::create([
            'fullname' => $googleUser->name,
            'email' => $googleUser->email,
            'role_id' => 2,
            'password' => '',
        ]);
    }

    // Tạo tài khoản social mới và liên kết với user
    $social = Social::create([
        'provider_user_id'   => $googleUser->id,
        'provider_user_email'=> $googleUser->email,
        'provider'           => strtoupper($provider),
        'user_id'            => $user->id,
    ]);

    return $social;
}
}
