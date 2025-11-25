@extends('pages.layout')
@section('content')

<div class="container" style="margin-top: 40px;">

    <h2 style="margin: 0; position: absolute; top: -30px; left: 50%; transform: translateX(-50%);">Thông Tin Cá Nhân</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($user)
    <div class="profile-image" style="margin: 30px;position: absolute;top: -30px; left: 50%; transform: translateX(-50%);">
        @if($user->image)
            <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" class="user-image">
        @else
            <img src="{{ asset('backend/images/3.png') }}" width="150" height="150"
                style="border-radius: 50%; object-fit: cover; border: 2px solid #ccc; margin-right:20px;" alt="User Image" class="user-image">
        @endif
    </div>
    

    <!-- Các thông tin khác của người dùng -->
@endif
<br><br><br><br><br>
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" value="{{ $user->fullname }}" required style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Email (không thể sửa)</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" class="form-control" value="{{ $user->address }}" style="max-width: 800px;">
                </div>
                
                <div class="form-group">
                    <label>Tỉnh / Thành phố</label>
                    <input type="text" name="province" class="form-control" value="{{ $user->province }}" style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Quận / Huyện</label>
                    <input type="text" name="district" class="form-control" value="{{ $user->district }}" style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Phường / Xã</label>
                    <input type="text" name="ward" class="form-control" value="{{ $user->ward }}" style="max-width: 800px;">
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
@endsection