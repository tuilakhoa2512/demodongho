@extends('pages.admin_layout')
@section('admin_content')

<div class="panel panel-default">

    <div class="panel-heading">
        Danh sách người dùng
        @if(isset($filterStatus) && $filterStatus == 1)
            <small>(Trạng thái: Hiện)</small>
        @elseif(isset($filterStatus) && $filterStatus === "0")
            <small>(Trạng thái: Ẩn)</small>
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

    {{-- form lọc trạng thái --}}
    <div class="row" style="padding: 10px 15px;">
        <div class="col-sm-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline">
                <select name="status" class="form-control input-sm">
                    <option value="">Lọc trạng thái (Tất cả)</option>
                    <option value="1" {{ isset($filterStatus) && $filterStatus == 1 ? 'selected' : '' }}>
                        Đang Hoạt động
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
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->fullname }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->status == 1)
                            <span class="label label-success">Hoạt động</span>
                        @else
                            <span class="label label-danger">Bị dình chỉ</span>
                        @endif
                    </td>
                    <td>
                        @if($user->status == 1)
                            <a href="{{ route('admin.users.unactive', $user->id) }}"
                               class="btn btn-warning btn-xs">
                                Đình chỉ hoạt động
                            </a>
                        @else
                            <a href="{{ route('admin.users.active', $user->id) }}"
                               class="btn btn-success btn-xs">
                                Kích hoạt lại
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
{{-- PHÂN TRANG --}}

<footer class="panel-footer">
      <div class="row">
        <div class="col-sm-5 text-center">
          <small class="text-muted inline m-t-sm m-b-sm">
            Hiển thị {{ $users->firstItem() }} - {{ $users->lastItem() }}
            / {{ $users->total() }} Tài Khoản
          </small>
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $users->links('vendor.pagination.number-only') }}
        </div>
      </div>
    </footer>

@endsection
