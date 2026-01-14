@extends('pages.layout')

@section('content')

@php

    $method = strtoupper((string)($order->payment_method ?? 'COD'));
    $status = (string)($order->status ?? 'pending');

    $isVNPay = ($method === 'VNPAY');
    $isCanceled = ($status === 'canceled');

    // Map trạng thái hiển thị cho khách
    $statusLabels = [
        'pending'   => 'Đợi Xác Nhận',
        'confirmed' => 'Đã Xác Nhận',
        'shipping'  => 'Đang Giao',
        'success'   => 'Hoàn Thành',
        'canceled'  => 'Đã Hủy',
    ];

    $statusText = $statusLabels[$status] ?? $status;

    // Tiêu đề theo trạng thái + phương thức
    if ($isCanceled) {
        $pageTitle = $isVNPay
            ? 'Thanh toán VNPay không thành công'
            : 'Đơn hàng đã bị hủy';
    } else {
        $pageTitle = $isVNPay
            ? 'Thanh toán VNPay thành công'
            : 'Đặt hàng thành công';
    }

    // Hiển thị phương thức thanh toán
    $methodText = $isVNPay ? 'VNPay (Chuyển khoản)' : 'COD (Thanh toán khi nhận hàng)';

    // Promo info (ưu tiên DB)
    $promoCode = $promoCode ?? ($order->promo_code ?? null);
    $promoName = $promoName ?? ($order->promo_campaign_name ?? null);

    // Số tiền giảm
    $discountValue = (int)($discountValue ?? 0);

@endphp


<h2 class="title text-center">{{ $pageTitle }}</h2>

<div class="pay-success-page">

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="pay-success-alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="pay-error-alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="pay-success-box">

        <div class="pay-success-row">
            <span>Mã đơn hàng:</span>
            <strong style="color:#e60012;">{{ $order->order_code }}</strong>
        </div>

        <div class="pay-success-row">
            <span>Trạng thái:</span>
            <strong class="{{ $isCanceled ? 'text-danger' : '' }}">
                 {{ $statusText }}
            </strong>
        </div>

        <div class="pay-success-row">
            <span>Phương thức:</span>
            <strong>{{ $methodText }}</strong>
        </div>

        <hr>

        <h3 class="pay-success-title">Thông tin giao hàng</h3>

        <div class="pay-success-row">
            <span>Người nhận:</span>
            <strong>{{ $order->receiver_name }}</strong>
        </div>
        <div class="pay-success-row">
            <span>Email:</span>
            <strong>{{ $order->receiver_email }}</strong>
        </div>
        <div class="pay-success-row">
            <span>SĐT:</span>
            <strong>{{ $order->receiver_phone }}</strong>
        </div>
        <div class="pay-success-row" style="align-items:flex-start;">
            <span>Địa chỉ:</span>
            <strong style="text-align:right;">
                {{ $order->receiver_address }}
            </strong>
        </div>

        <hr>

        <h3 class="pay-success-title">Chi tiết đơn hàng</h3>

        <div class="pay-success-items">
            @foreach($items as $it)
                @php $line = (float)$it->price * (int)$it->quantity; @endphp
                <div class="pay-success-item">
                    <div class="item-name">
                        {{ $it->name }}
                        <span class="item-qty">x {{ $it->quantity }}</span>
                    </div>
                    <div class="item-price">
                        {{ number_format($line, 0, ',', '.') }} đ
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pay-success-summary">
            <div class="sum-row">
                <span>Tạm tính</span>
                <strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
            </div>

            {{-- ✅ NEW: Ưu đãi hệ Promotion (đọc từ DB nếu có) --}}
            <div class="sum-row sum-discount">
                <span>
                    Ưu đãi hóa đơn
                    @if(!empty($promoCode))
                        <span style="color:#555; font-weight:900;">(Code: {{ $promoCode }})</span>
                    @endif
                    @if(!empty($promoName))
                        <span style="color:#555; font-weight:900;">- {{ $promoName }}</span>
                    @endif
                </span>
                <strong>-{{ number_format($discountValue, 0, ',', '.') }} đ</strong>
            </div>

            <div class="sum-row sum-total">
                <span>Tổng thanh toán</span>
                <strong>{{ number_format($grandTotal, 0, ',', '.') }} đ</strong>
            </div>
        </div>

        <div class="pay-success-actions">
            <a href="{{ url('/trang-chu') }}" class="btn btn-default">
                Tiếp tục mua sắm
            </a>

            <a href="{{ url('/my-orders') }}" class="btn btn-default">
                Đơn hàng của tôi
            </a>
        </div>
    </div>
</div>

<style>
.pay-success-page{ padding: 20px 0 40px; }

.pay-success-alert{
    background:#e60012;
    color:#fff;
    padding:12px 16px;
    border-radius:10px;
    margin: 10px 0 16px;
    font-weight:600;
    text-align:center;
}
.pay-error-alert{
    background:#ffefef;
    color:#b80010;
    border:1px solid #ffd1d1;
    padding:12px 16px;
    border-radius:10px;
    margin: 10px 0 16px;
    font-weight:700;
    text-align:center;
}

.pay-success-box{
    background:#fff;
    border:1px solid #eee;
    border-radius:12px;
    padding: 14px 16px;
}

.pay-success-title{
    font-size: 18px;
    font-weight: 800;
    margin: 0 0 12px;
}

.pay-success-row{
    display:flex;
    justify-content: space-between;
    align-items:center;
    gap: 12px;
    padding: 6px 0;
}

.pay-success-items{
    border-top:1px dashed #eee;
    padding-top: 10px;
    margin-top: 6px;
}

.pay-success-item{
    display:flex;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f3f3f3;
}

.item-name{ font-weight: 700; }
.item-qty{ color:#777; font-weight: 800; margin-left: 6px; }
.item-price{ font-weight: 900; color:#e60012; }

.pay-success-summary{ margin-top: 12px; }
.sum-row{
    display:flex;
    justify-content: space-between;
    align-items:center;
    padding: 8px 0;
}
.sum-discount{ color:#e60012; font-weight: 800; }
.sum-total{
    border-top:1px dashed #ddd;
    margin-top: 6px;
    padding-top: 12px;
    font-size: 16px;
}
.sum-total strong{ color:#e60012; font-size: 20px; }

.pay-success-actions{
    margin-top: 14px;
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
}

.text-danger{ color:#b80010; font-weight:900; }
</style>

@endsection
