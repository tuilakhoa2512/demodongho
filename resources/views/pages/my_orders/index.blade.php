@extends('pages.layout')
@section('content')

@php
    $statusLabels = [
        'pending'   => 'Đợi xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao',
        'success'   => 'Hoàn thành',
        'canceled'  => 'Đã hủy',
    ];

    // Badge class theo trạng thái
    function statusBadgeClass($st) {
        return match($st) {
            'confirmed' => 'badge-confirmed',
            'shipping'  => 'badge-shipping',
            'success'   => 'badge-success',
            'canceled'  => 'badge-cancel',
            default     => 'badge-pending',
        };
    }
@endphp

<h2 class="title text-center" style="margin-bottom: 20px;">
    LỊCH SỬ ĐƠN HÀNG
</h2>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 15px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom: 15px;">
        {{ session('error') }}
    </div>
@endif

<div class="order-card">
    <div class="order-header">
        <div class="order-title">Danh Sách Đơn Hàng</div>
        <div class="order-sub">
            @php $count = is_countable($orders ?? null) ? count($orders) : 0; @endphp
            {{ $count }} đơn
        </div>
    </div>

    @if(empty($orders) || count($orders) === 0)
        <div class="empty-box">
            Bạn chưa có đơn hàng nào.
            <div style="margin-top:10px;">
                <a href="{{ URL::to('/trang-chu') }}" class="btn btn-default btn-back">Tiếp tục mua sắm</a>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered myorder-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th style="width:140px; text-align:center;">Trạng thái</th>
                        <th style="width:120px; text-align:center;">Thanh toán</th>
                        <th style="width:170px; text-align:center;">Tổng tiền</th>
                        <th style="width:170px; text-align:center;">Ngày đặt</th>
                        <th style="width:110px; text-align:center;">Chi tiết</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $o)
                        @php
                            $code = $o->order_code ?? '';
                            $status = $o->status ?? 'pending';
                            $method = $o->payment_method ?? 'COD';
                            $total  = (float)($o->total_price ?? 0);

                            $created = !empty($o->created_at)
                                ? \Carbon\Carbon::parse($o->created_at)->format('d/m/Y H:i')
                                : '—';

                            $statusText = $statusLabels[$status] ?? $status;
                            $badgeClass = statusBadgeClass($status);
                        @endphp

                        <tr>
                            <td class="col-code text-wrap">
                                {{ $code }}
                            </td>

                            <td style="text-align:center;">
                                <span class="status-badge {{ $badgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>

                            <td style="text-align:center;">
                                <span class="method-pill">{{ $method }}</span>
                            </td>

                            <td style="text-align:center;" class="text-red">
                                {{ number_format($total, 0, ',', '.') }} đ
                            </td>

                            <td style="text-align:center;">
                                {{ $created }}
                            </td>

                            <td style="text-align:center;">
                                {{-- Không gọi route theo yêu cầu --}}
                                <a class="btn btn-xs btn-detail"
                                   href="{{ URL::to('/my-orders/' . $code) }}">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="note-box">
            * Trạng thái đơn hàng sẽ được cập nhật bởi quản trị viên.
        <br>
            * Nếu có thắc mắc hoặc vấn đề về đơn hàng, vui lòng liên hệ qua Hotline: 0983 567 891
        </div>
    @endif
</div> <br> <br> <br>

<style>
/* body > section > .container > .row > .col-sm-3 (left)
    body > section > .container > .row > .col-sm-9.padding-right (content)
*/
body > section > .container > .row > .col-sm-3{
    display: none !important;
}
body > section > .container > .row > .col-sm-9.padding-right{
    width: 90% !important;
    float: none !important;        
    margin: 0 auto !important;      
    display: block !important;
    
}
@media (min-width: 768px){
    body > section > .container > .row > .col-sm-9.padding-right{
        float: none !important;
    }
}

/* ====== CSS hiện tại của bạn ====== */
/* Chống tràn layout */
.order-card, .order-card * { max-width: 100%; box-sizing: border-box; }
.text-wrap{ word-break: break-word; white-space: normal; }
.text-red{ color:#e60012; font-weight:900; }

.order-card{
    background:#fff;
    border:1px solid #eee;
    border-radius:5px;
    padding:16px;
    overflow:hidden;
}

.order-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
    margin-bottom: 10px;
}
.order-title{ font-size:16px; font-weight:900; color:#222; }
.order-sub{ color:#777; font-weight:700; }

.table-responsive{ overflow-x:auto; }

/* Table đồng bộ */
.myorder-table{ width:100%; margin-bottom:0; }
.myorder-table thead th{
    background:#e60012;
    color:#fff;
    font-weight:900;
    border-color:#e60012;
    vertical-align:middle;
}
.myorder-table td{
    vertical-align:middle;
    white-space: normal;
}
.col-code{ font-weight:900; }

/* Badge trạng thái */
.status-badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    border:1px solid #eee;
}

/* Pending */
.badge-pending{ background:#fff5f5; color:#e60012; border-color:#ffd2d2; }
/* Confirmed */
.badge-confirmed{ background:#eaf6ff; color:#0a66c2; border-color:#b9d7ff; }
/* Shipping */
.badge-shipping{ background:#f0f7ff; color:#1f66d1; border-color:#b9d7ff; }
/* Success */
.badge-success{ background:#f0fff4; color:#0f8a3c; border-color:#b8f1c9; }
/* Canceled */
.badge-cancel{ background:#f7f7f7; color:#555; border-color:#e0e0e0; }

/* Method pill */
.method-pill{
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    background:#f7f7f7;
    color:#333;
    border:1px solid #e6e6e6;
}

/* Button */
.btn-detail{
    background:#e60012 !important;
    border-color:#e60012 !important;
    color:#fff !important;
    font-weight:800;
    padding:6px 10px;
    border-radius:6px;
}
.btn-detail:hover{
    background:#b80010 !important;
    border-color:#b80010 !important;
    color:#fff !important;
}

/* Empty + note */
.empty-box{
    padding:18px;
    border:1px dashed #ddd;
    border-radius:10px;
    text-align:center;
    color:#555;
    background:#fff;
}
.note-box{
    margin-top:12px;
    padding-top:12px;
    border-top:1px dashed #ddd;
    color:#777;
    font-size:13px;
}

.btn-back{
    border:1px solid #e60012 !important;
    color:#e60012 !important;
    background:#fff !important;
}

/* Mobile */
@media (max-width: 767px){
    .order-header{ flex-direction:column; }
}
</style>

@endsection
