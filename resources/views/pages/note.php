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

                    <form action="{{ route('profile.changePassword') }}" method="POST">
                        @csrf

                        <label>Email</label>
                        <input class="form-control mb-3"
                               name="email"
                               value="{{ $user->email }}" required>

                        <label>Mật khẩu mới</label>
                        <input type="password" class="form-control mb-3"
                               name="new_password" required>

                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control mb-3"
                               name="new_password_confirmation" required>

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

.profile-menu{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.btn-profile{
    padding:12px;
    border-radius:6px;
    border:2px solid #D70018;
    background:#D70018;
    color:#fff;
    font-weight:600;
    cursor:pointer;
}

.btn-profile.btn-outline{
    background:#fff;
    color:#D70018;
}

.btn-profile.active{
    background:#D70018;
    color:#fff;
}

.profile-box{
    background:#fff;
    border:1px solid #ddd;
    border-radius:10px;
    padding:30px;
}

.avatar-img{
    width:140px;height:140px;
    border-radius:50%;
    object-fit:cover;
}

.avatar-circle{
    width:140px;height:140px;
    border-radius:50%;
    background:#999;
    color:#fff;
    font-size:50px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
}

.btn-save{
    width:100%;
    background:#D70018;
    color:#fff;
    border:none;
    border-radius:6px;
    padding:12px;
    font-weight:600;
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

@endsection
