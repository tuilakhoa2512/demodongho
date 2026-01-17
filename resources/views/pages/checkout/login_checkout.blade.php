@extends('pages.layout')
@section('content')

<!-- <h2 class="title text-center">Đăng Nhập Tài Khoản</h2> -->
<div class="container" style="margin-top:50px;">
    <div class="row justify-content-center">
    <div class="col-sm-9 d-flex justify-content-center" style="padding-right: 15px;">
            <div class="login-form p-4" 
                 style="border: 1px solid #ddd; border-radius: 10px; background-color: #fff; width: 100%; padding: 93px;">
                 
                <h2 class="title text-center mb-4">Đăng Nhập</h2>
              

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ URL::to('/login-user') }}" method="POST">
                    {{ csrf_field() }}

                    <!-- Input full width -->
                    <label for="">Email:</label>
                    <input type="text" name="email" 
                        placeholder="xxx@gmail.com" 
                        class="form-control mb-3" 
                        style="width: 130%;"
                        pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                        title="Email phải kết thúc bằng @gmail.com"
                        maxlength="30"
                        required>
                           
                    <label for="">Mật Khẩu:</label>
                    <input  type="password"
                            name="password" 
                            placeholder="********" 
                            class="form-control mb-3"
                            style="width: 130%;"
                            maxlength="30"
                            required>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                            <label>
                                <input type="checkbox" class="checkbox" style="width:15px">
                                Ghi nhớ đăng nhập
                            </label>

                            <a href="{{ route('password.forgot') }}"
                            class="forgot-password-link">
                                Quên mật khẩu?
                            </a>
                        </div>
                    
                    <button type="submit" class="btn btn-danger btn-block mb-3" style="width: 130%;">Đăng nhập</button><br>
                    <div class="mt-3">
                        <a class="google-button" href="{{ URL::to('login-user-google') }}">
                            <img class="google-icon" src="{{ asset('frontend/images/google.png') }}" alt="Đăng nhập bằng tài khoản google">
                            Đăng nhập bằng Google
                        </a>
                    </div>
                </form>
<style type="text/css">
    ul.list-login {
        margin: 10px;
        padding: 0;
        text-align: center; 
    }
    .list-login {
        margin: 10px 0;
        text-align: center; 
    }
    ul.list-login li {
        display: inline-block;
        margin: 5px;
    }

    .google-button {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        color: #333;
        width: 130%;
        height: 32px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .google-button:hover {
        background-color: #f8f8f8;
    }

    .google-icon {
        width: 20px; 
        margin-right: 10px;
    }
    /* FIX QUÊN MẬT KHẨU */
.forgot-password-link {
    color: #0d6efd !important;   /* xanh bootstrap */
    font-size: 14px;
    text-decoration: none;
    opacity: 1 !important;
    visibility: visible !important;
}

.forgot-password-link:hover {
    color: #D70018 !important;
    text-decoration: underline;
}

</style>
                
<br>
<div class="mt-3 text-center">
        <p>Chưa có tài khoản? <a href="{{ URL::to('/register') }}" style="color:blue; font-weight:bold;">Đăng ký</a></p>
    </div>
            </div>
        </div>
    </div>
</div>
</section>

@endsection
