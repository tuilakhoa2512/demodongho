<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminStaffController extends BaseAdminController
{
    /**
     * Trang index (nếu có)
     */
    public function index()
    {
        $this->allowRoles([1]); // chỉ Admin Coder
        return view('admin.nhansu.index');
    }

    /**
     * ===============================
     * KIỂM TRA QUYỀN XEM DANH SÁCH
     * role: 1,3,4
     * ===============================
     */
    private function checkPermission()
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login')
                ->with('error', 'Bạn chưa đăng nhập admin');
        }

        $role = (int) Session::get('admin_role_id');

        if (!in_array($role, [1, 3, 4])) {
            return redirect()->back()
                ->with('error', 'Bạn không có quyền truy cập chức năng này');
        }

        return null;
    }

    /**
     * ===============================
     * DANH SÁCH NHÂN SỰ
     * role_id = 1,3,4,5
     * ===============================
     */
    public function all_staff_user(Request $request)
    {
        if ($resp = $this->checkPermission()) {
            return $resp;
        }

        $filterStatus = $request->get('status');

        $query = DB::table('nhansu')
            ->whereIn('role_id', [1, 3, 4, 5]);

        if ($filterStatus === "1") {
            $query->where('status', 1);
        } elseif ($filterStatus === "0") {
            $query->where('status', 0);
        }

        $staffs = $query
            ->orderBy('role_id', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.staff.all_staff_user', compact('staffs', 'filterStatus'));
    }

    /**
     * ===============================
     * KIỂM TRA QUYỀN ĐÌNH CHỈ
     * role: 1,3
     * ===============================
     */
    private function allowSuspendStaff()
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login')
                ->with('error', 'Bạn chưa đăng nhập admin');
        }

        $role = (int) Session::get('admin_role_id');

        if (!in_array($role, [1, 3])) {
            return redirect()->back()
                ->with('error', 'Chỉ Admin Coder hoặc Giám đốc mới được đình chỉ nhân sự');
        }

        return null;
    }

    /**
     * ===============================
     * ĐÌNH CHỈ NHÂN SỰ
     * ===============================
     */
    public function unactive_staff($id)
    {
        if ($resp = $this->allowSuspendStaff()) {
            return $resp;
        }

        $currentAdminId   = (int) Session::get('admin_id');
        $currentAdminRole = (int) Session::get('admin_role_id');

        $targetStaff = DB::table('nhansu')->where('id', $id)->first();

        if (!$targetStaff) {
            return redirect()->back()
                ->with('error', 'Nhân sự không tồn tại');
        }

        //  không được tự đình chỉ chính mình
        if ($currentAdminId === (int) $targetStaff->id) {
            return redirect()->back()
                ->with('error', 'Bạn không thể đình chỉ chính mình');
        }

        //  không ai được đình chỉ Admin Coder
        if ((int)$targetStaff->role_id === 1) {
            return redirect()->back()
                ->with('error', 'Không thể đình chỉ Admin Coder');
        }

        DB::table('nhansu')
            ->where('id', $id)
            ->update(['status' => 0]);

        return redirect()->back()
            ->with('success', 'Đã đình chỉ nhân sự');
    }

    /**
     * ===============================
     * KÍCH HOẠT NHÂN SỰ
     * ===============================
     */
    public function active_staff($id)
    {
        if ($resp = $this->allowSuspendStaff()) {
            return $resp;
        }

        $currentAdminId = (int) Session::get('admin_id');

        $targetStaff = DB::table('nhansu')->where('id', $id)->first();

        if (!$targetStaff) {
            return redirect()->back()
                ->with('error', 'Nhân sự không tồn tại');
        }

        if ($currentAdminId === (int) $targetStaff->id) {
            return redirect()->back()
                ->with('error', 'Bạn không thể thay đổi trạng thái của chính mình');
        }

        DB::table('nhansu')
            ->where('id', $id)
            ->update(['status' => 1]);

        return redirect()->back()
            ->with('success', 'Đã kích hoạt nhân sự');
    }

    /**
     * ===============================
     * CẬP NHẬT PHÂN QUYỀN
     * CHỈ ADMIN CODER
     * ===============================
     */
    public function update_staff_role(Request $request, $id)
    {
        if ((int) Session::get('admin_role_id') !== 1) {
            return redirect()->back()
                ->with('error', 'Bạn không có quyền phân quyền nhân sự');
        }

        $request->validate([
            'role_id' => 'required|in:1,3,4,5'
        ]);

        DB::table('nhansu')
            ->where('id', $id)
            ->update([
                'role_id'    => (int) $request->role_id,
                'updated_at' => now(),
            ]);

        return redirect()->back()
            ->with('success', 'Cập nhật quyền nhân sự thành công');
    }
}

// <!-- PHÂN QUYỀN THEO ROLE.QUẢN LÝ NHÂN VIÊN (CHỈ role_id = 1) -->