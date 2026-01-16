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

<style>
/* ===============================
   RESET PASSWORD FORM
================================ */

.reset-form {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 36px 40px;
    width: 160%;
}

/* Title */
.reset-form .title {
    color: #D70018;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 20px;
}

/* Alert */
.reset-form .alert {
    font-size: 14px;
    margin-bottom: 20px;
}

/* Label nhỏ giống form mật khẩu */
.reset-form label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
    margin-bottom: 6px;
    display: block;
}

/* Input đồng bộ */
.reset-form .form-control {
    height: 46px;
    border-radius: 6px;
    border: 1px solid #dcdcdc;
    font-size: 14px;
    padding: 10px 14px;
    margin-bottom: 16px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

/* Focus */
.reset-form .form-control:focus {
    border-color: #D70018;
    box-shadow: 0 0 0 2px rgba(215, 0, 24, 0.12);
}

/* Button */
.reset-btn {
    display: block;
    width: 260px;              /*  nút ngắn */
    margin: 24px auto 0;       /*  căn giữa */
    height: 46px;

    background-color: #D70018;
    color: #fff;
    font-size: 15px;
    font-weight: 600;

    border: none;
    border-radius: 6px;
    cursor: pointer;

    transition: background-color 0.2s, transform 0.15s;
}

/* Hover */
.reset-btn:hover {
    background-color: #b80015;
    transform: translateY(-1px);
}

/* Active */
.reset-btn:active {
    transform: scale(0.97);
}
</style>

