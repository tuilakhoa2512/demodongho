<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    // GET /admin  → Hiện form đăng nhập admin
    public function index()
    {
        return view('admin_login'); // resources/views/pages/admin_login.blade.php
    }

    // GET /dashboard → Trang dashboard (chỉ vào được khi đã đăng nhập)
    public function show_dashboard()
    {
        // Nếu chưa đăng nhập admin, chuyển về trang login
        if (!Session::has('admin_id')) {
            return Redirect::to('/admin');
        }

        // Ở đây bạn load view admin_layout
        return view('admin_layout');
    }

    // POST /admin-dashboard → Xử lý login
    public function dashboard(Request $request)
    {
        // Validate cơ bản
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
            $user->role_id != 1 // chỉ cho role admin (id = 1) đăng nhập
        ) {
            // Gửi thông báo lỗi sang session
            Session::flash('message', 'Tài khoản hoặc mật khẩu sai!');
            // Quay lại trang /admin, giữ lại email vừa nhập
            return Redirect::to('/admin')->withInput($request->only('email'));
        }

        // Đăng nhập thành công → lưu session
        Session::put('admin_id', $user->id);
        Session::put('admin_name', $user->fullname);

        // Chuyển hướng đến dashboard
        return Redirect::to('/dashboard');
    }

    // GET /logout → Đăng xuất admin
    public function logout()
    {
        Session::forget('admin_id');
        Session::forget('admin_name');
        Session::flush(); // xóa hết session nếu muốn

        return Redirect::to('/admin');
    }
}
