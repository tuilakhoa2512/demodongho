<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminStaffController extends BaseAdminController
{
    public function index()
    {
        $this->allowRoles([1]); // CHỈ ADMIN CODER
        return view('admin.nhansu.index');
    }
    /**
    * Chỉ cho role 1,3,4 xem danh sách nhân sự
    */
   private function checkPermission()
   {
       if (!Session::has('admin_id')) {
           abort(403, 'Bạn chưa đăng nhập admin');
       }

       $role = (int) Session::get('admin_role_id');

       if (!in_array($role, [1, 3, 4])) {
           abort(403, 'Bạn không có quyền truy cập chức năng này');
       }
   }

    /**
     * DANH SÁCH NHÂN SỰ
     * role_id = 1,3,4,5
     */
    public function all_staff_user(Request $request)
    {
        $this->checkPermission();

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
            ->get();

        return view('admin.staff.all_staff_user', compact('staffs', 'filterStatus'));
    }
    /**
     * Đình chỉ nhân sự
     */
    public function unactive_staff($id)
    {
        $this->checkPermission();

        DB::table('nhansu')
            ->where('id', $id)
            ->update(['status' => 0]);

        return redirect()->back()
            ->with('message', 'Đã đình chỉ nhân sự');
    }

    /**
     * Kích hoạt nhân sự
     */
    public function active_staff($id)
    {
        $this->checkPermission();

        DB::table('nhansu')
            ->where('id', $id)
            ->update(['status' => 1]);

        return redirect()->back()
            ->with('message', 'Đã kích hoạt nhân sự');
    }

    public function update_staff_role(Request $request, $id)
{
    // Chỉ ADMIN CODER
    if ((int)Session::get(key: 'admin_role_id') !== 1) {
        abort(403, 'Bạn không có quyền phân quyền nhân sự');
    }

    $request->validate([
        'role_id' => 'required|in:1,3,4,5'
    ]);

    DB::table('nhansu')
        ->where('id', $id)
        ->update([
            'role_id'    => (int)$request->role_id,
            'updated_at' => now()
        ]);

    return redirect()->back()
        ->with('message', 'Cập nhật quyền nhân sự thành công');
}

}
// <!-- PHÂN QUYỀN THEO ROLE.QUẢN LÝ NHÂN VIÊN (CHỈ role_id = 1) -->