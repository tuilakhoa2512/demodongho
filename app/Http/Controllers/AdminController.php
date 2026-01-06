<?php

namespace App\Http\Controllers;

use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Social;
use App\Product;
use App\Statistic;
use App\Visitors;
use Carbon\Carbon;
use App\Models\User;
class AdminController extends Controller
{
    // =========================
    // FORM LOGIN ADMIN
    // =========================
    public function index()
    {
        return view('pages.admin_login');
    }

    // =========================
    // XỬ LÝ LOGIN ADMIN
    // =========================
    public function dashboard(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        //  CHỈ KIỂM TRA BẢNG NHÂN SỰ
        $nhansu = DB::table('nhansu')
            ->where('email', $request->email)
            ->where('status', 1)
            ->first();

        if (!$nhansu || !Hash::check($request->password, $nhansu->password)) {
            return Redirect::to('/admin')
                ->with('message', 'Email hoặc mật khẩu admin không đúng!');
        }

        //  LOGIN OK
        Session::put('admin_id', $nhansu->id);
        Session::put('admin_name', $nhansu->fullname);
        Session::put('admin_role_id', (int)$nhansu->role_id);

        return Redirect::to('/dashboard');
    }

    // =========================
    // DASHBOARD
    // =========================
    public function show_dashboard()
    {
        if (!Session::has('admin_id')) {
            return Redirect::to('/admin');
        }

        return view('pages.admin_layout');
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout()
    {
        Session::flush();
        return Redirect::to('/admin');
    }

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
                'password' => bcrypt('google_default'),
                'role_id' => 2,
            ]);
        }
       
        Auth::login($user);

        // Không cho admin login client
            if ($user->role_id != 2) {
                return redirect('/login-checkout')
                    ->with('error', 'Tài khoản của bạn không được phép đăng nhập ở đây!');
            }

            // Không cho user bị khoá
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
    $googleUser = Socialite::driver('google')->stateless()->user();

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

private function mergeGuestCartToDb(int $userId): void
{
    if ($userId <= 0) return;

    // Cart guest đang lưu ở session key "cart"
    $guestCart = Session::get('cart', []);
    if (empty($guestCart)) return;

    // Lấy danh sách product_id từ guest cart
    $productIds = [];
    foreach ($guestCart as $key => $item) {
        $pid = isset($item['id']) ? (int)$item['id'] : (int)$key;
        if ($pid > 0) $productIds[] = $pid;
    }
    $productIds = array_values(array_unique($productIds));
    if (empty($productIds)) return;

    // Load tồn kho + trạng thái sản phẩm (tránh N+1)
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

        // Chỉ merge nếu đang bán + còn hàng
        if ((int)$p->status !== 1 || $p->stock_status !== 'selling' || (int)$p->quantity <= 0) {
            continue;
        }

        $qty = max(1, (int)($item['quantity'] ?? 1));
        $qty = min($qty, (int)$p->quantity);

        // Nếu đã có trong DB cart => cộng dồn nhưng không vượt tồn
        $existing = DB::table('carts')
            ->where('user_id', $userId)
            ->where('product_id', $pid)
            ->first();

        if ($existing) {
            $newQty = min(((int)$existing->quantity + $qty), (int)$p->quantity);

            DB::table('carts')
                ->where('user_id', $userId)
                ->where('product_id', $pid)
                ->update(['quantity' => $newQty]);
        } else {
            DB::table('carts')->insert([
                'user_id' => $userId,
                'product_id' => $pid,
                'quantity' => $qty,
            ]);
        }
    }

    // Merge xong thì xóa cart guest để CartController đọc DB carts
    Session::forget('cart');
}

}
