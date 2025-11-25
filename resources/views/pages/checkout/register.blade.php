@extends('pages.layout')
@section('content')

<section id="form">
    <div class="container">
        <div class="row">

            <div class="col-sm-6 col-sm-offset-3">
                <div class="signup-form">
                    <h2>Đăng ký tài khoản</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ URL::to('/add-user') }}" method="POST">
                        {{ csrf_field() }}

                        <input type="text" name="fullname" placeholder="Họ và tên" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Mật khẩu" required>
                        <input type="text" name="phone" placeholder="Số điện thoại" required>

                        <button type="submit" class="btn btn-default">Đăng ký</button>

                        <p style="margin-top:15px;">
                            Đã có tài khoản? 
                            <a href="{{ URL::to('/login-checkout') }}" style="color:blue; font-weight:bold;">Đăng nhập</a>
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
