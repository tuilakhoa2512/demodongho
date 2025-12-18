@extends('pages.layout')

@section('content')

<div class="cart-page">
    <h2 class="title text-center">Giỏ Hàng Của Bạn</h2>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="cart-alert">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                const el = document.querySelector('.cart-alert');
                if (!el) return;
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                el.style.transition = '0.4s';
                setTimeout(() => el.remove(), 400);
            }, 1600);
        </script>
    @endif

    {{-- EMPTY CART --}}
    @if(empty($cart) || count($cart) === 0)
        <p class="cart-empty">Hiện chưa có sản phẩm nào trong giỏ hàng.</p>
    @else

    <form action="{{ route('cart.update') }}" method="POST">
        @csrf

        {{-- TABLE --}}
        <div class="cart-table-wrapper">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Tạm tính</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($cart as $item)
                        @php
                            $maxQty = max(1, (int) ($item['max_qty'] ?? 1));
                            $qty    = max(1, min((int) ($item['quantity'] ?? 1), $maxQty));

                            $finalPrice = (float) ($item['final_price'] ?? ($item['price'] ?? 0));
                            $basePrice  = (float) ($item['base_price'] ?? $finalPrice);
                            $hasSale    = !empty($item['has_sale']) && $basePrice > $finalPrice;
                            $lineTotal  = (float) ($item['line_total'] ?? ($finalPrice * $qty));
                        @endphp

                        <tr>
                            {{-- PRODUCT --}}
                            <td class="cart-product">
                                <div class="cart-product-box">
                                    <div class="cart-product-img">
                                        <img src="{{ $item['image'] ?? asset('frontend/images/noimage.jpg') }}"
                                             alt="{{ $item['name'] ?? 'Sản phẩm' }}">
                                    </div>
                                    <div class="cart-product-name">
                                        {{ $item['name'] ?? '—' }}
                                    </div>
                                </div>
                            </td>

                            {{-- PRICE --}}
                            <td class="cart-price">
                                <div class="cart-price-main">
                                    {{ number_format($finalPrice, 0, ',', '.') }} đ
                                </div>

                                @if($hasSale)
                                    <div class="cart-price-old">
                                        {{ number_format($basePrice, 0, ',', '.') }} đ
                                    </div>
                                @endif
                            </td>

                            {{-- QUANTITY ( - / + ) --}}
                            <td class="cart-qty">
                                <div class="qty-control"
                                     data-max="{{ $maxQty }}"
                                     data-product="{{ $item['id'] ?? '' }}">
                                    <button type="button" class="qty-btn qty-minus">−</button>

                                    <input
                                        type="text"
                                        readonly
                                        name="quantities[{{ $item['id'] }}]"
                                        value="{{ $qty }}"
                                        class="cart-qty-input js-cart-qty"
                                        data-old="{{ $qty }}"
                                    >

                                    <button type="button" class="qty-btn qty-plus">+</button>
                                </div>

                                <div class="cart-stock-note">
                                    Tồn: {{ $maxQty }}
                                </div>
                            </td>

                            {{-- LINE TOTAL --}}
                            <td class="cart-subtotal">
                                {{ number_format($lineTotal, 0, ',', '.') }} đ
                            </td>

                            {{-- REMOVE --}}
                            <td class="cart-remove">
                                <button
                                    type="submit"
                                    class="cart-remove-btn"
                                    formaction="{{ route('cart.remove') }}"
                                    formmethod="POST"
                                    name="product_id"
                                    value="{{ $item['id'] }}"
                                >
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            $subtotal = $subtotal ?? 0;
            $billDiscountAmount = $billDiscountAmount ?? 0;
            $grandTotal = $grandTotal ?? $subtotal;
        @endphp

        {{-- SUMMARY --}}
        <div class="cart-footer">
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
                </div>

                @if(!empty($billDiscount))
                    <div class="summary-row discount">
                        <span>
                            Ưu Đãi Hóa Đơn ({{ $billDiscount->name }} giảm {{ $billDiscount->rate }}%)
                        </span>
                        <strong>
                            -{{ number_format($billDiscountAmount, 0, ',', '.') }} đ
                        </strong>
                    </div>
                @endif

                <div class="summary-row total">
                    <span>Tổng thanh toán:</span>
                    <strong>{{ number_format($grandTotal, 0, ',', '.') }} đ</strong>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="cart-actions">
                <a href="{{ url('/trang-chu') }}" class="btn btn-default cart-continue">
                    Tiếp tục mua sắm
                </a>

                <button type="submit" class="btn cart-update">
                    Cập nhật giỏ hàng
                </button>

                @if(Session::get('id'))
                    <a href="{{ url('/payment') }}" class="btn cart-checkout">
                        Thanh toán
                    </a>
                @else
                    <a href="{{ url('/login-checkout') }}" class="btn cart-checkout">
                        Thanh toán
                    </a>
                @endif
            </div>
        </div>

    </form>
    @endif
</div>

{{-- JS: +/- quantity --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.qty-control').forEach(control => {
        const input = control.querySelector('.cart-qty-input');
        const minus = control.querySelector('.qty-minus');
        const plus  = control.querySelector('.qty-plus');
        const maxQty = parseInt(control.dataset.max) || 1;

        const setVal = (val) => {
            val = Math.max(1, Math.min(val, maxQty));
            input.value = val;
            input.dispatchEvent(new Event('change'));
        };

        minus.addEventListener('click', () => setVal((parseInt(input.value) || 1) - 1));
        plus.addEventListener('click',  () => setVal((parseInt(input.value) || 1) + 1));
    });
});
</script>

<style>
/* ===== ALERT ===== */
.cart-alert{
    background:#e60012;
    color:#fff;
    padding:12px 16px;
    border-radius:8px;
    margin: 10px 0 16px;
    font-weight:500;
    font-size:15px;
    text-align:center;
}

/* ===== PAGE ===== */
.cart-page { padding: 20px 0 40px; }
.cart-empty { font-size: 15px; margin-top: 10px; }

/* ===== TABLE ===== */

.cart-table-wrapper { width: 100%; overflow-x: auto; margin-bottom: 20px; }
.cart-table { width: 100%; border-collapse: collapse; font-size: 14px; background: #fff; }
.cart-table th, .cart-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
    text-align: left;
    white-space: nowrap;
}
.cart-table th {
    color: #fff;
    background-color: #e60012;
    font-weight: 600;
}

/* ===== PRODUCT CELL ===== */
.cart-product { white-space: normal; }
.cart-product-box { display: flex; align-items: center; gap: 10px; }
.cart-product-img {
    width: 60px; height: 60px; border-radius: 6px; overflow: hidden; flex-shrink: 0;
}
.cart-product-img img { width: 100%; height: 100%; object-fit: cover; }
.cart-product-name { font-weight: 600; line-height: 1.3; }

/* ===== PRICE ===== */
.cart-price-main {
    font-size: 16px;
    font-weight: 800;
    color: #e60012;
    line-height: 1.2;
}
.cart-price-old {
    margin-top: 3px;
    font-size: 13px;
    color: #999;
    text-decoration: line-through;
}

/* ===== QTY CONTROL (-/+) ===== */
.qty-control{
    display:inline-flex;
    align-items:center;
    border:1px solid #ccc;
    border-radius:8px;
    overflow:hidden;
}
.qty-btn{
    width:36px;
    height:36px;
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
    width:48px;
    height:36px;
    text-align:center;
    border:none;
    font-weight:700;
    font-size:15px;
    background:#fff;
}
.cart-qty-input:focus{ outline:none; }

.cart-stock-note{
    font-size: 12px;
    color:#777;
    margin-top: 4px;
}

/* ===== LINE TOTAL ===== */
.cart-subtotal { font-weight: 700; }

/* ===== FOOTER ===== */
.cart-footer{
    display:flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items:flex-start;
    gap: 12px;
    margin-top: 10px;
}

/* ===== SUMMARY BOX ===== */
.cart-summary{
    background:#fff;
    border:1px solid #eee;
    border-radius: 10px;
    padding: 12px 14px;
    min-width: 320px;
}
.summary-row{
    display:flex;
    justify-content: space-between;
    align-items:center;
    gap: 12px;
    padding: 6px 0;
    font-size: 14px;
}
.summary-row strong{ font-weight: 800; }
.summary-row.discount{ color:#e60012; }
.summary-row.total{
    border-top:1px dashed #ddd;
    margin-top: 4px;
    padding-top: 10px;
    font-size: 16px;
}
.summary-row.total strong{ color:#e60012; font-size: 18px; }

/* ===== ACTIONS ===== */
.cart-actions .btn{ margin-left: 6px; }
.cart-continue{
    background:#fff;
    border:1px solid #e60012;
    color:#e60012;
    border-radius:4px;
}
.cart-update{
    background:#f0ad4e;
    border-color:#f0ad4e;
    color:#fff;
    border-radius:4px;
}

.cart-update:hover{
    background:#d19b4d;
    border-color:#f0ad4e;
    color:#fff;
    border-radius:4px;
}
.cart-checkout{
    background:#e60012;
    border-color:#e60012;
    color:#fff;
    border-radius:4px;
}
.cart-checkout:hover{ background:#b80010; border-color:#b80010; color:#fff; }

/* ===== REMOVE BTN ===== */
.cart-remove-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
    font-weight:700;
    color:#fff;
    background:#e60012;
    padding:6px 14px;
    border-radius:4px;
    border:none;
    cursor:pointer;
    transition:0.2s ease;
}
.cart-remove-btn:hover{ background:#b80010; }

/* ===== MOBILE ===== */
@media (max-width: 767px) {
    .cart-footer{ flex-direction: column; align-items: stretch; }
    .cart-summary{ width:100%; min-width: unset; }
    .cart-actions{ width:100%; }
    .cart-actions .btn{ width:100%; margin: 6px 0 0 0; text-align:center; }
}
</style>

@endsection
