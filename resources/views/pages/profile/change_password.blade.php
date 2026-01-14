@extends('pages.layout')
@section('content')

<div class="container" style="margin-top:50px;">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="change-password-box">

                <h3 class="text-center mb-4">Đổi mật khẩu</h3>

                {{-- Success --}}
                @if(session('success'))
                    <div class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('profile.changePassword') }}" method="POST">
                    @csrf

                    {{-- EMAIL --}}
                    <div class="form-group">
                        <label>Email đăng ký</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="xxx@gmail.com"
                               required>
                    </div>

                    {{-- PASSWORD --}}
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Ít nhất 8 ký tự, có chữ hoa và số"
                               required>
                    </div>

                    {{-- CONFIRM --}}
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               required>
                    </div>

                    <button class="btn-change-password">
                        Cập nhật mật khẩu
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<style>
.change-password-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.change-password-box .form-group {
    margin-bottom: 18px;
}

.change-password-box .form-control {
    height: 42px;
}

.btn-change-password {
    width: 100%;
    height: 44px;
    background: #D70018;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
}

.btn-change-password:hover {
    background: #b80015;
}
</style>

@endsection
