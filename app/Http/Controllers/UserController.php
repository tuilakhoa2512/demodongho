<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
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

    // Lấy danh sách tỉnh
    $provinces = DB::table('provinces')->orderBy('name')->get();

    // Lấy huyện theo tỉnh người dùng đang chọn
    $districts = [];
    if ($user->province_id) {
        $districts = DB::table('districts')
            ->where('province_id', $user->province_id)
            ->orderBy('name')
            ->get();
    }

    // Lấy xã theo huyện người dùng đang chọn
    $wards = [];
    if ($user->district_id) {
        $wards = DB::table('wards')
            ->where('district_id', $user->district_id)
            ->orderBy('name')
            ->get();
    }

    return view('pages.profile', compact('user', 'provinces', 'districts', 'wards'));
}

public function profileUpdate(Request $request)
{
    $id = Session::get('id');
    if (!$id) {
        return redirect('/login-checkout');
    }

    // Lấy user dạng object
    $user = DB::table('users')->where('id', $id)->first();

    $request->validate([
        'fullname' => [
            'required',
            'string',
            'max:150',
            'regex:/^[\p{L}\s]+$/u'
        ],
    
        'phone' => [
            'nullable',
            'regex:/^[0-9]{10,15}$/'
        ],
    
        'address' => [
            'nullable',
            'string',
            'max:255'
        ],
    
        'province_id' => [
            'nullable',
            'exists:provinces,id'
        ],
    
        'district_id' => [
            'nullable',
            'exists:districts,id'
        ],
    
        'ward_id' => [
            'nullable',
            'exists:wards,id'
        ],
    
        'image' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048'
        ],
    ], [
        'fullname.regex' => 'Họ tên chỉ được chứa chữ cái và khoảng trắng',
        'phone.regex'    => 'Số điện thoại phải từ 10 đến 15 chữ số',
    ]);
    

    // Dữ liệu text
    $data = [
        'fullname'    => $request->fullname,
        'phone'       => $request->phone,
        'address'     => $request->address,
        'province_id' => $request->province_id,
        'district_id' => $request->district_id,
        'ward_id'     => $request->ward_id,
    ];

    // Xử lý ảnh đại diện
    if ($request->hasFile('image')) {

        $file = $request->file('image');
        $path = $file->store('users', 'public'); // upload ảnh mới
        $data['image'] = $path;

        // XÓA ảnh cũ nếu có
        if ($user && $user->image) {
            Storage::disk('public')->delete($user->image);
        }

    } else {
        // KHÔNG upload ảnh mới → giữ ảnh cũ
        $data['image'] = $user ? $user->image : null;
    }

    // Cập nhật database
    DB::table('users')->where('id', $id)->update($data);

    // Cập nhật session
    Session::put('fullname', $data['fullname']);
    Session::put('images', $data['image']);

    return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
}

public function getDistricts($province_id)
    {
        return DB::table('districts')
            ->where('province_id', $province_id)
            ->orderBy('name')
            ->get();
    }

    public function getWards($district_id)
    {
        return DB::table('wards')
            ->where('district_id', $district_id)
            ->orderBy('name')
            ->get();
    }
//Thay đổi mật khẩu khách
public function showChangePassword()
{
    $id = Session::get('id');

    if (!$id) {
        return redirect('/login-checkout');
    }

    $user = DB::table('users')->where('id', $id)->first();

    if (!$user) {
        return redirect('/login-checkout');
    }

    $email = $user->email;

    // Che email: ví dụ lam***@gmail.com
    $maskedEmail = preg_replace(
        '/^(.{3}).*(@gmail\.com)$/',
        '$1***$2',
        $email
    );

    return view('pages.profile.change_password', compact(
        'email',
        'maskedEmail'
    ));
}

/**
 * Xử lý đổi mật khẩu
 */
public function changePassword(Request $request)
{
    $request->validate([
        'email' => [
            'required',
            'email',
            'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
        ],
        'current_password' => [
            'required'
        ],
        'new_password' => [
            'required',
            'min:8',
            'regex:/[A-Z]/',   // ít nhất 1 chữ hoa
            'regex:/[0-9]/',   // ít nhất 1 số
            'confirmed'
        ],
    ], [
        'email.required'            => 'Email không được để trống',
        'email.email'               => 'Email không hợp lệ',
        'email.regex'               => 'Email phải là @gmail.com',

        'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',

        'new_password.required'     => 'Vui lòng nhập mật khẩu mới',
        'new_password.min'          => 'Mật khẩu phải có ít nhất 8 ký tự',
        'new_password.regex'        => 'Mật khẩu phải có ít nhất 1 chữ hoa và 1 số',
        'new_password.confirmed'    => 'Xác nhận mật khẩu không khớp',
    ]);

    $user = DB::table('users')
        ->where('email', $request->email)
        ->first();

    // Mật khẩu hiện tại sai
    if (!$user || !Hash::check($request->current_password, $user->password)) {
        return back()
            ->with('password_error', 'Mật khẩu hiện tại không đúng')
            ->with('active_tab', 'password')
            ->withInput();
    }

    // Không cho trùng mật khẩu cũ
    if (Hash::check($request->new_password, $user->password)) {
        return back()
            ->with('password_error', 'Mật khẩu mới không được trùng mật khẩu hiện tại')
            ->with('active_tab', 'password')
            ->withInput();
    }

    // Update mật khẩu
    DB::table('users')
        ->where('email', $request->email)
        ->update([
            'password' => Hash::make($request->new_password)
        ]);

    return back()
        ->with('password_success', 'Đổi mật khẩu thành công')
        ->with('active_tab', 'password');
}

}
