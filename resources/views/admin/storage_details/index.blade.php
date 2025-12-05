@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    {{-- Tiêu đề --}}
    <div class="panel-heading" style="color:#000; font-weight:600;">
      Sản phẩm trong lô: {{ $storage->batch_code }}
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
      <div class="alert alert-success" style="margin:15px;">
        {{ session('success') }}
      </div>
    @endif

    {{-- Nút chức năng --}}
    <div style="margin: 15px;">
      <a href="{{ route('admin.storage-details.create', $storage->id) }}" class="btn btn-primary btn-sm">
        + Thêm sản phẩm vào lô này
      </a>
      <a href="{{ route('admin.storages.index') }}" class="btn btn-default btn-sm">
        ← Quay lại danh sách lô
      </a>
    </div>

    {{-- Bảng danh sách sản phẩm trong lô --}}
    <div class="table-responsive">
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng nhập</th>
            <th>Trạng thái kho</th>
            <th>Ghi chú</th>
            <th>Hiển thị</th>
            <th style="width:130px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($details as $detail)
            <tr>
              <td>{{ $detail->id }}</td>

              {{-- Tên sản phẩm --}}
              <td>{{ $detail->product_name }}</td>

              {{-- Số lượng nhập --}}
              <td>{{ number_format($detail->import_quantity) }}</td>

              {{-- Trạng thái kho --}}
              <td>
                @if($detail->stock_status === 'pending')
                  <span class="label label-warning">Chờ bán</span>
                @elseif($detail->stock_status === 'selling')
                  <span class="label label-success">Đang bán</span>
                @elseif($detail->stock_status === 'sold_out')
                  <span class="label label-default">Hết hàng</span>
                @elseif($detail->stock_status === 'stopped')
                  <span class="label label-danger">Ngừng bán</span>
                @else
                  <span class="label label-default">—</span>
                @endif
              </td>

              {{-- Ghi chú --}}
              <td>{{ $detail->note }}</td>

              {{-- Hiển thị --}}
              <td>
                @if($detail->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>

              {{-- Thao tác với icon --}}
              <td>

                {{-- Nút sửa --}}
                <a href="{{ route('admin.storage-details.edit', $detail->id) }}"
                   title="Sửa"
                   style="margin-right:6px;">
                  <i class="fa fa-pencil-square-o text-success" style="font-size:18px;"></i>
                </a>

                {{-- Nút ẩn/hiện --}}
                <form action="{{ route('admin.storage-details.toggle-status', $detail->id) }}"
                      method="POST"
                      style="display:inline-block; margin-right:6px;">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                          title="{{ $detail->status ? 'Ẩn' : 'Hiện' }}"
                          style="background:none; border:none; padding:0;">
                    @if($detail->status)
                      <i class="fa fa-eye-slash text-warning" style="font-size:18px;"></i>
                    @else
                      <i class="fa fa-eye text-warning" style="font-size:18px;"></i>
                    @endif
                  </button>
                </form>

              
                <a href="{{ route('admin.storage-details.by-storage', $storage->id) }}"
                   title="Xem lô">
                  <i class="fa fa-database" style="font-size:18px;"></i>
                </a>

              </td>
            </tr>

          @empty
            <tr>
              <td colspan="7" class="text-center">Chưa có sản phẩm nào trong lô này.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Phần phân trang --}}
    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-12 text-right text-center-xs">
          {{ $details->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </footer>

  </div>
</div>

@endsection
