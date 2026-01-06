<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

class AdminWarehouseController extends BaseAdminController
{
    public function index()
{
    $this->allowRoles([1,3,5]);
}

}
