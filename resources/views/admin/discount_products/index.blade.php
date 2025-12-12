@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="color:#000; font-weight:600;">
      Quản lý ưu đãi sản phẩm
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin:15px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger" style="margin:15px;">{{ session('error') }}</div>
    @endif

    <div style="margin: 15px;">
      <a href="{{ route('admin.discount-products.create') }}" class="btn btn-info btn-sm">
        + Thêm ưu đãi
      </a>
    </div>

    <div class="table-responsive">
      <style>
        table td, table th { text-align:center !important; vertical-align:middle !important; }
      </style>

      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên ưu đãi</th>
            <th>% giảm</th>
            <th>Trạng thái</th>
            <th style="width:180px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse($discounts as $d)
            <tr>
              <td>{{ $d->id }}</td>
              <td>{{ $d->name }}</td>
              <td>{{ $d->rate }}%</td>
              <td>
                @if($d->status)
                  <span class="label label-success">Hoạt động</span>
                @else
                  <span class="label label-default">Tắt</span>
                @endif
              </td>
              <td>
                {{-- Edit --}}
                <a href="{{ route('admin.discount-products.edit', $d->id) }}"
                   title="Sửa" style="margin-right:8px;">
                  <i class="fa fa-pencil-square-o text-success" style="font-size:18px;"></i>
                </a>

                {{-- Manage products --}}
                <a href="{{ route('admin.discount-products.products.index', $d->id) }}"
                   title="Sản phẩm trong ưu đãi" style="margin-right:8px;">
                  <i class="fa fa-cubes text-primary" style="font-size:18px;"></i>
                </a>

                {{-- Toggle status --}}
                <form action="{{ route('admin.discount-products.toggle-status', $d->id) }}"
                      method="POST" style="display:inline-block;">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                          title="{{ $d->status ? 'Tắt ưu đãi (tất cả SP sẽ tạm ngừng)' : 'Bật ưu đãi (SP còn hạn sẽ tự bật)' }}"
                          style="background:none;border:none;padding:0;">
                    @if($d->status)
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
              <td colspan="5" class="text-center">Chưa có ưu đãi nào.</td>
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
