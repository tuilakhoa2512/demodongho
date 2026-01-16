@extends('pages.layout')
@section('content')

<div class="container" style="margin-top:50px;">
    <div class="row justify-content-center">

        <div class="col-sm-12 col-md-8 col-lg-6">

            <div class="forgot-form-box">

                <h3 class="text-center mb-4 title-forgot">
                    Quên mật khẩu
                </h3>

                {{-- Thông báo --}}
                @if(session('success'))
                    <div class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger text-center">
                        @foreach($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.sendOtp') }}">
                    @csrf

                    <div class="form-group">
                        <label>Email đã đăng ký</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="xxx@gmail.com"
                               required>
                    </div>

                    <button type="submit" class="forgot-btn">
                        Gửi mã OTP
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ url('/login-checkout') }}" class="back-login">
                        ← Quay lại đăng nhập
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
/* ===== BOX ===== */
.forgot-form-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 170px 80px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    width: 165%;
}
/* ===== TITLE ===== */
.title-forgot {
    color: #D70018;
    font-weight: 600;
}

/* ===== FORM GROUP ===== */
.forgot-form-box .form-group {
    max-width: 420px;
    margin: 0 auto 22px;
}

/* ===== INPUT ===== */
.forgot-form-box .form-control {
    height: 42px;
    border-radius: 5px;
}

/* ===== BUTTON ===== */
.forgot-btn {
    display: block;
    max-width: 420px;
    width: 100%;
    margin: 0 auto;

    background-color: #D70018;
    color: #fff;
    border: none;
    border-radius: 6px;

    height: 44px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;

    transition: background-color .2s ease, transform .15s ease;
}

.forgot-btn:hover {
    background-color: #b80015;
    transform: translateY(-1px);
}

.forgot-btn:active {
    transform: scale(0.98);
}

/* ===== BACK LOGIN ===== */
.back-login {
    color: #007bff;
    font-weight: 500;
    text-decoration: none;
}

.back-login:hover {
    text-decoration: underline;
}
body > section > .container > .row > .col-sm-9.padding-left{
    width: 90% !important;
    float: none !important;         /* bỏ float của bootstrap */
    margin: 0 auto !important;      /* căn giữa */
    display: block !important;
    
}
</style>

@endsection
