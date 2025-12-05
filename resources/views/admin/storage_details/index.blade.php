@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

   
    <div class="panel-heading" style="color:#000; font-weight:600;">
      Quản lý sản phẩm trong lô: {{ $storage->batch_code }}
    </div>

  
    @if (session('success'))
      <div class="alert alert-success" style="margin: 15px;">
        {{ session('success') }}
      </div>
    @endif

    <div style="margin: 15px; padding: 15px; border: 1px solid #e5e5e5; border-radius: 4px; background: #f9f9f9;">
      <h4 style="margin-top:0; font-weight:600; text-align:center">THÔNG TIN LÔ HÀNG</h4> <br>

      <div class="row" style="margin-bottom: 5px;">
        <div class="col-sm-4">
          <strong>Mã lô:</strong> {{ $storage->batch_code }}
        </div>
        <div class="col-sm-4">
          <strong>Nhà cung cấp:</strong> {{ $storage->supplier_name ?? '—' }}
        </div>
        <div class="col-sm-4">
          <strong>Email NCC:</strong> {{ $storage->supplier_email ?? '—' }}
        </div>
      </div>

      <div class="row" style="margin-bottom: 5px;">
        <div class="col-sm-4">
          <strong>Ngày nhập:</strong>
          {{ $storage->import_date ? \Carbon\Carbon::parse($storage->import_date)->format('d/m/Y') : '—' }}
        </div>
        <div class="col-sm-4">
          <strong>Trạng thái lô:</strong>
          @if($storage->status)
            <span class="label label-success">Đang hoạt động</span>
          @else
            <span class="label label-default">Đã ẩn / ngừng</span>
          @endif
        </div>
        <div class="col-sm-4">
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <strong>Ghi chú:</strong> {{ $storage->note ?? '—' }}
        </div>
      </div>
    </div>

    <div style="margin: 0 15px 15px 15px;">
      <a href="{{ route('admin.storage-details.create', $storage->id) }}" class="btn btn-primary btn-sm">
        + Thêm sản phẩm vào lô này
      </a>
      <a href="{{ route('admin.storages.index') }}" class="btn btn-default btn-sm">
        ← Quay lại danh sách lô
      </a>
    </div>

    <style>
        table td, table th {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>

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

           
              <td>{{ $detail->product_name }}</td>

              <td>{{ number_format($detail->import_quantity) }}</td>

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

                {{-- Sửa --}}
                <a href="{{ route('admin.storage-details.edit', $detail->id) }}"
                   title="Sửa"
                   style="margin-right:6px;">
                  <i class="fa fa-pencil-square-o text-success" style="font-size:18px;"></i>
                </a>

                {{-- Ẩn/Hiện --}}
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

                {{-- Xem lại lô (reload trang hiện tại) --}}
                <a href="{{ route('admin.storage-details.by-storage', $storage->id) }}"
                   title="Xem lại lô">
                  <i class="fa fa-archive text-primary" style="font-size:18px;"></i>
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

    {{-- PHÂN TRANG --}}
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
