<?php
$id = Session::get('id');

// Lấy thông tin người dùng từ session
$image = Session::get('images'); // Trả về giá trị của thuộc tính 'image'
$fullname = Session::get('fullname');
?>

@if($id != null)
    <li class="dropdown user-menu">
        <a href="#" class="dropdown-toggle">
            <div class="profile-image">
                @if($image)
                    <img src="{{ asset('uploads/users/'.$image) }}" alt="User Image" class="user-image">
                @else
                    <img src="{{ asset('frontend/images/default_user.png') }}" alt="User Image" class="user-image">
                @endif
                <span>{{ $fullname }}</span> <!-- Hiển thị tên đầy đủ -->
            </div>
        </a>

        <ul class="dropdown-menu user-dropdown">
            <li><a href="{{ URL::to('/profile') }}"><i class="fa fa-info-circle"></i> Thông tin cá nhân</a></li>
            <li><a href="{{ URL::to('/my-orders') }}"><i class="fa fa-list-alt"></i> Đơn hàng</a></li>
            <li><a href="{{ URL::to('/logout-checkout') }}"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
        </ul>
    </li>
@else
    <li><a href="{{ URL::to('/login-checkout') }}"><i class="fa fa-lock"></i> Đăng Nhập</a></li>
@endif