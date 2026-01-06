@extends('pages.layout')
@section('content')

@php
    $fullname = Session::get('fullname','id');
    $parts = explode(' ', trim($fullname));

    $first = mb_substr($parts[0], 0, 1, 'UTF-8');
    $last  = mb_substr(end($parts), 0, 1, 'UTF-8');
    $initials = strtoupper($first . $last);

    // Random màu
    function randomColor() {
        return sprintf("#%06X"  , mt_rand(0, 0xFFFFFF));
    }
    $bgColor = randomColor();
@endphp
'
'
<h2 class="title text-center" style="margin-bottom: 20px;">
        Thông Tin Cá Nhân: 
         {{ Session::get('fullname') }}
    </h2>
<section id="profile-form">
    

    <div class="container" style="margin-top:50px;">
        <div class="row justify-content-center">
            <div class="col-sm-9 d-flex justify-content-center">

                <div class="login-form p-4" 
                     style="border: 1px solid #ddd; border-radius: 10px; background-color: #fff; width: 100%; position: relative;">

                    {{-- Avatar --}}
                    @if($user)
                        <div class="profile-image text-center" style="margin-bottom:20px;">
                            @if($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}"
                                     style="width:150px; height:150px; border-radius:50%; object-fit:cover; border:2px solid #ccc;">
                            @else
                                <div class="profile-circle"
                                     style="background-color: {{ $bgColor }};
                                     margin: 20px auto; width: 150px; height: 150px;
                                     border-radius: 50%; display: flex; align-items: center;
                                     justify-content: center; color: white; font-size: 55px;
                                     font-weight: bold;">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                    @endif


                    {{-- SUCCESS MESSAGE --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif


                    {{-- FORM --}}
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


                        <button class="btn btn-danger btn-block mb-3">Cập nhật</button><br>
                    </form>

                </div>

            </div>
        </div>
    </div>
</section>

<br><br><br>
<style>
.login-form input.form-control {
    border-radius: 5px;
    padding: 8px 12px;
}
.login-form .form-control {
    width: 130%;
    max-width: 600px;
}
.btn-block {
    width: 130%;
}
.form-group {
        margin-bottom: 15px;
    }
</style>


{{-- province/district/ward --}}
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
</script>

@endsection
