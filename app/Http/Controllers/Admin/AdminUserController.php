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

        $users = $query->orderBy('id')->get();

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
        $this->checkAdminCoder();

        $request->validate([
            'fullname' => 'required|string|max:150',
            'email'    => 'required|email|max:255|unique:users,email|unique:nhansu,email',
            'password' => 'required|min:6',
            'role_id'  => 'required|integer|in:1,2,3,4,5',
        ]);

        DB::beginTransaction();

        try {

            // ===== KHÁCH HÀNG =====
            if ((int)$request->role_id === 2) {

                DB::table('users')->insert([
                    'fullname'   => $request->fullname,
                    'email'      => $request->email,
                    'password'   => Hash::make($request->password),
                    'role_id'    => 2,
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            
            } elseif (in_array((int)$request->role_id, [1,3,4,5])) {
            
                DB::table('nhansu')->insert([
                    'fullname'   => $request->fullname,
                    'email'      => $request->email,
                    'password'   => Hash::make($request->password),
                    'role_id'    => (int)$request->role_id,
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('message', 'Thêm tài khoản thành công');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors('Lỗi: '.$e->getMessage());
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
            ->paginate(10);

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
