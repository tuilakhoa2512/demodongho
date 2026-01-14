@extends('pages.layout')
@section('content')

@php
    $fullname = Session::get('fullname','');
    $parts = explode(' ', trim($fullname));
    $initials = strtoupper(
        mb_substr($parts[0] ?? '',0,1,'UTF-8') .
        mb_substr(end($parts) ?? '',0,1,'UTF-8')
    );
@endphp

@php
    $email = $user->email ?? Session::get('email');

    // Ví dụ: khoaneios2002@gmail.com → kho***@gmail.com
    if ($email) {
        [$name, $domain] = explode('@', $email);
        $maskedEmail = substr($name, 0, 3) . str_repeat('*', max(strlen($name) - 3, 0)) . '@' . $domain;
    } else {
        $maskedEmail = '';
    }
@endphp

<h2 class="title text-center mb-4">
    THÔNG TIN CÁ NHÂN: {{ $fullname }}
</h2>

<div class="container">
    <div class="row">

        {{-- MENU BÊN TRÁI (thay sidebar) --}}
        <div class="col-md-3">
            <div class="profile-menu">
                <button class="btn-profile active" onclick="showTab('info', this)">
                    Thông tin cá nhân
                </button>
                <button class="btn-profile btn-outline" onclick="showTab('password', this)">
                    Mật khẩu
                </button>
            </div>
        </div>

        {{-- NỘI DUNG --}}
        <div class="col-md-9">

            {{-- TAB THÔNG TIN --}}
            <div id="tab-info" class="profile-tab-content">

                <div class="profile-box">

                    {{-- Avatar --}}
                    <div class="text-center mb-4">
                        @if($user->image)
                            <img src="{{ asset('storage/'.$user->image) }}"
                                 class="avatar-img">
                        @else
                            <div class="avatar-circle">{{ $initials }}</div>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <label>Họ và tên</label>
                        <input type="text" name="fullname" class="form-control mb-3"
                               value="{{ $user->fullname }}"
                               maxlength="30"
                               required />

                        <label>Email (không thể sửa)</label>
                        <input type="email" class="form-control mb-3"
                               value="{{ $user->email }}" disabled />

                               <label>Số điện thoại</label>
                                <input type="text"
                                    name="phone"
                                    class="form-control mb-3"
                                    value="{{ $user->phone }}"
                                    pattern="[0-9]{10}"
                                    maxlength="10"
                                    inputmode="numeric"
                                    title="Số điện thoại phải gồm 10 chữ số" 
                                    required>

                        <label>Địa chỉ (Số nhà, đường)</label>
                        <input type="text" name="address" class="form-control mb-3"
                               value="{{ $user->address }}"
                               maxlength="50" />

                        <div class="form-group mb-3">
                        <label for="province">Tỉnh / Thành phố</label>
                        <select name="province_id" id="province" class="form-control mb-3">
                            <option value="">-- Chọn Tỉnh / Thành phố --</option>
                            @foreach ($provinces as $p)
                                <option value="{{ $p->id }}"
                                    {{ $p->id == $user->province_id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                         </div>

                        {{-- DISTRICT --}}
                        <div class="form-group mb-3">
                        <label for="district">Quận / Huyện</label>
                        <select name="district_id" id="district" class="form-control mb-3">
                            <option value="">-- Chọn Quận / Huyện --</option>
                            @foreach ($districts as $d)
                                <option value="{{ $d->id }}"
                                    {{ $d->id == $user->district_id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>

                        {{-- WARD --}}
                        <div class="form-group mb-3">
                        <label for="ward">Phường / Xã</label>
                        <select name="ward_id" id="ward" class="form-control mb-3">
                            <option value="">-- Chọn Phường / Xã --</option>
                            @foreach ($wards as $w)
                                <option value="{{ $w->id }}"
                                    {{ $w->id == $user->ward_id ? 'selected' : '' }}>
                                    {{ $w->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>


                        <label>Ảnh đại diện</label>
                        <input type="file" name="image" class="form-control mb-3" />

                        <button class="btn-save">Cập nhật</button>
                    </form>
                </div>
            </div>

            
{{-- TAB ĐỔI MẬT KHẨU --}}
<div id="tab-password" class="profile-tab-content" style="display:none">
    <div class="profile-box">

        @if(session('password_error'))
            <div class="alert alert-danger">{{ session('password_error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('profile.changePassword') }}" method="POST">
            @csrf

            {{-- EMAIL HIỂN THỊ (ĐÃ CHE) --}}
            <label>Email</label>
            <input type="text"
                class="form-control mb-3"
                value="{{ $maskedEmail }}"
                disabled>

            {{-- EMAIL THẬT --}}
            <input type="hidden" name="email" value="{{ $email }}"><br>

            {{-- MẬT KHẨU CŨ --}}
            <label>Mật khẩu hiện tại</label>
            <input type="password"
                name="current_password"
                class="form-control mb-3"
                required>

            {{-- MẬT KHẨU MỚI --}}
            <label>Mật khẩu mới</label>
            <input type="password"
                name="new_password"
                class="form-control mb-3"
                required>

            {{-- XÁC NHẬN --}}
            <label>Xác nhận mật khẩu mới</label>
            <input type="password"
                name="new_password_confirmation"
                class="form-control mb-3"
                required>

            <button class="btn-save">Đổi mật khẩu</button> 
        </form>

    </div>
</div>


        </div>
    </div>
</div>

{{-- CSS --}}
<style>
/* Ẩn sidebar cũ */
.left-sidebar,.category-products,.brands_products,.price-range{
    display:none!important;
}

/* Ẩn sidebar cũ */
.left-sidebar,
.category-products,
.brands_products,
.price-range {
    display: none !important;
}

/* ===== MENU TÀI KHOẢN (GIỐNG DANH MỤC) ===== */
.profile-menu {
    border: 1px solid #eee;
    padding: 20px;
    background: #fff;
}

/* CHA */
.profile-menu::before {
    content: "TÀI KHOẢN";
    display: block;
    text-align: center;
    font-weight: 700;
    color: #D70018;
    margin-bottom: 15px;
    text-transform: uppercase;
}

/* CON */
.btn-profile {
    display: block;
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 8px;

    background: #fff;
    color: #555;

    border: none;
    border-radius: 0;

    text-align: left;
    font-weight: 500;
    cursor: pointer;

    transition: all 0.2s ease;
}

/* Hover giống DANH MỤC */
.btn-profile:hover {
    background: #D70018;
    color: #fff;
}

/* Active */
.btn-profile.active {
    background: #D70018;
    color: #fff;
}

/* ===== PROFILE BOX ===== */
.profile-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 30px;
}

/* ===== AVATAR ===== */
.avatar-img {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-circle {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: #999;
    color: #fff;
    font-size: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
}

/* ===== NÚT LƯU ===== */
.btn-save {
    width: 100%;
    background: #D70018;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 12px;
    font-weight: 600;
    cursor: pointer;
}

.btn-save:hover {
    background: #b80015;
}

body > section > .container > .row > .col-sm-9.padding-right{
    width: 90% !important;
    float: none !important;         /* bỏ float của bootstrap */
    margin: 0 auto !important;      /* căn giữa */
    display: block !important;
    
}

/* ===============================
   FORM ĐỔI MẬT KHẨU
================================ */

#tab-password .profile-box {
    max-width: 720px;
    margin: 0 auto;
    padding: 32px 36px;
}

/* Label giống login */
#tab-password label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
    margin-bottom: 6px;
    display: block;
}

/* Input đẹp, cao */
#tab-password .form-control {
    height: 46px;
    border-radius: 6px;
    border: 1px solid #dcdcdc;
    font-size: 14px;
    padding: 10px 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

/* Focus input */
#tab-password .form-control:focus {
    border-color: #D70018;
    box-shadow: 0 0 0 2px rgba(215, 0, 24, 0.12);
}

/* Khoảng cách giữa các field */
#tab-password .form-control + label,
#tab-password label + .form-control {
    margin-top: 14px;
}

/* Button đổi mật khẩu */
#tab-password .btn-save {
    margin-top: 22px;
    height: 48px;
    width: 100%;
    background-color: #D70018;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.15s;
}

/* Hover button */
#tab-password .btn-save:hover {
    background-color: #b80015;
    transform: translateY(-1px);
}

/* Active */
#tab-password .btn-save:active {
    transform: scale(0.98);
}

/* Alert đẹp gọn */
#tab-password .alert {
    font-size: 14px;
    margin-bottom: 20px;
}

/* ===============================
   FORM THÔNG TIN CÁ NHÂN (TAB INFO)
   label nhỏ giống TAB MẬT KHẨU
================================ */

#tab-info label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
    margin-bottom: 6px;
    display: block;
}

/* Input cao – gọn – đồng bộ */
#tab-info .form-control {
    height: 46px;
    border-radius: 6px;
    border: 1px solid #dcdcdc;
    font-size: 14px;
    padding: 10px 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

/* Focus input */
#tab-info .form-control:focus {
    border-color: #D70018;
    box-shadow: 0 0 0 2px rgba(215, 0, 24, 0.12);
}

/* Khoảng cách giữa label & input */
#tab-info label + .form-control {
    margin-bottom: 16px;
}

/* Select cũng đồng bộ chiều cao */
#tab-info select.form-control {
    height: 46px;
}

/* File input gọn */
#tab-info input[type="file"].form-control {
    height: auto;
    padding: 6px;
}

/* Button cập nhật giống đổi mật khẩu */
#tab-info .btn-save {
    margin-top: 20px;
    height: 48px;
    font-size: 15px;
    font-weight: 600;
}
/* ===============================
   BUTTON NGẮN + CĂN GIỮA
================================ */

/* Nút trong tab đổi mật khẩu */
#tab-password .btn-save {
    width: 260px;          /* 👈 độ dài nút */
    max-width: 100%;
    margin: 24px auto 0;   /* 👈 căn giữa */
    display: block;

    height: 46px;
    background-color: #D70018;
    color: #fff;
    font-size: 15px;
    font-weight: 600;

    border-radius: 6px;
    border: none;
    cursor: pointer;
}

/* Nút trong tab thông tin cá nhân */
#tab-info .btn-save {
    width: 260px;          /* 👈 cùng kích thước */
    max-width: 100%;
    margin: 24px auto 0;
    display: block;

    height: 46px;
    background-color: #D70018;
    color: #fff;
    font-size: 15px;
    font-weight: 600;

    border-radius: 6px;
    border: none;
    cursor: pointer;
}

/* Hover */
#tab-password .btn-save:hover,
#tab-info .btn-save:hover {
    background-color: #b80015;
}

/* Click */
#tab-password .btn-save:active,
#tab-info .btn-save:active {
    transform: scale(0.97);
}


</style>

{{-- JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

const province = document.getElementById('province');
const district = document.getElementById('district');
const ward     = document.getElementById('ward');

province.addEventListener('change', function() {
    const pid = this.value;
    district.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
    ward.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

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
    ward.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

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

//Chuyển Tap
function showTab(tab, el){
    document.getElementById('tab-info').style.display='none';
    document.getElementById('tab-password').style.display='none';
    document.getElementById('tab-'+tab).style.display='block';

    document.querySelectorAll('.btn-profile')
        .forEach(b=>b.classList.remove('active'));

    el.classList.add('active');
}

</script>

<br><br><br>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Giữ tab sau submit
    const activeTab = "{{ session('active_tab') }}";

    if (activeTab === 'password') {
        showTab('password', document.querySelector('.btn-profile:nth-child(2)'));
    } else {
        showTab('info', document.querySelector('.btn-profile:nth-child(1)'));
    }

});
</script>

@endsection
