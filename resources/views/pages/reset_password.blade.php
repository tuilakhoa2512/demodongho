@extends('pages.layout')
@section('content')

<div class="container" style="margin-top:50px;">
    <div class="row justify-content-center">
        <div class="col-sm-9 col-md-7 col-lg-6">
            <div class="reset-form">

                <h2 class="title text-center mb-4">ĐẶT LẠI MẬT KHẨU</h2>

                @if (session('success'))
                    <div class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger text-center">
                        @foreach ($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="form-group">
                        <label>Mã OTP</label>
                        <input type="text"
                               name="otp"
                               class="form-control"
                               placeholder="Nhập OTP 6 số"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password"
                               name="password"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Xác nhận mật khẩu</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               required>
                    </div>

                    <button type="submit" class="reset-btn">
                        Cập nhật mật khẩu
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
