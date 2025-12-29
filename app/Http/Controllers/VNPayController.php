<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VNPayController extends Controller
{
    /**
     * GET /vnpay/create/{order_code}
     * Tạo URL thanh toán và redirect sang VNPay
     */
    public function create(Request $request, string $order_code)
    {
        if (!$order_code) {
            return redirect('/payment')->with('error', 'Thiếu mã đơn hàng để thanh toán VNPay.');
        }

        $order = DB::table('orders')->where('order_code', $order_code)->first();
        if (!$order) {
            return redirect('/payment')->with('error', 'Không tìm thấy đơn hàng để thanh toán VNPay.');
        }

        // (Khuyến nghị) chỉ cho thanh toán khi đơn còn hợp lệ
        // nếu bạn có payment_status thì check ở đây, còn không thì bỏ qua.
        // Ví dụ: không cho thanh toán nếu đã hủy:
        if (($order->status ?? null) === 'canceled') {
            return redirect('/my-orders/' . $order_code)->with('error', 'Đơn hàng đã bị hủy, không thể thanh toán.');
        }

        // VNPay yêu cầu: VND * 100
        $amount = (int) round(((float)$order->total_price) * 100);

        $vnp_TmnCode    = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url        = config('vnpay.url');
        $vnp_ReturnUrl  = config('vnpay.return_url'); // ví dụ: http://127.0.0.1:8000/vnpay/return

        if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Url || !$vnp_ReturnUrl) {
            return redirect('/payment')->with('error', 'Thiếu cấu hình VNPay (tmn_code/hash_secret/url/return_url).');
        }

        $vnp_TxnRef    = $order->order_code; // dùng order_code để đối soát
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

        // sort + build query/hash theo chuẩn VNPay
        ksort($inputData);

        $hashdata = '';
        $query = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

        // cập nhật phương thức (nếu bạn muốn)
        DB::table('orders')->where('id', $order->id)->update([
            'payment_method' => 'VNPAY',
        ]);

        return redirect()->away($paymentUrl);
    }

    /**
     * GET /vnpay/return
     * VNPay redirect về đây sau khi thanh toán
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $orderCode = $request->get('vnp_TxnRef');                 // order_code
        $respCode  = $request->get('vnp_ResponseCode');           // "00" ok
        $txnStatus = $request->get('vnp_TransactionStatus');      // "00" ok

        if (!$orderCode) {
            return redirect('/my-orders')->with('error', 'VNPay return thiếu mã đơn.');
        }

        $order = DB::table('orders')->where('order_code', $orderCode)->first();
        if (!$order) {
            return redirect('/my-orders')->with('error', 'Không tìm thấy đơn để cập nhật thanh toán.');
        }

        // 1) check chữ ký
        if (!$vnp_SecureHash || $secureHash !== $vnp_SecureHash) {
            return redirect('/my-orders/' . $orderCode)->with('error', 'Sai chữ ký VNPay (vnp_SecureHash).');
        }

        // 2) check kết quả thanh toán
        if ($respCode === "00" && $txnStatus === "00") {
            DB::table('orders')->where('id', $order->id)->update([
                'payment_method' => 'VNPAY',
                // nếu bạn có cột payment_status thì set paid ở đây
            ]);

            return redirect('/my-orders/' . $orderCode)->with('success', 'Thanh toán VNPay thành công.');
        }

        return redirect('/my-orders/' . $orderCode)->with('error', 'Thanh toán VNPay không thành công hoặc bị hủy.');
    }
}
