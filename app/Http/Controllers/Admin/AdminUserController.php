<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Review;

class AdminUserController extends Controller
{
    // Danh sách KHÁCH HÀNG
    public function all_admin_user(Request $request)
{
    // lấy trạng thái từ URL (?status=1 | 0)
    $filterStatus = $request->get('status');

    $query = DB::table('users')
        ->where('role_id', 2); //chỉ khách hàng

    // nếu có lọc
    if ($filterStatus === "1") {
        $query->where('status', 1);
    } elseif ($filterStatus === "0") {
        $query->where('status', 0);
    }

    $users = $query->orderBy('id', 'desc')->get();

    return view('admin.users.all_admin_user', compact('users', 'filterStatus'));
}

    // Trang thêm KHÁCH HÀNG
    public function add_admin_user()
    {
        return view('admin.users.add_admin_user');
    }

    // Lưu KHÁCH HÀNG
    public function store_admin_user(Request $request)
{
    $request->validate(
        [
            'fullname' => ['required','string','max:30','regex:/^[\pL\s]+$/u',],

            'email' => ['required','email','max:255','unique:users,email','regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/',],

            'password' => ['required','string','max:30','min:6',],

            'phone' => ['nullable','regex:/^[0-9]+$/',],

            'address' => ['nullable','string','max:255',],
        ],
        [
            // ===== MESSAGE TIẾNG VIỆT =====
            'fullname.required' => 'Vui lòng nhập họ tên.',
            'fullname.max' => 'Họ tên không được vượt quá 30 ký tự.',
            'fullname.regex' => 'Họ tên không được chứa số hoặc ký tự đặc biệt.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'email.regex' => 'Email phải có định dạng @gmail.com.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá 30 ký tự.',

            'phone.regex' => 'Số điện thoại chỉ được chứa chữ số.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ]
    );

    DB::table('users')->insert([
        'fullname'   => $request->fullname,
        'email'      => $request->email,
        'password'   => Hash::make($request->password), // mã hoá
        'phone'      => $request->phone,
        'address'    => $request->address,
        'role_id'    => 2, // KHÁCH HÀNG
        'status'     => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()
        ->route('admin.users.index')
        ->with('message', 'Thêm khách hàng thành công');
}


    // ẨN KHÁCH HÀNG
    public function unactive_admin_user($id)
    {
        DB::table('users')
            ->where('id', $id)
            ->where('role_id', 2) // 🔒 CHẮC CHẮN LÀ KHÁCH
            ->update(['status' => 0]);

        return redirect()->back()
            ->with('message', 'Đã ẩn tài khoản khách hàng');
    }

    //KÍCH HOẠT KHÁCH HÀNG
    public function active_admin_user($id)
    {
        DB::table('users')
            ->where('id', $id)
            ->where('role_id', 2)
            ->update(['status' => 1]);

        return redirect()->back()
            ->with('message', 'Đã kích hoạt tài khoản khách hàng');
    }
    public function all_reviews_user()
{
    $reviews = Review::with(['user', 'product'])
        ->orderByDesc('created_at')
        ->paginate(10);

    return view('admin.reviews_user.index', compact('reviews'));
}
public function toggle_review_status($id)
{
    $review = Review::findOrFail($id);

    // Đảo trạng thái
    $review->status = $review->status == 1 ? 0 : 1;
    $review->save();

    return redirect()->back()->with('success', 'Cập nhật trạng thái đánh giá thành công');
}
}
