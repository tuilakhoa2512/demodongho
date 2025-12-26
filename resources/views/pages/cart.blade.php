@extends('pages.layout')

@section('content')

<h2 class="title text-center" style="margin-bottom: 20px;">
    GIỎ HÀNG CỦA BẠN
</h2>

<div class="cart-page">

    {{-- ALERT --}}
    @if(session('success'))
    <div id="cart-alert" 
         style="background:#e60012; color:#fff; padding:12px 16px; border-radius:8px; 
                margin-bottom:16px; font-weight:500; font-size:15px; text-align:center;">
        {{ session('success') }}
    </div>
        <script>
        setTimeout(() => {
            const alertBox = document.getElementById('cart-alert');
            if (alertBox) {
                alertBox.style.opacity = '0';
                alertBox.style.transform = 'translateY(-10px)';
                alertBox.style.transition = '0.5s';
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 1800);
        </script>
    @endif

    {{-- EMPTY --}}
    @if(empty($cart))
        <div class="order-card">
            <div class="empty-box">
                Hiện chưa có sản phẩm nào trong giỏ hàng.
                <div style="margin-top:10px;">
                    <a href="{{ url('/trang-chu') }}" class="btn btn-default btn-back">Tiếp tục mua sắm</a>
                </div>
            </div>
        </div>
    @else

    <form action="{{ route('cart.update') }}" method="POST">
        @csrf

        <div class="order-card">
            <div class="order-header">
                <div class="order-title">Danh Sách Sản Phẩm</div>
                <div class="order-sub">
                    @php $count = is_countable($cart ?? null) ? count($cart) : 0; @endphp
                    {{ $count }} sản phẩm
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered myorder-table">
                    <thead>
                        <tr>
                            <th style="width:90px; text-align:center;">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th style="width:170px; text-align:center;">Đơn giá</th>
                            <th style="width:160px; text-align:center;">Số lượng</th>
                            <th style="width:170px; text-align:right;">Tạm tính</th>
                            <th style="width:90px; text-align:center;">Xóa</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($cart as $item)
                        @php
                            $maxQty = max(1, (int)$item['max_qty']);
                            $qty = max(1, min((int)$item['quantity'], $maxQty));
                        @endphp

                        <tr>
                            {{-- IMAGE --}}
                            <td style="text-align:center;">
                                <img src="{{ $item['image'] }}" class="od-thumb" alt="product">
                            </td>

                            {{-- PRODUCT --}}
                            <td class="col-name text-wrap" style="font-weight:800;">
                                {{ $item['name'] }}
                            </td>

                            {{-- PRICE --}}
                            <td style="text-align:center;">
                                <div class="od-price">
                                    <span class="od-price-sale">
                                        {{ number_format($item['final_price'],0,',','.') }} đ
                                    </span>
                                    @if($item['has_sale'])
                                        <div class="od-price-old">
                                            {{ number_format($item['base_price'],0,',','.') }} đ
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- QTY --}}
                            <td style="text-align:center;">
                                <div class="qty-control" data-max="{{ $maxQty }}">
                                    <button type="button" class="qty-btn qty-minus">−</button>
                                    <input type="text"
                                           readonly
                                           name="quantities[{{ $item['id'] }}]"
                                           value="{{ $qty }}"
                                           class="cart-qty-input">
                                    <button type="button" class="qty-btn qty-plus">+</button>
                                </div>
                                <div class="cart-stock-note">Tồn: {{ $maxQty }}</div>
                            </td>

                            {{-- LINE TOTAL --}}
                            <td style="text-align:right;" class="text-red">
                                {{ number_format($item['line_total'],0,',','.') }} đ
                            </td>

                            {{-- REMOVE --}}
                            <td style="text-align:center;">
                                <button type="submit"
                                        class="btn-remove"
                                        formaction="{{ route('cart.remove') }}"
                                        name="product_id"
                                        value="{{ $item['id'] }}">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY --}}
            <div class="summary-box" style="margin-top:12px;">
                <div class="sum-row">
                    <span>Tạm tính:</span>
                    <strong>{{ number_format($subtotal,0,',','.') }} đ</strong>
                </div>

                <div class="sum-row discount">
                    <span>
                        @if($billDiscount)
                            Ưu Đãi Hóa Đơn ({{ $billDiscount->name }} - {{ $billDiscount->rate }}%)
                        @else
                            Ưu Đãi Hóa Đơn
                        @endif
                    </span>
                    <strong>-{{ number_format($billDiscountAmount,0,',','.') }} đ</strong>
                </div>

                <div class="sum-row total">
                    <span>Tổng thanh toán:</span>
                    <strong class="text-red">{{ number_format($grandTotal,0,',','.') }} đ</strong>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="cart-actions">
                <a href="{{ url('/trang-chu') }}" class="btn btn-default btn-back">Tiếp tục mua sắm</a>

                <button type="submit" class="btn btn-warning btn-update">
                    Cập nhật
                </button>

                @if(Session::get('id'))
                    <a href="{{ url('/payment') }}" class="btn btn-danger btn-checkout">Thanh toán</a>
                @else
                    <a href="{{ url('/login-checkout') }}" class="btn btn-danger btn-checkout">Thanh toán</a>
                @endif
            </div>
        </div>
    </form>
    @endif
</div>

{{-- JS +/- --}}
<script>
document.querySelectorAll('.qty-control').forEach(c => {
    const input = c.querySelector('input');
    const max = parseInt(c.dataset.max);

    c.querySelector('.qty-minus').onclick = () => {
        input.value = Math.max(1, input.value - 1);
    };
    c.querySelector('.qty-plus').onclick = () => {
        input.value = Math.min(max, parseInt(input.value) + 1);
    };
});
</script>

<style>
/* Page */
.cart-page { padding: 10px 0 40px; }

/* Đồng bộ style khung giống Lịch sử đơn hàng */
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

/* Table giống my_orders */
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

/* Ảnh */
.od-thumb{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #eee;
    background:#fff;
}

/* Đơn giá (giá sale + giá gốc) */
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

/* Qty control */
.qty-control{
    display:inline-flex;
    align-items:center;
    border:1px solid #ccc;
    border-radius:8px;
    overflow:hidden;
}
.qty-btn{
    width:34px;
    height:34px;
    border:none;
    background:#f5f5f5;
    font-size:18px;
    font-weight:700;
    cursor:pointer;
    transition:0.2s;
}
.qty-btn:hover{
    background:#e60012;
    color:#fff;
}
.cart-qty-input{
    width:46px;
    height:34px;
    text-align:center;
    border:none;
    font-weight:800;
    font-size:15px;
    background:#fff;
}
.cart-qty-input:focus{ outline:none; }

.cart-stock-note{
    font-size: 12px;
    color:#777;
    margin-top: 4px;
}

/* Summary giống show */
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

/* Buttons */
.btn-back{
    border:1px solid #e60012 !important;
    color:#e60012 !important;
    background:#fff !important;
    font-weight:800;
    border-radius:6px;
}

.btn-update{
    font-weight:800;
    border-radius:6px;
}

.btn-checkout{
    background:#e60012 !important;
    border-color:#e60012 !important;
    color:#fff !important;
    font-weight:800;
    border-radius:6px;
}
.btn-checkout:hover{
    background:#b80010 !important;
    border-color:#b80010 !important;
}

/* Nút xóa */
.btn-remove{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:13px;
    font-weight:800;
    color:#fff;
    background:#e60012;
    padding:6px 12px;
    border-radius:6px;
    border:none;
    cursor:pointer;
    transition:0.2s ease;
}
.btn-remove:hover{ background:#b80010; }

/* Action row */
.cart-actions{
    margin-top:14px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    justify-content:flex-end;
}

/* Empty box */
.empty-box{
    padding:18px;
    border:1px dashed #ddd;
    border-radius:10px;
    text-align:center;
    color:#555;
    background:#fff;
}

/* Mobile */
@media (max-width: 767px){
    .order-header{ flex-direction:column; }
    .cart-actions{ justify-content:stretch; }
    .cart-actions .btn, .cart-actions a{
        width:100%;
        text-align:center;
    }
}
</style>

@endsection
