@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Thêm người dùng
            </header>

            <div class="panel-body">

                {{-- THÔNG BÁO --}}
                @if ($errors->any())
                    <script>
                        $(document).ready(function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                html: `{!! implode('<br>', $errors->all()) !!}`,
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif

                <div class="position-center">

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Họ tên</label>
                            <input type="text"
                                   name="fullname"
                                   class="form-control"
                                   placeholder="Nhập họ tên"
                                   value="{{ old('fullname') }}">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="Nhập email"
                                   value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label>Mật khẩu</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Nhập mật khẩu">
                        </div>

                        <div class="form-group">
                            <label>Phân quyền</label>
                            <select name="role_id" class="form-control input-sm m-bot15">
                                <option value="">-- Chọn quyền --</option>
                                <option value="2">Khách hàng</option>
                                <option value="1">Admin Coder</option>
                                <option value="3">Giám đốc</option>
                                <option value="4">Nhân viên bán hàng</option>
                                <option value="5">Nhân viên kho</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info">
                            Thêm người dùng
                        </button>

                    </form>

                </div>
            </div>
        </section>
    </div>
</div>

@endsection
