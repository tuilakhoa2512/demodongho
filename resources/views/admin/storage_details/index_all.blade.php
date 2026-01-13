@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    {{-- Tiêu đề --}}
    <div class="panel-heading" style="color:#000; font-weight:600;">
      @isset($currentStatus)
          @if($currentStatus == 'pending')      Hàng Chưa Bán Trong Kho
          @elseif($currentStatus == 'selling')  Hàng Đang Bán Trong Kho
          @elseif($currentStatus == 'sold_out') Hàng Bán Hết Trong Kho
          @elseif($currentStatus == 'stopped')  Hàng Ngừng Bán Trong Kho
          @endif
      @else
          Quản Lý Kho Hàng (Của Các Lô)
      @endisset
    </div>

    {{-- Toast thông báo thành công --}}
    @if(session('success'))
      <div class="alert alert-success" style="margin:15px;">
        {{ session('success') }}
      </div>
    @endif

    {{-- Hàng lọc theo lô hàng --}}
    <div class="row w3-res-tb" style="padding: 0 15px; margin-top: 15px;">
      <div class="col-sm-5 m-b-xs">
        <form method="GET" action="{{ route('admin.storage-details.index') }}" class="form-inline">

          {{-- Dropdown chọn lô --}}
          <select name="storage_id" class="input-sm form-control w-sm inline v-middle">
            <option value="">Lọc theo lô (Tất cả)</option>
            @foreach($storages as $st)
              <option value="{{ $st->id }}"
                {{ isset($selectedStorageId) && $selectedStorageId == $st->id ? 'selected' : '' }}>
                {{ $st->batch_code }}
              </option>
            @endforeach
          </select>

          {{-- Nút áp dụng --}}
          <button type="submit" class="btn btn-sm btn-default" style="margin-left:5px;">
            Áp dụng
          </button>

        </form>
      </div>

      <div class="col-sm-4"></div>
    </div>

    {{-- Bảng danh sách kho --}}
    <div class="table-responsive" style="margin-top:10px;">
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
            <th>Mã lô</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng nhập</th>
            <th>SL đang bán</th>
            <th>SL đã bán</th>
            <th>Trạng thái kho</th>
            <th>Hiển thị</th>
            <th style="width:150px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse($details as $detail)
            @php
              $sellingQty = (int)($detail->selling_qty ?? 0);
              $soldQty    = (int)($detail->sold_qty ?? 0);
            @endphp

            <tr>
              <td>{{ $detail->id }}</td>

              {{-- Hiển thị mã lô --}}
              <td>{{ optional($detail->storage)->batch_code ?? '—' }}</td>

              {{-- Tên sản phẩm trong lô --}}
              <td>{{ $detail->product_name }}</td>

              {{-- SL nhập --}}
              <td>{{ number_format($detail->import_quantity) }}</td>

              {{-- SL đang bán (tồn hiện tại của product) --}}
              <td>{{ number_format($sellingQty) }}</td>

              {{-- SL đã bán (tính từ order_details + orders.status != canceled) --}}
              <td>{{ number_format($soldQty) }}</td>

              {{-- Nhãn trạng thái kho --}}
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

              {{-- Ẩn/hiện --}}
              <td>
                @if($detail->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>

              {{-- Các nút thao tác --}}
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

                {{-- Xem lô chứa dòng kho này --}}
                @if($detail->storage)
                  <a href="{{ route('admin.storage-details.by-storage', $detail->storage->id) }}"
                     title="Xem lô"
                     style="margin-right:6px;">
                    <i class="fa fa-database" style="font-size:18px;"></i>
                  </a>
                @endif

                {{-- Icon dẫn tới sản phẩm đã đăng bán --}}
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
              <td colspan="9" class="text-center">Kho đang trống.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

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
