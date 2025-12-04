@extends('pages.layout')
@section('content')
@php
    $fullname = Session::get('fullname');
    $parts = explode(' ', trim($fullname));

    // Lấy ký tự đầu họ (phần tử đầu)
    $first = mb_substr($parts[0], 0, 1, 'UTF-8');

    // Lấy ký tự đầu của tên cuối (phần tử cuối)
    $last = mb_substr(end($parts), 0, 1, 'UTF-8');

    $initials = strtoupper($first . $last);
@endphp
<div class="container" style="margin-top: 40px;">

    <h2 style="margin: 0; position: absolute; top: -30px; left: 50%; transform: translateX(-50%);">
        Thông Tin Cá Nhân : 
        <!-- {{ Session::get('fullname') }} -->
        {{ $initials }}
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($user)
        <div class="profile-image"
             style="margin: 30px;position: absolute;top: -30px; left: 50%; transform: translateX(-50%);">
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" class="user-image">
            @else
                <img src="{{ asset('backend/images/3.png') }}" width="150" height="150"
                     style="border-radius: 50%; object-fit: cover; border: 2px solid #ccc; margin-right:20px;"
                     alt="User Image" class="user-image">
            @endif
        </div>
    @endif

    

    <br><br><br><br><br>


    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" class="form-control"
                           value="{{ $user->fullname }}" required style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Email (không thể sửa)</label>
                    <input type="email" class="form-control"
                           value="{{ $user->email }}" disabled style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ $user->phone }}" style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Địa chỉ (số nhà, đường)</label>
                    <input type="text" name="address" class="form-control"
                           value="{{ $user->address }}" style="max-width: 800px;">
                </div>


                {{-- PROVINCE --}}
                <div class="form-group">
                    <label for="province">Tỉnh / Thành phố</label>
                    <select name="province_id" id="province" class="form-control" style="max-width: 800px;">
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
                <div class="form-group">
                    <label for="district">Quận / Huyện</label>
                    <select name="district_id" id="district" class="form-control" style="max-width: 800px;">
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
                <div class="form-group">
                    <label for="ward">Phường / Xã</label>
                    <select name="ward_id" id="ward" class="form-control" style="max-width: 800px;">
                        <option value="">-- Chọn Phường / Xã --</option>

                        @foreach ($wards as $w)
                            <option value="{{ $w->id }}"
                                {{ $w->id == $user->ward_id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach

                    </select>
                </div>



                <div class="form-group">
                    <label>Ảnh đại diện</label>
                    <input type="file" name="image" class="form-control" style="max-width: 800px;">
                </div>

                <button class="btn btn-primary">Cập nhật</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const province = document.getElementById('province');
    const district = document.getElementById('district');
    const ward     = document.getElementById('ward');

    // Khi chọn tỉnh -> load huyện
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

    // Khi chọn huyện -> load xã
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
</script>

@endsection
