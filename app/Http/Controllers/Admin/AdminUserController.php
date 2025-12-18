<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // 📌 Danh sách KHÁCH HÀNG
    public function all_admin_user(Request $request)
{
    // lấy trạng thái từ URL (?status=1 | 0)
    $filterStatus = $request->get('status');

    $query = DB::table('users')
        ->where('role_id', 2); // ✅ chỉ khách hàng

    // nếu có lọc
    if ($filterStatus === "1") {
        $query->where('status', 1);
    } elseif ($filterStatus === "0") {
        $query->where('status', 0);
    }

    $users = $query->orderBy('id', 'desc')->get();

    return view('admin.users.all_admin_user', compact('users', 'filterStatus'));
}

    // 📌 Trang thêm KHÁCH HÀNG
    public function add_admin_user()
    {
        return view('admin.users.add_admin_user');
    }

    // 📌 Lưu KHÁCH HÀNG
    public function store_admin_user(Request $request)
    {
        $request->validate([
            'fullname'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ]);

        DB::table('users')->insert([
            'fullname'   => $request->fullname,
            'email'      => $request->email,
            'password'   => Hash::make($request->password), // 🔐 mã hoá
            'phone'      => $request->phone,
            'address'    => $request->address,
            'role_id'    => 2, // ✅ GÁN LÀ KHÁCH HÀNG
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('message', 'Thêm khách hàng thành công');
    }

    // 📌 ẨN KHÁCH HÀNG
    public function unactive_admin_user($id)
    {
        DB::table('users')
            ->where('id', $id)
            ->where('role_id', 2) // 🔒 CHẮC CHẮN LÀ KHÁCH
            ->update(['status' => 0]);

        return redirect()->back()
            ->with('message', 'Đã ẩn tài khoản khách hàng');
    }

    // 📌 KÍCH HOẠT KHÁCH HÀNG
    public function active_admin_user($id)
    {
        DB::table('users')
            ->where('id', $id)
            ->where('role_id', 2)
            ->update(['status' => 1]);

        return redirect()->back()
            ->with('message', 'Đã kích hoạt tài khoản khách hàng');
    }
}
