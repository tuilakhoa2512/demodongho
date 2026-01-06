<!-- PHÂN QUYỀN THEO ROLE.QUẢN LÝ Giam đốc (CHỈ role_id = 3) -->

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

class AdminDashboardController extends BaseAdminController
{
    public function dashboard()
{
    $this->allowRoles([1,3]);
    return view('admin.dashboard');
}

}
