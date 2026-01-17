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
    <div id="cart-empty" style="{{ empty($cart) ? '' : 'display:none' }}">
        <div class="order-card">
            <div class="empty-box">
                Hiện chưa có sản phẩm nào trong giỏ hàng.
                <div style="margin-top:10px;">
                    <a href="{{ url('/trang-chu') }}" class="btn btn-default btn-back">Tiếp tục mua sắm</a>
                </div>
            </div>
        </div>
    </div>

        @php
            // đảm bảo không null
            $subtotal = (float) ($subtotal ?? 0);
            $billDiscountAmount = (float) ($billDiscountAmount ?? 0);
            $grandTotal = (float) ($grandTotal ?? $subtotal);

            // billDiscount là Promotion|null (order scope)
            $orderPromoName = $billDiscount ? ($billDiscount->name ?? null) : null;

            // promoCode từ session
            $promoCode = $promoCode ?? null;

            $count = is_countable($cart ?? null) ? count($cart) : 0;
        @endphp

    <div id="cart-box" style="{{ empty($cart) ? 'display:none' : '' }}">
        <form action="{{ route('cart.update') }}" method="POST">
            @csrf

            <div class="order-card">

                <div class="order-header">
                    <div class="order-title">Danh Sách Sản Phẩm</div>
                    <div class="order-sub">{{ $count }} sản phẩm</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered myorder-table">
                        <thead>
                        <tr>
                            <th style="width:90px; text-align:center;">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th style="width:150px; text-align:center;">Đơn giá</th>
                            <th style="width:160px; text-align:center;">Số lượng</th>
                            <th style="width:150px; text-align:center;">Tạm tính</th>
                            <th style="width:90px; text-align:center;">Xóa</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($cart as $item)
                            @php
                                // chuẩn hoá dữ liệu
                                $id = (int) ($item['id'] ?? 0);
                                $name = $item['name'] ?? 'Sản phẩm';
                                $image = $item['image'] ?? asset('frontend/images/noimage.jpg');

                                $basePrice  = (float) ($item['base_price'] ?? 0);
                                $finalPrice = (float) ($item['final_price'] ?? $basePrice);

                                // chỉ coi là giảm khi final < base
                                $hasDiscount = ($finalPrice > 0) && ($finalPrice < $basePrice);

                                $maxQty = max(1, (int)($item['max_qty'] ?? 1));
                                $qty = max(1, min((int)($item['quantity'] ?? 1), $maxQty));

                                // line_total ưu tiên controller tính sẵn, nhưng vẫn fallback tính lại cho chắc
                                $lineTotal = isset($item['line_total'])
                                    ? (float)$item['line_total']
                                    : ($finalPrice * $qty);
                            @endphp

                            <tr id="row-{{ $id }}">
                                <!-- <td style="text-align:center;">
                                    <a href="{{ url('/product/'.$id) }}" class="text-decoration-none">
                                        <img src="{{ $image }}" class="od-thumb" alt="product">
                                    </a>
                                </td> -->

                                
                                <td style="text-align:center;">
                                    <img src="{{ $image }}" class="od-thumb" alt="product">
                                </td>

                            
                               <td class="col-name text-wrap" style="font-weight:800;">
                                        {{ $name }}
                                </td>

                                <!-- <a href="{{ url('/product/'.$id) }}" style="color:#000; text-decoration:none;">
                                    </a> -->


                                <td style="text-align:center;">
                                    <div class="od-price">
                                        <span class="od-price-sale">
                                            {{ number_format($finalPrice, 0, ',', '.') }} đ
                                        </span>

                                        @if($hasDiscount)
                                            <div class="od-price-old">
                                                {{ number_format($basePrice, 0, ',', '.') }} đ
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
                                            name="quantities[{{ $id }}]"
                                            value="{{ $qty }}"
                                            class="cart-qty-input"
                                            id="qty-{{ $id }}"
                                            data-product="{{ $id }}">

                                        <button type="button" class="qty-btn qty-plus">+</button>
                                    </div>

                                    <div class="cart-stock-note">Tồn: {{ $maxQty }}</div>
                                </td>

                                {{-- LINE TOTAL --}}
                                <td style="text-align:center;" class="text-red" id="line-total-{{ $id }}">
                                    {{ number_format($lineTotal, 0, ',', '.') }} đ
                                </td>

                                {{-- REMOVE --}}
                                <td style="text-align:center;">
                                    <button type="button"
                                            class="btn-remove"
                                            data-product="{{ $id }}">
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
                        <strong id="sum-subtotal">{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
                    </div>

                    {{-- ORDER PROMO + CODE (new system) --}}
                    <div class="sum-row discount">
                        <span>
                            Ưu đãi hóa đơn
                            @if(!empty($promoCode))
                                <span style="color:#555; font-weight:800;">(Code: {{ $promoCode }})</span>
                            @endif

                            @if(!empty($orderPromoName))
                                <span style="color:#555; font-weight:800;">- {{ $orderPromoName }}</span>
                            @endif
                        </span>

                        <strong id="sum-discount">
                            @if($billDiscountAmount > 0)
                                -{{ number_format($billDiscountAmount, 0, ',', '.') }} đ
                            @else
                                0 đ
                            @endif
                        </strong>
                    </div>

                    <div class="sum-row total">
                        <span>Tổng Thanh Toán:</span>
                        <strong class="text-red" id="sum-total">{{ number_format($grandTotal, 0, ',', '.') }} đ</strong>
                    </div>

                </div>

                {{-- ACTIONS --}}
                <div class="cart-actions">
                    <a href="{{ url('/trang-chu') }}" class="btn btn-default btn-back">Tiếp tục mua sắm</a>

                    <!-- <button type="submit" class="btn btn-warning btn-update">
                        Cập nhật
                    </button> -->

                    @if(Session::get('id'))
                        <a href="{{ url('/payment') }}" class="btn btn-danger btn-checkout">Thanh toán</a>
                    @else
                        <a href="{{ url('/login-checkout') }}" class="btn btn-danger btn-checkout">Thanh toán</a>
                    @endif
                </div>

            </div>
        </form>
    </div>
</div>

<script>
const csrf = "{{ csrf_token() }}";

// ===== AJAX =====

function updateCartAjax(productId, qty) {
    fetch("{{ route('cart.update') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            quantities: { [productId]: qty }
        })
    })
    .then(res => res.json())
    .then(data => renderCart(data))
    .catch(err => console.error('Update cart error:', err));
}

function removeCartAjax(productId) {
    fetch("{{ route('cart.remove') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            "Accept": "application/json"
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        const row = document.getElementById('row-' + productId);
        if (row) row.remove();
        renderCart(data);
    })
    .catch(err => console.error('Remove cart error:', err));
}

function renderCart(data) {

    // ===== UPDATE ROWS =====
    Object.entries(data.cart).forEach(([id, row]) => {
        const qtyEl = document.getElementById('qty-' + id);
        const totalEl = document.getElementById('line-total-' + id);

        if (qtyEl) qtyEl.value = row.quantity;
        if (totalEl) {
            totalEl.innerText =
                new Intl.NumberFormat('vi-VN').format(row.line_total) + ' đ';
        }
    });

    // ===== CART EMPTY TOGGLE =====
    const cartBox = document.getElementById('cart-box');
    const cartEmpty = document.getElementById('cart-empty');

    if (data.count === 0) {
        if (cartBox) cartBox.style.display = 'none';
        if (cartEmpty) cartEmpty.style.display = 'block';
    } else {
        if (cartBox) cartBox.style.display = 'block';
        if (cartEmpty) cartEmpty.style.display = 'none';
    }

    // ===== BADGE =====
    const badge = document.getElementById('cart-count');
    if (badge) {
        if (data.count > 0) {
            badge.innerText = '(' + data.count + ')';
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    // ===== SUMMARY =====
    document.getElementById('sum-subtotal').innerText =
        new Intl.NumberFormat('vi-VN').format(data.subtotal) + ' đ';

    document.getElementById('sum-discount').innerText =
        data.billDiscountAmount > 0
            ? '-' + new Intl.NumberFormat('vi-VN').format(data.billDiscountAmount) + ' đ'
            : '0 đ';

    document.getElementById('sum-total').innerText =
        new Intl.NumberFormat('vi-VN').format(data.total) + ' đ';
}


// ===== QTY EVENTS =====

document.querySelectorAll('.qty-control').forEach(c => {
    const input = c.querySelector('input');
    const max = parseInt(c.dataset.max || '1', 10);
    const pid = input.dataset.product;

    const getVal = () => {
        const v = parseInt(input.value || '1', 10);
        return isNaN(v) ? 1 : v;
    };

    c.querySelector('.qty-minus').onclick = () => {
        const v = Math.max(1, getVal() - 1);
        input.value = v;
        updateCartAjax(pid, v);
    };

    c.querySelector('.qty-plus').onclick = () => {
        const v = Math.min(max, getVal() + 1);
        input.value = v;
        updateCartAjax(pid, v);
    };
});

// ===== REMOVE EVENTS =====

document.querySelectorAll('.btn-remove').forEach(btn => {
    btn.onclick = () => {
        const pid = btn.dataset.product;
        removeCartAjax(pid);
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

/* Đơn giá */
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
