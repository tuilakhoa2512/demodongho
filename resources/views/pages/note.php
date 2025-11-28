@extends('pages.layout')
@section('content')

<section id="profile-form">
    <h2 class="title text-center">Thông Tin Cá Nhân: {{ Session::get('fullname') }}</h2>

    <div class="container" style="margin-top:50px;">
        <div class="row justify-content-center">
            <div class="col-sm-9 d-flex justify-content-center">
                <div class="login-form p-4" 
                     style="border: 1px solid #ddd; border-radius: 10px; background-color: #fff; width: 100%; position: relative;">

                    <!-- Avatar -->
                    @if($user)
                        <div class="profile-image text-center" style="margin-bottom:20px;">
                            @if($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" class="user-image" style="width:150px; height:150px; border-radius:50%; object-fit:cover; border:2px solid #ccc;">
                            @else
                                <img src="{{ asset('backend/images/3.png') }}" alt="User Image" class="user-image" style="width:150px; height:150px; border-radius:50%; object-fit:cover; border:2px solid #ccc;">
                            @endif
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Form cập nhật profile -->
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <label>Họ và tên</label>
                        <input type="text" name="fullname" class="form-control mb-3" value="{{ $user->fullname }}" required />

                        <label>Email (không thể sửa)</label>
                        <input type="email" class="form-control mb-3" value="{{ $user->email }}" disabled />

                        <label>Số điện thoại</label>
                        <input type="text" name="phone" class="form-control mb-3" value="{{ $user->phone }}" />

                        <label>Địa chỉ</label>
                        <input type="text" name="address" class="form-control mb-3" value="{{ $user->address }}" />

                        <label>Tỉnh / Thành phố</label>
                        <input type="text" name="province" class="form-control mb-3" value="{{ $user->province }}" />

                        <label>Quận / Huyện</label>
                        <input type="text" name="district" class="form-control mb-3" value="{{ $user->district }}" />

                        <label>Phường / Xã</label>
                        <input type="text" name="ward" class="form-control mb-3" value="{{ $user->ward }}" />

                        <label>Ảnh đại diện</label>
                        <input type="file" name="image" class="form-control mb-3" />

                        <button class="btn btn-danger btn-block mb-3">Cập nhật</button>
                    </form>

                </div>
                <br> <br>
            </div>
        </div>
    </div>
</section>

<style>
.login-form {
    border: 1px solid #ddd;
    border-radius: 10px;
    background-color: #fff;
    padding: 20px;
    width: 100%;
}

.login-form h2 {
    font-size: 22px;
    margin-bottom: 20px;
    text-align: center;
}

.login-form input.form-control {
    border-radius: 5px;
    padding: 8px 12px;
    width: 130%;
}

.login-form .btn-block {
    display: block;
    width: 130%;
}

.user-image {
    display: inline-block;
}
</style>

@endsection
