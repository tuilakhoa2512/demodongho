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
    // public function show_dashboard()
    // {
    //     if (!Session::has('admin_id')) {
    //         return Redirect::to('/admin');
    //     }

    //     return view('pages.admin_layout');
    // }
    public function show_dashboard()
    {
        if (!Session::has('admin_id')) {
            return Redirect::to('/admin');
        }
    
        /* ================== THỐNG KÊ TRÊN ================== */
    
        // Khách hàng (không tính nhân sự)
        $totalCustomers = DB::table('users')->count();
    
        // Tổng đơn hàng (mọi trạng thái)
        $totalOrders = DB::table('orders')->count();
    
        // Tổng sản phẩm đang bán (không tính số lượng)
        $totalProducts = DB::table('products')
            ->where('status', 1)
            ->count();
    
        // Tổng đánh giá
        $totalReviews = DB::table('reviews')->count();
    
        /* ================== BIỂU ĐỒ ================== */
    
        // Doanh thu theo tháng (chỉ đơn hoàn tất)
        $revenueRaw = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->where('status', 'success')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    
            $revenueData = [];
            for ($i = 1; $i <= 12; $i++) {
                $found = $revenueRaw->firstWhere('month', $i);
                $revenueData[] = [
                    'month' => 'Tháng ' . $i,
                    'total' => $found ? (int)$found->total : 0
                ];
            }
        
            // ===============================
            // BIỂU ĐỒ SỐ ĐƠN HÀNG THEO THÁNG
            // ===============================
            $orderRaw = DB::table('orders')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        
            $orderData = [];
            for ($i = 1; $i <= 12; $i++) {
                $found = $orderRaw->firstWhere('month', $i);
                $orderData[] = [
                    'month' => 'Tháng ' . $i,
                    'total' => $found ? (int)$found->total : 0
                ];
            }
        
            return view('pages.dashboard', compact(
                'totalCustomers',
                'totalOrders',
                'totalProducts',
                'totalReviews',
                'revenueData',
                'orderData'
            ));
        }
    
    // =========================
    // LOGOUT
    // =========================
    public function logout()
    {
        Session::flush();
        return Redirect::to('/admin');
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
