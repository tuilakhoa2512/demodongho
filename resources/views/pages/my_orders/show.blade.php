@extends('pages.layout')
@section('content')

<h2 class="title text-center" style="margin-bottom: 20px;">
    CHI TIẾT ĐƠN HÀNG
</h2>

@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom: 15px;">
        {{ session('error') }}
    </div>
@endif

<div style="margin-bottom: 15px;">
    <a href="{{ URL::to('/my-orders') }}" class="btn btn-default btn-back">
        ← Quay lại danh sách đơn hàng
    </a>
</div>

<div class="order-card">
    <div class="order-header">
        <div class="order-title">THÔNG TIN ĐƠN HÀNG</div>
    </div>

    @php
        // Ưu tiên lấy mapping từ controller (statusLabels), nếu không có thì fallback
        $statusMap = $statusLabels ?? [
            'pending'   => 'Đợi xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao',
            'success'   => 'Hoàn thành',
            'canceled'  => 'Đã hủy',
        ];

        $rawStatus = $order->status ?? 'pending';
        $statusVi = $statusMap[$rawStatus] ?? $rawStatus;
    @endphp

    <div class="order-meta">
        <div class="meta-row">
            <span>Mã đơn hàng:</span>
            <strong class="text-red">{{ $order->order_code ?? '' }}</strong>
        </div>
        <div class="meta-row">
            <span>Trạng thái:</span>
            <strong>{{ $statusVi }}</strong>
        </div>
        <div class="meta-row">
            <span>Phương thức:</span>
            <strong>{{ $order->payment_method ?? 'COD' }}</strong>
        </div>
        <div class="meta-row">
            <span>Ngày đặt:</span>
            <strong>
                @if(!empty($order->created_at))
                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                @else
                    —
                @endif
            </strong>
        </div>
    </div>

    <hr>

    <div class="section-title" style="font-weight: bold">Thông Tin Giao Hàng</div>
    <div class="ship-grid">
        <div class="ship-row">
            <span>Người nhận:</span>
            <strong>{{ $order->receiver_name ?? '—' }}</strong>
        </div>
        <div class="ship-row">
            <span>Email:</span>
            <strong class="text-wrap">{{ $order->receiver_email ?? '—' }}</strong>
        </div>
        <div class="ship-row">
            <span>SĐT:</span>
            <strong>{{ $order->receiver_phone ?? '—' }}</strong>
        </div>
        <div class="ship-row">
            <span>Địa chỉ:</span>
            <strong class="text-wrap">{{ $order->receiver_address ?? '—' }}</strong>
        </div>
    </div>

    <hr>

    <div class="section-title" style="font-weight: bold;">Chi Tiết Đặt Hàng</div> <br>

    <div class="table-responsive">
        <table class="table table-bordered myorder-table">
            <thead>
                <tr>
                    <th style="width:90px; text-align:center;">Ảnh</th>
                    <th>Sản phẩm</th>
                    <th style="width:170px; text-align:center;">Đơn giá</th>
                    <th style="width:90px; text-align:center;">SL</th>
                    <th style="width:160px; text-align:right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $calcSubtotal = 0; @endphp

                @forelse($items as $it)
                    @php
                        $qty   = (int)($it->quantity ?? 0);

                        // Giá gốc hiện tại + đơn giá đã chốt trong đơn
                        $base  = (float)($it->base_price ?? 0);
                        $unit  = (float)($it->unit_price ?? 0);

                        // line total dùng đơn giá đã chốt
                        $line  = $qty * $unit;
                        $calcSubtotal += $line;

                        // Nếu đơn giá < giá gốc => có sale
                        $hasSale = ($unit > 0 && $base > 0 && $unit < $base);

                        // Ảnh
                        $img = $it->image ?? null;
                        $imgUrl = $img ? asset('storage/' . $img) : null;
                    @endphp
                    <tr>
                        <td style="text-align:center;">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="product" class="od-thumb">
                            @else
                                <div class="od-thumb od-thumb--empty">No Image</div>
                            @endif
                        </td>

                        <td class="col-name">
                            {{ $it->name ?? 'Sản phẩm' }}
                        </td>

                        <td style="text-align:center;">
                            <div class="od-price">
                                <span class="od-price-sale">
                                    {{ number_format($unit, 0, ',', '.') }} đ
                                </span>
                                @if($hasSale)
                                    <div class="od-price-old">
                                        {{ number_format($base, 0, ',', '.') }} đ
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td style="text-align:center;">
                            x{{ $qty }}
                        </td>

                        <td style="text-align:right;" class="text-red">
                            {{ number_format($line, 0, ',', '.') }} đ
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">Không có sản phẩm trong đơn.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $subtotalShow = isset($subtotal) ? (float)$subtotal : (float)$calcSubtotal;
        $discountShow = isset($discountValue) ? (int)$discountValue : (int)($order->discount_bill_value ?? 0);
        $grandShow = isset($grandTotal) ? (float)$grandTotal : (float)($order->total_price ?? max(0, $subtotalShow - $discountShow));
    @endphp

    <div class="summary-box">
        <div class="sum-row">
            <span>Tạm tính:</span>
            <strong>{{ number_format($subtotalShow, 0, ',', '.') }} đ</strong>
        </div>

        <div class="sum-row discount">
            <span>Ưu đãi hóa đơn</span>
            <strong>-{{ number_format($discountShow, 0, ',', '.') }} đ</strong>
        </div>

        <div class="sum-row total">
            <span>Tổng thanh toán:</span>
            <strong class="text-red">{{ number_format($grandShow, 0, ',', '.') }} đ</strong>
        </div>
    </div>
</div>
<br> <br>
<style>
/* ====== ẨN SIDEBAR CHỈ Ở TRANG NÀY (KHÔNG SỬA LAYOUT) ====== */
.left-sidebar{ display:none !important; }
section > .container > .row > .col-sm-3{ display:none !important; }
section > .container > .row > .col-sm-9.padding-right{
    width: 100% !important;
    float: none !important;
}

/*
    .left-sidebar {
        display: none !important;
    } */
/* Quan trọng: KHÔNG để rộng hơn khung layout */
.order-card, .order-card * { max-width: 100%; box-sizing: border-box; }

/* Button */
.btn-back{
    border:1px solid #e60012 !important;
    color:#e60012 !important;
    background:#fff !important;
}

/* Card */
.order-card{
    background:#fff;
    border:1px solid #eee;
    border-radius:5px;
    padding:16px;
    overflow:hidden; /* chặn tràn */
}

.order-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
}
.order-title{ font-size:16px; font-weight:900; color:#222; }
.order-code{ font-weight:900; color:#e60012; word-break: break-all; }

.text-red{ color:#e60012; font-weight:900; }
.text-wrap{ word-break: break-word; white-space: normal; }

.order-meta .meta-row,
.ship-grid .ship-row{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    padding:6px 0;
    font-size:14px;
}
.order-meta .meta-row span,
.ship-grid .ship-row span{
    color:#555;
    min-width:110px;
}

/* Table: bỏ nowrap để không tràn */
.table-responsive{ overflow-x:auto; }
.myorder-table{ width:100%; margin-bottom: 0; }
.myorder-table thead th{
    background:#e60012;
    color:#fff;
    font-weight:900;
    border-color:#e60012;
}
.myorder-table td{ vertical-align:middle; white-space: normal; }
.myorder-table .col-name{ font-weight:800; }

/* THÊM: ảnh sản phẩm */
.od-thumb{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #eee;
    background:#fff;
}
.od-thumb--empty{
    width:60px;
    height:60px;
    border-radius:8px;
    border:1px dashed #ddd;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    color:#777;
    background:#fafafa;
}

/* THÊM: đơn giá (giá sale + giá gốc gạch ngang) */
.od-price{ line-height: 1.1; }
.od-price-sale{
    color:#e60012;
    font-weight:900;
    font-size:14px;
}
.od-price-old{
    margin-top:4px;
    color:#999;
    text-decoration: line-through;
    font-weight:700;
    font-size:12px;
}

/* Summary */
.summary-box{ border-top:1px dashed #ddd; margin-top:12px; padding-top:12px; }
.sum-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:6px 0;
    font-size:14px;
}

.sum-row strong{ font-weight:900; }
.sum-row.discount{ color:#e60012; }
.sum-row.total{
    margin-top:6px;
    padding-top:10px;
    border-top:1px dashed #ddd;
    font-size:16px;
}

/* Mobile fix: giảm min-width của label để đỡ đẩy tràn */
@media (max-width: 767px){
    .order-meta .meta-row span,
    .ship-grid .ship-row span{ min-width: 90px; }
    section > .container > .row > .col-sm-9.padding-right{
        width: 100% !important;
    }
}
</style>

@endsection
