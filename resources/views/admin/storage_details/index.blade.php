@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    {{-- TIÊU ĐỀ --}}
    <div class="panel-heading" style="color:#000; font-weight:600;">
      Quản lý sản phẩm trong lô: {{ $storage->batch_code }}
    </div>

    {{-- THÔNG BÁO THÀNH CÔNG (SweetAlert) --}}
    @if (session('success'))
      <script>
          Swal.fire({
              title: "Thành công!",
              text: "{{ session('success') }}",
              icon: "success",
              confirmButtonText: "OK",
          });
      </script>
    @endif

    {{-- THÔNG TIN LÔ HÀNG --}}
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
        <div class="col-sm-4"></div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <strong>Ghi chú:</strong> {{ $storage->note ?? '—' }}
        </div>
      </div>
    </div>

    {{-- NÚT THÊM & QUAY LẠI --}}
    <div style="margin: 0 15px 15px 15px;">
      <a href="{{ route('admin.storage-details.create', $storage->id) }}" class="btn btn-primary btn-sm">
        + Thêm sản phẩm vào lô này
      </a>
      <a href="{{ route('admin.storages.index') }}" class="btn btn-default btn-sm">
        ← Quay lại danh sách lô
      </a>
    </div>

    {{-- CĂN GIỮA BẢNG --}}
    <style>
        table td, table th {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>

    {{-- BẢNG SẢN PHẨM TRONG LÔ --}}
    <div class="table-responsive">
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng nhập</th>
            <th>SL đang bán</th>
            <th>SL đã bán</th>
            <th>Trạng thái kho</th>
            <th>Hiển thị</th>
            <th style="width:160px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($details as $detail)
            @php
              // 2 cột mới: lấy từ query buildIndexQuery()
              $sellingQty = isset($detail->selling_qty) ? (int)$detail->selling_qty : null;
              $soldQty    = isset($detail->sold_qty) ? (int)$detail->sold_qty : null;
            @endphp

            <tr>
              <td>{{ $detail->id }}</td>

              {{-- Tên sản phẩm trong lô --}}
              <td>{{ $detail->product_name }}</td>

              {{-- Số lượng nhập --}}
              <td>{{ number_format($detail->import_quantity) }}</td>

              {{-- SL đang bán --}}
              <td>
                {{ $sellingQty !== null ? number_format($sellingQty) : '—' }}
              </td>

              {{-- SL đã bán --}}
              <td>
                {{ $soldQty !== null ? number_format($soldQty) : '—' }}
              </td>

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

              {{-- Hiển thị --}}
              <td>
                @if($detail->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>

              {{-- THAO TÁC --}}
              <td>

                {{-- Sửa dòng kho --}}
                <a href="{{ route('admin.storage-details.edit', $detail->id) }}"
                   title="Sửa"
                   style="margin-right:6px;">
                  <i class="fa fa-pencil-square-o text-success" style="font-size:18px;"></i>
                </a>

                {{-- Ẩn / Hiện dòng kho --}}
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

                {{-- Xem lại lô (trang hiện tại) --}}
                <a href="{{ route('admin.storage-details.by-storage', $storage->id) }}"
                   title="Xem lại lô"
                   style="margin-right:6px;">
                  <i class="fa fa-archive text-primary" style="font-size:18px;"></i>
                </a>

                {{-- Icon dẫn tới sản phẩm đã đăng bán từ dòng kho này --}}
                @if($detail->product)
                  <a href="{{ route('admin.products.show', $detail->product->id) }}"
                     title="Xem sản phẩm đã đăng bán">
                    <i class="fa fa-cubes text-info" style="font-size:18px;"></i>
                  </a>
                @else
                  <a href="#"
                     onclick="alert('Sản phẩm này chưa được đăng bán'); return false;"
                     title="Sản phẩm này chưa được đăng bán">
                    <i class="fa fa-cubes text-muted" style="font-size:18px;"></i>
                  </a>
                @endif

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">Chưa có sản phẩm nào trong lô này.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- PHÂN TRANG --}}
    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-5 text-center">
          <small class="text-muted inline m-t-sm m-b-sm">
            Hiển thị {{ $details->firstItem() }} - {{ $details->lastItem() }}
            / {{ $details->total() }} lô hàng
          </small>
        </div>

        <div class="text-center">
          {{ $details->links('vendor.pagination.number-only') }}
        </div>
      </div>
    </footer>

  </div>
</div>

@endsection
