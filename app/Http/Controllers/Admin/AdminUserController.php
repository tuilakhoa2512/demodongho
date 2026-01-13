<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Review;

class AdminUserController extends BaseAdminController
{
    protected function checkAdminCoder()
{
    //  Chưa đăng nhập admin
    if (!Session::has('admin_id')) {
        abort(403, 'Bạn chưa đăng nhập admin');
    }

    //  Không phải Admin Coder
    if ((int) Session::get('admin_role_id') !== 1) {
        abort(403, 'Bạn không có quyền thực hiện chức năng này');
    }
}

    /**
     * =========================
     * DANH SÁCH KHÁCH HÀNG
     * Role: 1,3,4
     * =========================
     */
    public function all_admin_user(Request $request)
    {
        $this->allowRoles([1,3,4]);

        $filterStatus = $request->get('status');

        $query = DB::table('users')
            ->where('role_id', 2); // chỉ khách hàng

        if ($filterStatus === '1') {
            $query->where('status', 1);
        } elseif ($filterStatus === '0') {
            $query->where('status', 0);
        }

        $users = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.users.all_admin_user', compact('users', 'filterStatus'));
    }

    /**
     * =========================
     * FORM THÊM USER
     * CHỈ ADMIN CODER
     * =========================
     */
    public function add_admin_user()
    {
        $this->checkAdminCoder();

        return view('admin.users.add_admin_user');
    }

    /**
     * =========================
     * LƯU USER
     * - role_id = 2 → users
     * - role_id = 1,3,4,5 → nhansu
     * =========================
     */
    public function store_admin_user(Request $request)
{
    //  BẮT BUỘC đăng nhập
    if (!Session::has('admin_id')) {
        abort(403, 'Bạn chưa đăng nhập admin');
    }

    $creatorRole = (int) Session::get('admin_role_id'); // role người tạo
    $targetRole  = (int) $request->role_id;             // role sắp tạo

    /**
     * =========================
     * VALIDATE DỮ LIỆU
     * =========================
     */
    $request->validate(
        [
            'fullname' => [
                'required',
                'string',
                'min:3',
                'max:25',
                'regex:/^[\pL\s]+$/u',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
                'unique:nhansu,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
            'role_id' => [
                'required',
                'integer',
                'in:1,2,3,4,5',
            ],
        ],
        [
            'fullname.regex' => 'Họ tên chỉ được chứa chữ cái.',
            'password.regex' => 'Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt.',
        ]
    );

    /**
     * =========================
     * RÀNG BUỘC PHÂN QUYỀN
     * =========================
     */

    //  KHÁCH HÀNG KHÔNG ĐƯỢC TẠO BẤT KỲ AI
    if ($creatorRole === 2) {
        abort(403, 'Bạn không có quyền tạo tài khoản.');
    }

    //  role 4,5 KHÔNG ĐƯỢC TẠO NHÂN SỰ
    if (in_array($creatorRole, [4,5]) && $targetRole !== 2) {
        abort(403, 'Bạn chỉ được tạo tài khoản khách hàng.');
    }

    //  chỉ role 1,3 mới được tạo NHÂN SỰ
    if (in_array($targetRole, [1,3,4,5]) && !in_array($creatorRole, [1,3])) {
        abort(403, 'Chỉ Admin Coder hoặc Giám đốc mới được tạo tài khoản nhân sự.');
    }

    DB::beginTransaction();

    try {

        /**
         * =========================
         * TẠO KHÁCH HÀNG
         * =========================
         */
        if ($targetRole === 2) {
            DB::table('users')->insert([
                'fullname'   => $request->fullname,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role_id'    => 2,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /**
         * =========================
         * TẠO NHÂN SỰ
         * =========================
         */
        if (in_array($targetRole, [1,3,4,5])) {
            DB::table('nhansu')->insert([
                'fullname'   => $request->fullname,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role_id'    => $targetRole,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return redirect()
            ->route('admin.users.index')
            ->with('message', 'Thêm tài khoản thành công');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors('Lỗi hệ thống: '.$e->getMessage());
    }
}


    /**
     * =========================
     * KHOÁ / MỞ KHÁCH HÀNG
     * =========================
     */
    public function unactive_admin_user($id)
    {
        $this->allowRoles([1,3,4]);

        DB::table('users')
            ->where('id', $id)
            ->where('role_id', 2)
            ->update(['status' => 0]);

        return back()->with('message', 'Đã khoá khách hàng');
    }

    public function active_admin_user($id)
    {
        $this->allowRoles([1,3,4]);

        DB::table('users')
            ->where('id', $id)
            ->where('role_id', 2)
            ->update(['status' => 1]);

        return back()->with('message', 'Đã mở khoá khách hàng');
    }

    /**
     * =========================
     * QUẢN LÝ REVIEW
     * =========================
     */
    public function all_reviews_user()
    {
        $this->allowRoles([1,3,4]);

        $reviews = Review::with(['user', 'product'])
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('admin.reviews_user.index', compact('reviews'));
    }

    public function toggle_review_status($id)
    {
        $this->allowRoles([1,3,4]);

        $review = Review::findOrFail($id);
        $review->status = $review->status ? 0 : 1;
        $review->save();

        return back()->with('success', 'Cập nhật đánh giá thành công');
    }
}
