@extends('pages.admin_layout')
@section('admin_content')

<div class="panel panel-default">

    <div class="panel-heading">
        Danh sách nhân sự
        @if(isset($filterStatus) && $filterStatus == 1)
            <small>(Trạng thái: Hoạt động)</small>
        @elseif(isset($filterStatus) && $filterStatus === "0")
            <small>(Trạng thái: Đình chỉ)</small>
        @endif
    </div>

    {{-- popup thông báo --}}
    @if (session('message'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session('message') }}',
                confirmButtonText: 'OK'
            });
        });
    </script>
    @endif

    {{-- form lọc --}}
    <div class="row" style="padding: 10px 15px;">
        <div class="col-sm-4">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="form-inline">
                <select name="status" class="form-control input-sm">
                    <option value="">Lọc trạng thái (Tất cả)</option>
                    <option value="1" {{ isset($filterStatus) && $filterStatus == 1 ? 'selected' : '' }}>
                        Đang hoạt động
                    </option>
                    <option value="0" {{ isset($filterStatus) && $filterStatus == 0 ? 'selected' : '' }}>
                        Bị đình chỉ
                    </option>
                </select>

                <button type="submit" class="btn btn-sm btn-default" style="margin-left:5px;">
                    Áp dụng
                </button>
            </form>
        </div>
    </div>

    {{-- bảng --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Phân Quyền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $staff)
                <tr>
                    <td>{{ $staff->id }}</td>
                    <td>{{ $staff->fullname }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        @switch($staff->role_id)
                            @case(1) Admin Coder @break
                            @case(3) Giám đốc @break
                            @case(4) Nhân viên bán hàng @break
                            @case(5) Nhân viên kho @break
                        @endswitch
                    </td>
                    <td>
    @if(Session::get('admin_role_id') == 1)
        <form action="{{ route('admin.staff.updateRole', $staff->id) }}" method="POST">
            @csrf
            <select name="role_id" class="form-control input-sm"
                    onchange="this.form.submit()">

                <option value="1" {{ $staff->role_id == 1 ? 'selected' : '' }}>
                    Admin Coder
                </option>
                <option value="3" {{ $staff->role_id == 3 ? 'selected' : '' }}>
                    Giám đốc
                </option>
                <option value="4" {{ $staff->role_id == 4 ? 'selected' : '' }}>
                    Nhân viên bán hàng
                </option>
                <option value="5" {{ $staff->role_id == 5 ? 'selected' : '' }}>
                    Nhân viên kho
                </option>
            </select>
        </form>
    @else
        {{-- role 3,4 chỉ được xem --}}
        @switch($staff->role_id)
            @case(1) Admin Coder @break
            @case(3) Giám đốc @break
            @case(4) NV Bán hàng @break
            @case(5) NV Kho @break
        @endswitch
    @endif
</td>

                    <td>
                        @if($staff->status == 1)
                            <span class="label label-success">Hoạt động</span>
                        @else
                            <span class="label label-danger">Bị đình chỉ</span>
                        @endif
                    </td>
                    <td>
                        @if($staff->status == 1)
                            <a href="{{ route('admin.staff.unactive', $staff->id) }}"
                               class="btn btn-warning btn-xs">
                                Đình chỉ
                            </a>
                        @else
                            <a href="{{ route('admin.staff.active', $staff->id) }}"
                               class="btn btn-success btn-xs">
                                Kích hoạt
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach

                @if($staffs->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">Không có nhân sự</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

</div>

@endsection
