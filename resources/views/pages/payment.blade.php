@extends('pages.layout')

@section('content')
<h2 class="title text-center">Thông Tin Thanh Toán</h2>

<div class="payment-page">

    {{-- ERROR / SUCCESS --}}
    @if(session('error'))
        <div class="pay-alert pay-alert-error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="pay-alert pay-alert-error">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payment.place') }}" method="POST">
        @csrf

        <div class="row">
            {{-- LEFT: SHIPPING INFO --}}
            <div class="col-sm-7">
                <div class="pay-box">
                    <h3 class="pay-box-title" style="text-align:center;">Thông Tin Giao Hàng</h3>

                    <div class="form-group">
                        <label>Họ và tên người nhận *</label>
                        <input type="text" name="receiver_name" class="form-control"
                               value="{{ old('receiver_name', $user->fullname ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="receiver_email" class="form-control"
                               value="{{ old('receiver_email', $user->email ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại *</label>
                        <input type="text" name="receiver_phone" class="form-control"
                               value="{{ old('receiver_phone', $user->phone ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ (số nhà, đường) *</label>
                        <input type="text" name="receiver_address" class="form-control"
                               value="{{ old('receiver_address', $user->address ?? '') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tỉnh/Thành</label>
                                <select name="province_id" id="province" class="form-control">
                                    <option value="">-- Chọn Tỉnh --</option>
                                    @foreach($provinces as $p)
                                        <option value="{{ $p->id }}"
                                            {{ (string)old('province_id', $user->province_id ?? '') === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Quận/Huyện</label>
                                <select name="district_id" id="district" class="form-control">
                                    <option value="">-- Chọn Huyện --</option>
                                    @foreach($districts as $d)
                                        <option value="{{ $d->id }}"
                                            {{ (string)old('district_id', $user->district_id ?? '') === (string)$d->id ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Phường/Xã</label>
                                <select name="ward_id" id="ward" class="form-control">
                                    <option value="">-- Chọn Xã --</option>
                                    @foreach($wards as $w)
                                        <option value="{{ $w->id }}"
                                            {{ (string)old('ward_id', $user->ward_id ?? '') === (string)$w->id ? 'selected' : '' }}>
                                            {{ $w->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- PAYMENT METHOD --}}
                    <h3 class="pay-box-title" style="text-align:center;">Phương Thức Thanh Toán</h3>
                    <div class="form-group">
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="COD" {{ old('payment_method') === 'COD' ? 'selected' : '' }}>
                                Thanh toán khi nhận hàng (COD)
                            </option>
                            <option value="VNPAY" {{ old('payment_method') === 'VNPAY' ? 'selected' : '' }}>
                                Chuyển khoản ngân hàng (VNPay)
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- RIGHT: ORDER SUMMARY + PROMO CODE --}}
            <div class="col-sm-5">
                <div class="pay-box">
                    <h3 class="pay-box-title" style="text-align:center;">Đơn Hàng Của Bạn</h3>

                    {{-- PROMO CODE (moved to right) --}}
                    <div class="promo-wrap">
                        <label style="font-weight:800; margin-bottom:6px; display:block;">Mã giảm giá</label>

                        <div class="promo-row">
                            <input type="text"
                                   name="promo_code"
                                   id="promo_code"
                                   class="form-control"
                                   placeholder="VD: TET2026"
                                   value="{{ old('promo_code', $promoCode ?? '') }}">

                            <button type="button" class="btn promo-btn" id="btn_apply_promo">
                                Áp dụng
                            </button>
                        </div>

                        <div id="promo_feedback" class="promo-feedback" style="display:none;"></div>

                        <small style="display:block; margin-top:6px; color:#777; font-weight:600;">
                            * Bấm “Áp dụng” để kiểm tra mã trước khi đặt hàng.
                        </small>
                    </div>

                    <hr style="margin:12px 0;">

                    <div class="pay-items">
                        @foreach($cart as $it)
                            <div class="pay-item">
                                <div class="pay-item-name">
                                    {{ $it['name'] }}
                                    <span class="pay-item-qty">x {{ $it['quantity'] }}</span>
                                </div>
                                <div class="pay-item-price">
                                    {{ number_format($it['line_total'], 0, ',', '.') }} đ
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $appliedCode = old('promo_code', $promoCode ?? '');
                    @endphp

                    <div class="pay-summary">
                        <div class="sum-row">
                            <span>Tạm tính</span>
                            <strong id="subtotal_text">{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
                        </div>

                        <div class="sum-row sum-discount">
                            <span>
                                Ưu đãi hóa đơn
                                <span id="applied_code"
                                      style="color:#555; font-weight:800; {{ !empty($appliedCode) ? '' : 'display:none;' }}">
                                    (Code: {{ $appliedCode }})
                                </span>
                                <span id="bill_discount_name" style="color:#555; font-weight:800; {{ !empty($billDiscount) ? '' : 'display:none;' }}">
                                    - {{ $billDiscount->name ?? '' }}
                                </span>
                            </span>
                            <strong id="discount_text">-{{ number_format($billDiscountAmount ?? 0, 0, ',', '.') }} đ</strong>
                        </div>

                        <div class="sum-row sum-total">
                            <span>Tổng thanh toán</span>
                            <strong id="total_text">{{ number_format($grandTotal, 0, ',', '.') }} đ</strong>
                        </div>
                    </div>

                    {{-- COD BUTTON --}}
                    <button type="submit" id="btn_place_order" class="btn pay-btn">
                        Xác Nhận Đặt Hàng
                    </button>

                    {{-- VNPAY BUTTON --}}
                    <button type="submit" id="btn_vnpay" class="btn pay-btn" style="display:none;">
                        Thanh toán VNPay
                    </button>

                    <a href="{{ route('cart.index') }}" class="pay-back">
                        ← Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- TOGGLE BUTTONS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('payment_method');
    const btnOrder = document.getElementById('btn_place_order');
    const btnVNPay = document.getElementById('btn_vnpay');

    function togglePayButtons() {
        if (select.value === 'VNPAY') {
            btnVNPay.style.display = 'block';
            btnOrder.style.display = 'none';
        } else {
            btnVNPay.style.display = 'none';
            btnOrder.style.display = 'block';
        }
    }

    select.addEventListener('change', togglePayButtons);
    togglePayButtons();
});
</script>

{{-- APPLY PROMO (AJAX) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn_apply_promo');
    const input = document.getElementById('promo_code');
    const feedback = document.getElementById('promo_feedback');

    const subtotalText = document.getElementById('subtotal_text');
    const discountText = document.getElementById('discount_text');
    const totalText = document.getElementById('total_text');

    const appliedCode = document.getElementById('applied_code');
    const billName = document.getElementById('bill_discount_name');

    function fmtVND(n) {
        try { return (Number(n) || 0).toLocaleString('vi-VN') + ' đ'; }
        catch(e){ return (Number(n) || 0) + ' đ'; }
    }

    function showMsg(ok, msg) {
        feedback.style.display = 'block';
        feedback.className = 'promo-feedback ' + (ok ? 'promo-ok' : 'promo-err');
        feedback.textContent = msg || '';
    }

    async function applyPromo() {
        btn.disabled = true;
        btn.textContent = 'Đang kiểm tra...';

        const code = (input.value || '').trim();

        try {
            const res = await fetch("{{ route('payment.applyPromo') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ promo_code: code })
            });

            const data = await res.json().catch(() => null);

            if (!res.ok || !data) {
                showMsg(false, (data && data.message) ? data.message : 'Không thể kiểm tra mã lúc này.');
                return;
            }

            // Update money UI
            subtotalText.textContent = fmtVND(data.subtotal);
            discountText.textContent = '-' + fmtVND(data.discount_amount);
            totalText.textContent = fmtVND(data.total);

            // Update code label (only show when code exists + server says has_code)
            if (data.promo_code && data.has_code) {
                appliedCode.style.display = 'inline';
                appliedCode.textContent = '(Code: ' + data.promo_code + ')';
            } else {
                appliedCode.style.display = 'none';
                appliedCode.textContent = '';
            }

            // Optional: promo name (if server returns)
            if (data.promo_name) {
                billName.style.display = 'inline';
                billName.textContent = '- ' + data.promo_name;
            } else {
                billName.style.display = 'none';
                billName.textContent = '';
            }

            showMsg(true, data.message || 'Đã cập nhật ưu đãi.');

        } catch (e) {
            showMsg(false, 'Lỗi mạng hoặc server.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Áp dụng';
        }
    }

    btn.addEventListener('click', applyPromo);

    // Enter to apply
    input.addEventListener('keydown', function(e){
        if (e.key === 'Enter') {
            e.preventDefault();
            applyPromo();
        }
    });
});
</script>

{{-- AJAX Province/District/Ward (GIỮ NGUYÊN) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const province = document.getElementById('province');
    const district = document.getElementById('district');
    const ward     = document.getElementById('ward');

    province.addEventListener('change', function() {
        const pid = this.value;
        district.innerHTML = '<option value="">-- Chọn Huyện --</option>';
        ward.innerHTML = '<option value="">-- Chọn Xã --</option>';
        if (!pid) return;

        fetch(`/location/districts/${pid}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(d => {
                    district.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                });
            });
    });

    district.addEventListener('change', function() {
        const did = this.value;
        ward.innerHTML = '<option value="">-- Chọn Xã --</option>';
        if (!did) return;

        fetch(`/location/wards/${did}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(w => {
                    ward.innerHTML += `<option value="${w.id}">${w.name}</option>`;
                });
            });
    });
});
</script>

<style>
.payment-page{ padding: 20px 0 40px; }

.pay-alert{
    padding: 12px 16px;
    border-radius: 8px;
    margin: 10px 0 16px;
    font-size: 15px;
}
.pay-alert-error{
    background:#e60012;
    color:#fff;
}

.pay-box{
    background:#fff;
    border:1px solid #eee;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 12px;
}
.pay-box-title{
    font-size: 18px;
    font-weight: 800;
    margin: 0 0 12px;
}
.form-group{ margin-bottom: 12px; }
.form-control{ border-radius: 8px; }

.pay-items{
    border-top:1px dashed #eee;
    padding-top: 10px;
    margin-top: 6px;
}
.pay-item{
    display:flex;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f3f3f3;
}
.pay-item-name{ font-weight: 600; }
.pay-item-qty{ color:#777; font-weight: 700; margin-left: 6px; }
.pay-item-price{ font-weight: 800; color:#e60012; }

.pay-summary{ margin-top: 12px; }
.sum-row{
    display:flex;
    justify-content: space-between;
    align-items:center;
    padding: 8px 0;
}
.sum-discount{ color:#e60012; font-weight: 700; }
.sum-total{
    border-top:1px dashed #ddd;
    margin-top: 6px;
    padding-top: 12px;
    font-size: 16px;
}
.sum-total strong{ color:#e60012; font-size: 20px; }

.pay-btn{
    width: 100%;
    background:#e60012;
    border:1px solid #e60012;
    color:#fff;
    font-weight: 800;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 12px;
}
.pay-btn:hover{
    background:#b80010;
    border-color:#b80010;
    color:#fff;
}
.pay-back{
    display:block;
    text-align:center;
    margin-top: 10px;
    color:#333;
    font-weight: 600;
}
.pay-back:hover{ color:#e60012; }

/* PROMO UI */
.promo-row{
    display:flex;
    gap:10px;
    align-items:center;
}
.promo-row .form-control{ flex: 1; }

.promo-btn{
    background:#111;
    border:1px solid #111;
    color:#fff;
    font-weight:800;
    border-radius:8px;
    padding:10px 14px;
    white-space:nowrap;
}
.promo-btn:hover{ opacity:0.9; color:#fff; }

.promo-feedback{
    margin-top:10px;
    padding:10px 12px;
    border-radius:8px;
    font-weight:800;
    display:none;
}
.promo-ok{
    background:#eafff1;
    border:1px solid #b7f0cc;
    color:#0f6b2f;
}
.promo-err{
    background:#ffefef;
    border:1px solid #ffd1d1;
    color:#b80010;
}
</style>

@endsection
