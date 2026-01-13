<?php

namespace App\Http\Controllers;

use App\Models\Social;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

session_start();

class CheckoutController extends Controller
{
    public function login_checkout(){
        $cate_pro = DB::table('categories')->where('status', 1)->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->where('status', 1)->orderby('id','asc')->get();
        return view('pages.checkout.login_checkout')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro);
    }

    public function add_user(Request $request){
        $request->validate(
            [
                'fullname' => [
                    'required',
                    'string',
                    'max:30',
                    'regex:/^[\pL\s]+$/u', // chỉ chữ + khoảng trắng
                ],
                'email' => [
                    'required',
                    'email',
                    'max:30',
                    'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                    'unique:users,email',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'max:30',
                    'regex:/^(?=.*[A-Z]).+$/',
                    'confirmed'
                ],
                'phone' => [
                    'required',
                    'regex:/^[0-9]{10,15}$/',
                    'unique:users,phone',
                ],
            ],
            [
                // fullname
                'fullname.required' => 'Vui lòng nhập họ và tên.',
                'fullname.max'      => 'Họ và tên không được vượt quá 30 ký tự.',
                'fullname.regex'    => 'Họ và tên chỉ được chứa chữ cái.',
    
                // email
                'email.required' => 'Vui lòng nhập email.',
                'email.email'    => 'Email không đúng định dạng.',
                'email.max'      => 'Email không được vượt quá 30 ký tự.',
                'email.regex'    => 'Email phải kết thúc bằng @gmail.com.',
                'email.unique'   => 'Email này đã được sử dụng.',
    
                // password
                'password.required' => 'Vui lòng nhập mật khẩu.',
                'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
                'password.max'      => 'Mật khẩu không được vượt quá 30 ký tự.',
                'password.regex'    => 'Mật khẩu phải chứa ít nhất 1 chữ cái viết hoa và bao gồm số.',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
    
                // phone
                'phone.required' => 'Vui lòng nhập số điện thoại.',
                'phone.regex'    => 'Số điện thoại phải từ 10 đến 15 chữ số.',
                'phone.unique'   => 'Số điện thoại đã tồn tại.',
            ]
        );
        $data = array();
        $data['fullname'] = $request->fullname;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);
        $data['phone'] = $request->phone;

        $data['role_id'] = 2;
        $id = DB::table('users')->insertGetId($data);

        // Đăng nhập session 
        Session::put('id', $id);
        Session::put('fullname', $request->fullname);
        Session::put('role_id', 2);

        // GỘP CART guest → DB carts
        $this->mergeGuestCartToDb($id);

        return Redirect::to('/trang-chu');
    }

    public function logout_checkout(){
        Session::flush();
        return Redirect::to('/login-checkout');
    }

    public function login_user(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'max:30',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
            ],
            'password' => [
                'required',
                'string',
                'max:30'
            ]
        ], [
            'email.regex' => 'Email phải kết thúc bằng @gmail.com',
        ]);        

        $user = DB::table('users')->where('email', $request->email)->first();
        // CHẶN NHÂN SỰ / ADMIN
        $nhansu = DB::table('nhansu')
        ->where('email', $request->email)
        ->whereIn('role_id', [1, 3, 4, 5])
        ->first();

        if ($nhansu) {
        return redirect('/login-checkout')
            ->with('error', 'Tài khoản này không được phép đăng nhập vào hệ thống người dùng.');
        }

        if (!$user) {
            return redirect('/login-checkout')->with('error', 'Email hoặc mật khẩu không chính xác.');
        }

        if ($user->status == 0) {
            return redirect('/login-checkout')
                ->with('error', 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ quản trị viên.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect('/login-checkout')->with('error', 'Email hoặc mật khẩu không chính xác.');
        }

        //  Đăng nhập thành công thì lưu session khách hàng
        Session::put('id', $user->id);
        Session::put('fullname', $user->fullname);
        Session::put('role_id', $user->role_id);

        //  gộp cart vào db 
        $this->mergeGuestCartToDb($user->id);

        // gộp yêu thích vào db
        $guest_favorites = Session::get('favorite_guest', []);

        if (!empty($guest_favorites)) {
            foreach ($guest_favorites as $product_id) {
                DB::table('favorites')->updateOrInsert(
                    [
                        'user_id'    => $user->id,
                        'product_id' => $product_id
                    ],
                    [
                        'created_at' => now()
                    ]
                );
            }

            Session::forget('favorite_guest');
        }

        return Redirect::to('/trang-chu');
    }

    public function payment(){
        $cate_pro = DB::table('categories')->where('status', 1)->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->where('status', 1)->orderby('id','asc')->get();
        return view('pages.checkout.payment')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro);
    }

    public function register()
    {
        return view('pages.checkout.register');
    }

    
    private function mergeGuestCartToDb($userId)
    {
        $userId = (int) $userId;
        if ($userId <= 0) return;

        $guestCart = Session::get('cart', []);
        if (empty($guestCart)) return;

        // Lấy danh sách product_id
        $productIds = [];
        foreach ($guestCart as $key => $item) {
            $pid = isset($item['id']) ? (int)$item['id'] : (int)$key;
            if ($pid > 0) $productIds[] = $pid;
        }
        $productIds = array_values(array_unique($productIds));
        if (empty($productIds)) return;

        // Load sản phẩm 1 lần để lấy các sản phẩm trong giỏ hàng
        $products = DB::table('products')
            ->whereIn('id', $productIds)
            ->select('id', 'quantity', 'status', 'stock_status')
            ->get()
            ->keyBy('id');

        foreach ($guestCart as $key => $item) {
            $pid = isset($item['id']) ? (int)$item['id'] : (int)$key;
            if ($pid <= 0) continue;

            $p = $products->get($pid);
            if (!$p) continue;

            // chỉ merge nếu còn bán + còn hàng
            if ((int)$p->status !== 1 || $p->stock_status !== 'selling' || (int)$p->quantity <= 0) {
                continue;
            }

            $qty = (int)($item['quantity'] ?? 1);
            $qty = max(1, $qty);
            $qty = min($qty, (int)$p->quantity);

            // Kiểm tra sp đã có chưa, nếu có thì update sl
            $row = DB::table('carts')
                ->where('user_id', $userId)
                ->where('product_id', $pid)
                ->first();

            if ($row) {
                $newQty = (int)$row->quantity + $qty;
                $newQty = min($newQty, (int)$p->quantity);

                DB::table('carts')
                    ->where('user_id', $userId)
                    ->where('product_id', $pid)
                    ->update([
                        'quantity' => $newQty,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('carts')->insert([
                    'user_id'    => $userId,
                    'product_id' => $pid,
                    'quantity'   => $qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        //sau khi gộp xong thì xoá cart guest để tránh nhân đôi
        Session::forget('cart');
    }

//ĐĂNG NHẬP GOOGLE
public function login_google()
{
    return Socialite::driver('google')->redirect();
}

public function callback_google()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        // Tìm user theo email
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            // Tạo user mới
            $user = User::create([
                'fullname' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => bcrypt('google_default'), //pass ko dùng chỉ để tránh null
                'role_id' => 2,
            ]);
        }

        // Không cho admin login client
            if ($user->role_id != 2) {
                return redirect('/login-checkout')
                    ->with('error', 'Tài khoản của bạn không được phép đăng nhập ở đây!');
            }

            // chặn ko cho user đăng nhập khi bị khoá
            if ($user->status == 0) {
                return redirect('/login-checkout')
                    ->with('error', 'Tài khoản của bạn đã bị đình chỉ!');
            }

            Auth::login($user);

            return redirect('/')->with('message', 'Đăng nhập Google thành công!');

    } catch (\Exception $e) {
        return redirect('/login-checkout')->with('error', 'Đăng nhập Google thất bại!');
    }
}

public function login_user_google()
{
    return Socialite::driver('google')->redirect();
}

public function callback_user_google()
{
    /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
    $provider = Socialite::driver('google');
    $googleUser = $provider->stateless()->user();
    // Tìm hoặc tạo tài khoản social
    $socialAccount = $this->findOrCreateUser($googleUser, 'google');

    // Lấy user liên kết
    $user = $socialAccount->user;

    // Không cho admin login client
    if ($user->role_id != 2) {
        return redirect('/login-checkout')
            ->with('error', 'Tài khoản của bạn không được phép đăng nhập!');
    }

    // Không cho user bị đình chỉ
    if ($user->status == 0) {
        return redirect('/login-checkout')
            ->with('error', 'Tài khoản của bạn đã bị đình chỉ!');
    }

    // OK thì mới lưu session
    Session::put('id', $user->id);
    Session::put('image', $user->image);
    Session::put('fullname', $user->fullname);

    // MERGE CART GUEST (Session cart) → DB carts
    $this->mergeGuestCartToDb((int)$user->id);

    return redirect('/trang-chu')
        ->with('message', 'Đăng nhập Google <span style="color:red">'.$user->email.'</span> thành công!');
}



public function findOrCreateUser($googleUser, $provider)
{
    //Tìm trong bảng social trước
    $social = Social::where('provider_user_id', $googleUser->id)
                    ->where('provider', strtoupper($provider))
                    ->first();

    if ($social) {
        return $social;
    }

    //Nếu không có thì tìm user theo email
    $user = User::where('email', $googleUser->email)->first();

    //Nếu user chưa tồn tại thì tạo mới
    if (!$user) {
        $user = User::create([
            'fullname' => $googleUser->name,
            'email' => $googleUser->email,
            'role_id' => 2,
            'status'   => 1,
            'password' => '',
        ]);
    }

    // Tạo tài khoản social mới và liên kết với user
    $social = Social::create([
        'provider_user_id'   => $googleUser->id,
        'provider_user_email'=> $googleUser->email,
        'provider'           => strtoupper($provider),
        'user_id'            => $user->id,
    ]);

    return $social;
}
}
