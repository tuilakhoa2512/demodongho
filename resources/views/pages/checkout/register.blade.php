@extends('pages.layout')
@section('content')

<div class="container" style="margin-top:50px;">
    <div class="row">

        <!-- CONTENT SAU SIDEBAR -->
        <div class="col-sm-12 col-md-9 col-lg-9">

            <div class="register-form">

                <h2 class="title text-center mb-4">ĐĂNG KÝ</h2>

                <div class="register-inner">
                @if ($errors->any())
                    <div class="alert alert-danger text-center">
                        @foreach ($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                @endif
                
                    <form action="{{ URL::to('/add-user') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="fullname"
                                   class="form-control"
                                   placeholder="Nguyễn Văn A"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email"
                                   class="form-control"
                                   placeholder="xxx@gmail.com"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Mật khẩu</label>
                            <input type="password" name="password"
                                   id="password"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Xác nhận mật khẩu</label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="form-control"
                                   required>
                            <small id="password-match-message"></small>
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone"
                                   class="form-control"
                                   required>
                        </div>

                        <button type="submit" class="register-btn">
                            Đăng ký
                        </button>
                    </form>

                    <div class="mt-4 text-center">
                        Đã có tài khoản?
                        <a href="{{ url('/login-checkout') }}" style="color:blue;font-weight:bold;">
                            Đăng nhập
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- JS KIỂM TRA KHỚP MẬT KHẨU -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const confirm  = document.getElementById('password_confirmation');
    const message  = document.getElementById('password-match-message');

    function checkMatch() {
        if (!confirm.value) {
            message.textContent = '';
            return;
        }

        if (password.value === confirm.value) {
            message.textContent = '✔ Mật khẩu khớp';
            message.style.color = 'green';
        } else {
            message.textContent = '✖ Mật khẩu không khớp';
            message.style.color = 'red';
        }
    }

    password.addEventListener('keyup', checkMatch);
    confirm.addEventListener('keyup', checkMatch);
});
</script>


@endsection
