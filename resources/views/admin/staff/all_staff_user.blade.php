@extends('pages.admin_layout')
@section('admin_content')

@php
    $currentAdminId   = (int) Session::get('admin_id');
    $currentAdminRole = (int) Session::get('admin_role_id');
@endphp

<div class="panel panel-default">

    {{-- ===== HEADER ===== --}}
    <div class="panel-heading">
        Danh sách nhân sự
        @if(isset($filterStatus) && $filterStatus == 1)
            <small>(Trạng thái: Hoạt động)</small>
        @elseif(isset($filterStatus) && $filterStatus === "0")
            <small>(Trạng thái: Đình chỉ)</small>
        @endif
    </div>

    {{-- ===== THÔNG BÁO ===== --}}

    <!-- đã có parial -->

    {{-- ===== FILTER ===== --}}
    <div class="row" style="padding: 10px 15px;">
        <div class="col-sm-4">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="form-inline">
                <select name="status" class="form-control input-sm">
                    <option value="">Lọc trạng thái (Tất cả)</option>
                    <option value="1" {{ (string)$filterStatus === "1" ? 'selected' : '' }}>
                        Đang hoạt động
                    </option>
                    <option value="0" {{ (string)$filterStatus === "0" ? 'selected' : '' }}>
                        Bị đình chỉ
                    </option>
                </select>

                <button type="submit" class="btn btn-sm btn-default" style="margin-left:5px;">
                    Áp dụng
                </button>
            </form>
        </div>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Phân quyền</th>
                    <th>Trạng thái</th>
                    <th style="width:140px">Thao tác</th>
                </tr>
            </thead>
            <tbody>

                @forelse($staffs as $staff)
                    <tr>
                        <td>{{ $staff->id }}</td>
                        <td>{{ $staff->fullname }}</td>
                        <td>{{ $staff->email }}</td>

                        {{-- ===== ROLE TEXT ===== --}}
                        <td>
                            @switch($staff->role_id)
                                @case(1) Admin Coder @break
                                @case(3) Giám đốc @break
                                @case(4) Nhân viên bán hàng @break
                                @case(5) Nhân viên kho @break
                            @endswitch
                        </td>

                        {{-- ===== UPDATE ROLE (CHỈ ROLE 1) ===== --}}
                        <td>
                            @if($currentAdminRole === 1 && $staff->id !== $currentAdminId)
                                <form action="{{ route('admin.staff.updateRole', $staff->id) }}" method="POST">
                                    @csrf
                                    <select name="role_id"
                                            class="form-control input-sm"
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
                                <span class="text-muted">
                                    @switch($staff->role_id)
                                        @case(1) Admin Coder @break
                                        @case(3) Giám đốc @break
                                        @case(4) NV Bán hàng @break
                                        @case(5) NV Kho @break
                                    @endswitch
                                </span>
                            @endif
                        </td>

                        {{-- ===== STATUS ===== --}}
                        <td>
                            @if($staff->status == 1)
                                <span class="label label-success">Hoạt động</span>
                            @else
                                <span class="label label-danger">Bị đình chỉ</span>
                            @endif
                        </td>

                        {{-- ===== ACTIONS ===== --}}
                        <td>
                            @php
                                $isSelf = ($staff->id === $currentAdminId);
                            @endphp

                            @if($staff->status == 1)
                                <button
                                    class="btn btn-warning btn-xs btn-change-status"
                                    data-url="{{ route('admin.staff.unactive', $staff->id) }}"
                                    data-action="đình chỉ"
                                    data-self="{{ $isSelf ? 1 : 0 }}"
                                    data-role="{{ $staff->role_id }}"
                                >
                                    Đình chỉ
                                </button>
                            @else
                                <button
                                    class="btn btn-success btn-xs btn-change-status"
                                    data-url="{{ route('admin.staff.active', $staff->id) }}"
                                    data-action="kích hoạt"
                                    data-self="{{ $isSelf ? 1 : 0 }}"
                                    data-role="{{ $staff->role_id }}"
                                >
                                    Kích hoạt
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Không có nhân sự
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ===== PAGINATION ===== --}}
    <footer class="panel-footer">
        <div class="row">
            <div class="col-sm-5 text-center">
                <small class="text-muted inline m-t-sm m-b-sm">
                    Hiển thị {{ $staffs->firstItem() }} - {{ $staffs->lastItem() }}
                    / {{ $staffs->total() }} tài khoản
                </small>
            </div>

            <div class="col-sm-7 text-center">
                {{ $staffs->links('vendor.pagination.number-only') }}
            </div>
        </div>
    </footer>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-change-status').forEach(function (btn) {
        btn.addEventListener('click', function () {

            const url    = this.dataset.url;
            const action = this.dataset.action;
            const isSelf = this.dataset.self === "1";
            const role   = parseInt(this.dataset.role);

            // TỰ ĐÌNH CHỈ CHÍNH MÌNH
            if (isSelf) {
                Swal.fire({
                    icon: 'error',
                    title: 'Không được phép',
                    text: 'Bạn không thể đình chỉ hoặc kích hoạt chính mình.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // KHÔNG ĐƯỢC ĐÌNH CHỈ ADMIN CODER
            if (role === 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Bị chặn',
                    text: 'Không thể đình chỉ Admin Coder.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // XÁC NHẬN HÀNH ĐỘNG
            Swal.fire({
                title: 'Xác nhận',
                text: `Bạn có chắc muốn ${action} nhân sự này?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

});
</script>


@endsection
