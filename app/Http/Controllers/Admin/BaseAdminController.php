<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class BaseAdminController extends Controller
{
    protected function checkLogin()
    {
        if (!Session::has('admin_id')) {
            abort(403, 'Bạn chưa đăng nhập admin');
        }
    }

    protected function allowRoles(array $roles)
    {
        $this->checkLogin();

        if (!in_array(Session::get('admin_role_id'), $roles)) {
            abort(403); // Ẩn trang hoàn toàn
        }
    }
}
// <!-- HÀM CHECK QUYỀN CHUNG (DÙNG CHO TẤT CẢ CONTROLLER ADMIN) -->