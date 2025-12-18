@extends('pages.admin_layout')
@section('admin_content')

<div class="panel panel-default">
    <div class="panel-heading">
        Thêm người dùng
    </div>

    <div class="panel-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Họ tên</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="address" class="form-control">
            </div>

            <button type="submit" class="btn btn-info">
                Thêm người dùng
            </button>
        </form>
    </div>
</div>

@endsection
