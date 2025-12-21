<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MyOrderController extends Controller
{
    // GET /my-orders
    public function index()
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        return view('pages.my_orders.index', compact('orders'));
    }

    // GET /my-orders/{order_code}
    public function show($order_code)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        $order = DB::table('orders')
            ->where('order_code', $order_code)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return redirect('/my-orders')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $items = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->where('od.order_id', $order->id)
            ->select('p.name', 'od.quantity', 'od.price')
            ->get();

        return view('pages.my_orders.show', compact('order', 'items'));
    }
}
