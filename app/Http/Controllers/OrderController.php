<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
   
    public function showPaymentForm()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->to('/cart')
                ->with('error', 'Giỏ hàng của bạn đang trống, không thể thanh toán.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('pages.payment', compact('cart', 'total'));
    }

   
    public function placeOrder(Request $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->to('/cart')
                ->with('error', 'Giỏ hàng của bạn đang trống, không thể thanh toán.');
        }

        $data = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:255',
            'customer_note'    => 'nullable|string',
        ]);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

       
        $userId = Session::get('id'); 

     
        DB::beginTransaction();

        try {
          
            $orderId = DB::table('orders')->insertGetId([
                'user_id'          => $userId,      // có thể null nếu khách vãng lai
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'customer_note'    => $data['customer_note'] ?? null,
                'total_amount'     => $total,
                'status'           => 'pending',    // chờ xử lý
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

         
            foreach ($cart as $item) {
                DB::table('order_details')->insert([
                    'order_id'   => $orderId,
                    'product_id' => $item['id'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('products')
                    ->where('id', $item['id'])
                    ->decrement('quantity', $item['quantity']);
            }

            Session::forget('cart');

            DB::commit();

            return redirect()
                ->to('/cart')
                ->with('success', 'Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại cửa hàng.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log lỗi
            // logger()->error($e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra trong quá trình đặt hàng. Vui lòng thử lại sau.');
        }
    }
}
