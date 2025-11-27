@extends('pages.layout')
@section('content')

<section id="register-form">
<h2 class="title text-center">Đăng Ký Tài Khoản</h2>

<div class="container" style="margin-top:50px;">
    <div class="row justify-content-center">

        <div class="col-sm-9 d-flex justify-content-center" style="padding-right: 15px;">
            <div class="login-form p-4"
                 style="border: 1px solid #ddd; border-radius: 10px; background-color:#fff; width:100%; max-width:600px;">

                <h2 class="text-center mb-4">Đăng ký</h2>

                {{-- HIỂN THỊ LỖI --}}
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

                <div class="mt-4 text-center">
                    <p>Đã có tài khoản?
                        <a href="{{ URL::to('/login-checkout') }}" style="color:blue; font-weight:bold;">Đăng nhập</a>
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>

</section>

@endsection
