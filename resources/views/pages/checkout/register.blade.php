@extends('pages.layout')
@section('content')


<div class="container" style="margin-top:50px;">
    <div class="row justify-content-center">

        <div class="col-sm-9 d-flex justify-content-center" style="padding-right: 15px;">
            <div class="login-form p-4"
                 style="border: 1px solid #ddd; border-radius: 10px; background-color:#fff; width:100%;">

                <h2 class="title text-center mb-4">Đăng ký</h2>

                @if ($errors->any())
                    <div class="alert alert-danger text-center">
                        @foreach ($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ URL::to('/add-user') }}" method="POST">
                    {{ csrf_field() }}

                    <label>Họ và tên:</label>
                    <input type="text" name="fullname" placeholder="Nguyễn Văn A"
                           class="form-control mb-3" style="width:130%;" required>

                    <label>Email:</label>
                    <input type="email" name="email" placeholder="xxx@gmail.com"
                           class="form-control mb-3" style="width:130%;" required>

                    <label>Mật khẩu:</label>
                    <input type="password" name="password" placeholder="********"
                           class="form-control mb-3" style="width:130%;" required>

                    <label>Số điện thoại:</label>
                    <input type="text" name="phone" placeholder="0123456789"
                           class="form-control mb-3" style="width:130%;" required>
                           

                    <button type="submit" class="btn btn-danger btn-block" style="width:130%;">Đăng ký</button>
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
    
</style>
                <div class="mt-4 text-center">
                    <p>Đã có tài khoản?
                        <a href="{{ URL::to('/login-checkout') }}" style="color:blue; font-weight:bold;">Đăng nhập</a>
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
