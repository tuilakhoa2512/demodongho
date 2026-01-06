<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Cart;

// ✅ NEW (promotion system)
use App\Models\PromotionRedemption;

class VNPayController extends Controller
{
    /**
     * Tạo URL thanh toán VNPay
     */
    public function create(Request $request, string $order_code)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        if (!$order_code) {
            return redirect()->route('payment.show')->with('error', 'Thiếu mã đơn hàng để thanh toán VNPay.');
        }

        $order = Order::where('order_code', $order_code)->first();
        if (!$order) {
            return redirect()->route('payment.show')->with('error', 'Không tìm thấy đơn hàng để thanh toán VNPay.');
        }

        // ✅ chống pay hộ
        if ((int)$order->user_id !== $userId) {
            return redirect()->route('myorders.index')->with('error', 'Bạn không có quyền thanh toán đơn hàng này.');
        }

        // chỉ cho thanh toán khi pending
        if (($order->status ?? null) !== 'pending') {
            return redirect()->route('payment.success', ['order_code' => $order_code])
                ->with('error', 'Đơn này không ở trạng thái pending nên không thể thanh toán VNPay.');
        }

        // Nếu có stock_deducted và đã trừ kho rồi thì không cho thanh toán lại
        if (Schema::hasColumn('orders', 'stock_deducted') && (int)($order->stock_deducted ?? 0) === 1) {
            return redirect()->route('payment.success', ['order_code' => $order_code])
                ->with('error', 'Đơn này đã được xử lý kho trước đó.');
        }

        // VNPay dùng đơn vị *100
        $amount = (int) round(((float)$order->total_price) * 100);

        $vnp_TmnCode    = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url        = config('vnpay.url');
        $vnp_ReturnUrl  = config('vnpay.return_url');

        if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Url || !$vnp_ReturnUrl) {
            return redirect()->route('payment.show')
                ->with('error', 'Thiếu cấu hình VNPay (tmn_code/hash_secret/url/return_url).');
        }

        $vnp_TxnRef    = $order->order_code;
        $vnp_OrderInfo = 'Thanh toan don hang ' . $order->order_code;
        $vnp_IpAddr    = $request->ip();

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $vnp_IpAddr,
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => "other",
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $vnp_TxnRef,
        ];

        ksort($inputData);

        $hashdata = '';
        $query = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            else { $hashdata .= urlencode($key) . '=' . urlencode($value); $i = 1; }

            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

        // set payment_method
        $order->update(['payment_method' => 'VNPAY']);

        return redirect()->away($paymentUrl);
    }

    /**
     * VNPay return
     * - Success: trừ kho + clear cart + status=confirmed + set promotion_redemptions.used_at
     * - Fail: status=canceled
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;

        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            else { $hashData .= urlencode($key) . '=' . urlencode($value); $i = 1; }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $orderCode = $request->get('vnp_TxnRef');
        $respCode  = $request->get('vnp_ResponseCode');
        $txnStatus = $request->get('vnp_TransactionStatus');
        $vnpAmount = (int)($request->get('vnp_Amount') ?? 0);

        if (!$orderCode) {
            return redirect('/my-orders')->with('error', 'VNPay return thiếu mã đơn.');
        }

        // Check chữ ký
        if (!$vnp_SecureHash || $secureHash !== $vnp_SecureHash) {
            return redirect('/my-orders/' . $orderCode)->with('error', 'Sai chữ ký VNPay (vnp_SecureHash).');
        }

        $order = Order::where('order_code', $orderCode)->first();
        if (!$order) {
            return redirect('/my-orders')->with('error', 'Không tìm thấy đơn để cập nhật thanh toán.');
        }

        // ✅ check amount khớp
        $expectedAmount = (int) round(((float)$order->total_price) * 100);
        if ($vnpAmount > 0 && $vnpAmount !== $expectedAmount) {
            return redirect()
                ->route('payment.success', ['order_code' => $orderCode])
                ->with('error', 'Sai số tiền VNPay so với đơn hàng.');
        }

        $isSuccess = ($respCode === "00" && $txnStatus === "00");

        // Fail => hủy đơn (chỉ khi pending)
        if (!$isSuccess) {
            if ($order->status === 'pending') {
                $order->update([
                    'payment_method' => 'VNPAY',
                    'status' => 'canceled',
                ]);
            }

            return redirect()
                ->route('payment.success', ['order_code' => $orderCode])
                ->with('error', 'Thanh toán VNPay đã bị hủy hoặc không thành công. Đơn hàng đã được hủy.');
        }

        // Nếu đã xử lý rồi thì thôi
        if ($order->status !== 'pending') {
            return redirect()
                ->route('payment.success', ['order_code' => $orderCode])
                ->with('success', 'Thanh toán VNPay thành công.');
        }

        DB::beginTransaction();
        try {
            // lock order để tránh return/ipn đụng nhau
            $order = Order::where('id', $order->id)->lockForUpdate()->first();

            // anti double tuyệt đối nếu có stock_deducted
            if (Schema::hasColumn('orders', 'stock_deducted') && (int)($order->stock_deducted ?? 0) === 1) {
                DB::commit();
                return redirect()
                    ->route('payment.success', ['order_code' => $orderCode])
                    ->with('success', 'Thanh toán VNPay thành công.');
            }

            $details = OrderDetail::where('order_id', $order->id)->get();

            foreach ($details as $d) {
                $updated = Product::where('id', $d->product_id)
                    ->where('quantity', '>=', $d->quantity)
                    ->decrement('quantity', $d->quantity);

                if (!$updated) {
                    throw new \Exception('Sản phẩm đã hết hàng hoặc không đủ tồn kho khi xác nhận VNPay.');
                }
            }

            // clear cart
            Cart::where('user_id', $order->user_id)->delete();

            // promotion_redemptions used_at
            PromotionRedemption::where('order_id', $order->id)
                ->whereNull('used_at')
                ->update(['used_at' => now()]);

            // update order
            $update = [
                'payment_method' => 'VNPAY',
                'status'         => 'confirmed',
            ];
            if (Schema::hasColumn('orders', 'stock_deducted')) {
                $update['stock_deducted'] = 1;
            }

            $order->update($update);

            DB::commit();

            return redirect()
                ->route('payment.success', ['order_code' => $orderCode])
                ->with('success', 'Thanh toán VNPay thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();

            // nếu fail xử lý kho => hủy đơn để không treo pending
            $order->update([
                'payment_method' => 'VNPAY',
                'status'         => 'canceled',
            ]);

            return redirect()
                ->route('payment.success', ['order_code' => $orderCode])
                ->with('error', 'Thanh toán VNPay thành công nhưng xử lý đơn thất bại: ' . $e->getMessage());
        }
    }

    /**
     * IPN (server-to-server) — tối thiểu
     */
    public function ipn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;

        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            else { $hashData .= urlencode($key) . '=' . urlencode($value); $i = 1; }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $orderCode = $request->get('vnp_TxnRef');
        $respCode  = $request->get('vnp_ResponseCode');
        $txnStatus = $request->get('vnp_TransactionStatus');
        $vnpAmount = (int)($request->get('vnp_Amount') ?? 0);

        if (!$orderCode) return response()->json(["RspCode" => "01", "Message" => "Order not found"]);
        if (!$vnp_SecureHash || $secureHash !== $vnp_SecureHash) {
            return response()->json(["RspCode" => "97", "Message" => "Invalid signature"]);
        }

        $order = Order::where('order_code', $orderCode)->first();
        if (!$order) return response()->json(["RspCode" => "01", "Message" => "Order not found"]);

        // check amount
        $expectedAmount = (int) round(((float)$order->total_price) * 100);
        if ($vnpAmount > 0 && $vnpAmount !== $expectedAmount) {
            return response()->json(["RspCode" => "04", "Message" => "Invalid amount"]);
        }

        $isSuccess = ($respCode === "00" && $txnStatus === "00");

        // Nếu đã xử lý rồi => OK
        if ($order->status !== 'pending') {
            return response()->json(["RspCode" => "00", "Message" => "Confirm Success"]);
        }

        if (!$isSuccess) {
            $order->update(['payment_method' => 'VNPAY', 'status' => 'canceled']);
            return response()->json(["RspCode" => "00", "Message" => "Confirm Success"]);
        }

        DB::beginTransaction();
        try {
            $order = Order::where('id', $order->id)->lockForUpdate()->first();

            if (Schema::hasColumn('orders', 'stock_deducted') && (int)($order->stock_deducted ?? 0) === 1) {
                DB::commit();
                return response()->json(["RspCode" => "00", "Message" => "Confirm Success"]);
            }

            $details = OrderDetail::where('order_id', $order->id)->get();

            foreach ($details as $d) {
                $updated = Product::where('id', $d->product_id)
                    ->where('quantity', '>=', $d->quantity)
                    ->decrement('quantity', $d->quantity);

                if (!$updated) {
                    throw new \Exception('Not enough stock');
                }
            }

            Cart::where('user_id', $order->user_id)->delete();

            PromotionRedemption::where('order_id', $order->id)
                ->whereNull('used_at')
                ->update(['used_at' => now()]);

            $update = ['payment_method' => 'VNPAY', 'status' => 'confirmed'];
            if (Schema::hasColumn('orders', 'stock_deducted')) {
                $update['stock_deducted'] = 1;
            }
            $order->update($update);

            DB::commit();
            return response()->json(["RspCode" => "00", "Message" => "Confirm Success"]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $order->update(['payment_method' => 'VNPAY', 'status' => 'canceled']);
            return response()->json(["RspCode" => "00", "Message" => "Confirm Success"]);
        }
    }
}
