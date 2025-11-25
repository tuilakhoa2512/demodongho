@extends('pages.layout')
@section('content')

<section id="login-form">
<h2 class="title text-center">Đăng Nhập Tài Khoản</h2>
<div class="container" style="margin-top:50px;">
    <div class="row justify-content-center">
        <div class="col-sm-9" style="padding-right: 15px;">
            <div class="login-form p-4" 
                 style="border: 1px solid #ddd; border-radius: 10px; background-color: #fff; width: 100%;">
                 
                <h2 class="text-center mb-4">Đăng nhập</h2>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ URL::to('/login-user') }}" method="POST">
                    {{ csrf_field() }}

                    <!-- Input full width -->
                    <input type="text" name="email" placeholder="Email" 
                           class="form-control mb-3" style="width: 100%;"/>
                    <input type="password" name="password" placeholder="Mật khẩu" 
                           class="form-control mb-3" style="width: 100%;"/>

                    <div class="d-flex justify-content-between mb-3">
                        <label><input type="checkbox" class="checkbox"> Ghi nhớ đăng nhập</label>
                        <a href="{{ url('/quen-mat-khau') }}">Quên mật khẩu?</a>
                    </div>

                    <!-- Button full width -->
                    <button type="submit" class="btn btn-danger btn-block" style="width: 100%;">Đăng nhập</button>
                </form>

                <div class="mt-4 text-center">
                    <p>Chưa có tài khoản? 
                    <a href="{{ URL::to('/register') }}" style="color:blue; font-weight:bold;">Đăng ký</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

@endsection
