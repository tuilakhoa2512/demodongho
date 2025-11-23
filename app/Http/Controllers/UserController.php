<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

session_start();

class UserController extends Controller
{
    public function getUserInfo()
{
    $id = Session::get('id');
    if ($id) {
        $user = DB::table('users')->where('id', $id)->first();
        
        // Lưu thông tin người dùng vào session
        Session::put('fullname', $user->fullname);
        Session::put('images', $user->image);
    }
}
    public function profile()
{
    $id = Session::get('id');
    if (!$id) {
        return redirect('/login-checkout');
    }

    $user = DB::table('users')->where('id', $id)->first();
    return view('pages.profile', compact('user'));
}

public function profileUpdate(Request $request)
{
    $id = Session::get('id');
    if (!$id) {
        return redirect('/login-checkout');
    }
    $user = DB::table('users')->where('id', $id)->first();

    $data = [];
    $data['fullname'] = $request->fullname;
    $data['phone'] = $request->phone;
    $data['address'] = $request->address;
    $data['district'] = $request->district;
    $data['ward'] = $request->ward;
    $data['province'] = $request->province;

    // upload ảnh đại diện
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        // lưu vào storage/app/public/users
        $path = $file->store('users', 'public'); // vd "brands/logo1.jpg"
            $data['image'] = $path;
        } else {
            $data['image'] = null;
        } 
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('users', 'public'); // ảnh mới

            $data['image'] = $path;

            // xóa ảnh cũ
            if ($user && $user->image) {
                Storage::disk('public')->delete($user->image);
            }
        } else {
            // không upload ảnh mới → giữ nguyên ảnh cũ
            $data['image'] = $user ? $user->image : null;
        }    

    DB::table('users')->where('id', $id)->update($data);

    return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
}

}
