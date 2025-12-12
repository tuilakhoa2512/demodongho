@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="color:#000; font-weight:600;">
      Chương Trình Ưu Đãi Sản Phẩm
    </div>

    {{-- Thông báo thành công --}}
    @if (session('success'))
      <div class="alert alert-success" style="margin:15px;">
        {{ session('success') }}
      </div>
    @endif

    <div style="margin: 15px;">
      <a href="{{ route('admin.discount-products.create') }}" class="btn btn-primary btn-sm">
        + Thêm Chương Trình Mới
      </a>
    </div>

    <div class="table-responsive">

      <style>
        table td, table th {
          text-align: center !important;
          vertical-align: middle !important;
        }
      </style>

      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên chương trình</th>
            <th>% Giảm</th>
            <th>Trạng thái</th>
            <th style="width:150px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse($discounts as $discount)
            <tr>
              <td>{{ $discount->id }}</td>
              <td>{{ $discount->name }}</td>
              <td>{{ $discount->rate }}%</td>
              <td>
                @if($discount->status)
                  <span class="label label-success">Đang hoạt động</span>
                @else
                  <span class="label label-default">Đã tắt</span>
                @endif
              </td>
              <td>
                {{-- Sửa --}}
                <a href="{{ route('admin.discount-products.edit', $discount->id) }}"
                   title="Sửa"
                   style="margin-right:6px;">
                  <i class="fa fa-pencil-square-o text-success" style="font-size:18px;"></i>
                </a>

                {{-- Ẩn/Hiện --}}
                <form action="{{ route('admin.discount-products.toggle-status', $discount->id) }}"
                      method="POST"
                      style="display:inline-block;">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                          style="border:none; background:none; padding:0;"
                          title="{{ $discount->status ? 'Tắt chương trình' : 'Bật chương trình' }}">
                    @if($discount->status)
                      <i class="fa fa-eye-slash text-warning" style="font-size:18px;"></i>
                    @else
                      <i class="fa fa-eye text-warning" style="font-size:18px;"></i>
                    @endif
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Chưa có chương trình ưu đãi nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-12 text-right text-center-xs">
          {{ $discounts->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </footer>

  </div>
</div>

@endsection
