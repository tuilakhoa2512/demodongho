@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="font-size: 18px; font-weight: 600;">
      DANH SÁCH SẢN PHẨM
    </div>

    {{-- Thông báo thành công --}}
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

    {{-- Thông báo lỗi (ví dụ kho đang ẩn, không cho hiện sản phẩm) --}}
    @if (session('error'))
      <script>
          Swal.fire({
              icon: 'error',
              title: 'Không thể thực hiện!',
              text: '{{ session("error") }}',
              confirmButtonText: "OK"
          });
      </script>
    @endif

    {{-- Hàng công cụ (tạm để nguyên, sau này bạn làm bulk action / search sau) --}}
    <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">
        <select class="input-sm form-control w-sm inline v-middle">
          <option value="0">Thao tác</option>
          <option value="1">Xóa đã chọn (chưa làm)</option>
          <option value="2">Xuất Excel (chưa làm)</option>
        </select>
        <button class="btn btn-sm btn-default">Áp dụng</button>
      </div>

      <div class="col-sm-3"></div>

      <div class="col-sm-4">
        <div class="input-group">
          <input type="text" class="input-sm form-control" placeholder="Tìm kiếm (chưa làm)">
          <span class="input-group-btn">
            <button class="btn btn-sm btn-default" type="button">Go!</button>
          </span>
        </div>
      </div>
    </div>

    <div class="table-responsive">

      {{-- Căn giữa toàn bộ bảng --}}
      <style>
          table td, table th {
              text-align: center !important;
              vertical-align: middle !important;
          }
      </style>

      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th style="width:20px;">
              <label class="i-checks m-b-none">
                <input type="checkbox"><i></i>
              </label>
            </th>
            <th>ID</th>
            <th>Hình</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng tồn kho</th>
            <th>Giá bán</th>
            <th>Trạng thái bán</th>
            <th>Hiển Thị</th>
            <th style="width:110px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse($products as $product)
            @php
              $storageDetail = optional($product->storageDetail);
              $storage       = optional($storageDetail->storage);
            @endphp

            <tr>
              {{-- checkbox --}}
              <td>
                <label class="i-checks m-b-none">
                  <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"><i></i>
                </label>
              </td>

              {{-- ID --}}
              <td>{{ $product->id }}</td>

              {{-- Hình (ảnh 1) --}}
              <td>
                @if ($product->productImage && $product->productImage->image_1)
                  <img src="{{ asset('storage/' . $product->productImage->image_1) }}"
                       alt="{{ $product->name }}"
                       style="width: 60px; height: 60px; object-fit: cover; border-radius:4px;">
                @else
                  <span>Không có ảnh</span>
                @endif
              </td>

              {{-- Tên sản phẩm --}}
              <td>{{ $product->name }}</td>

              {{-- Số lượng tồn kho --}}
              <td>{{ number_format($product->quantity) }}</td>

              {{-- Giá bán --}}
              <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>

              {{-- Trạng thái bán --}}
              <td>
                @if($product->stock_status === 'selling')
                  <span class="label label-success">Đang bán</span>
                @elseif($product->stock_status === 'sold_out')
                  <span class="label label-default">Hết hàng</span>
                @elseif($product->stock_status === 'stopped')
                  <span class="label label-danger">Ngừng bán</span>
                @else
                  <span class="label label-default">—</span>
                @endif
              </td>

              {{-- Hiển thị --}}
              <td>
                @if($product->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>

              {{-- Thao tác: Ẩn/Hiện – Xem chi tiết – Tới kho hàng --}}
              <td>

                {{-- Ẩn / Hiện --}}
                <form action="{{ route('admin.products.toggle-status', $product->id) }}"
                      method="POST"
                      style="display:inline-block; margin-right:6px;">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                          style="border:none; background:none; padding:0;"
                          title="{{ $product->status ? 'Ẩn sản phẩm' : 'Hiển thị sản phẩm' }}">
                    @if($product->status)
                      <i class="fa fa-eye-slash text-warning" style="font-size:18px;"></i>
                    @else
                      <i class="fa fa-eye text-warning" style="font-size:18px;"></i>
                    @endif
                  </button>
                </form>

                {{-- Xem chi tiết --}}
                <a href="{{ route('admin.products.show', $product->id) }}"
                   title="Xem chi tiết"
                   style="margin-right:6px;">
                  <i class="fa fa-info-circle text-info" style="font-size:18px;"></i>
                </a>

                {{-- Tới kho hàng của sản phẩm --}}
                @if($storage && $storage->id)
                  <a href="{{ route('admin.storage-details.by-storage', $storage->id) }}"
                     title="Xem sản phẩm trong lô {{ $storage->batch_code }}">
                    <i class="fa fa-archive text-primary" style="font-size:18px;"></i>
                  </a>
                @else
                  <span title="Không tìm thấy lô hàng">
                    <i class="fa fa-archive text-muted" style="font-size:18px; opacity:0.5;"></i>
                  </span>
                @endif

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center">Chưa có sản phẩm nào.</td>
            </tr>
          @endforelse

        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">

        <div class="col-sm-5 text-center">
          @if($products->total() > 0)
            <small class="text-muted inline m-t-sm m-b-sm">
              Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }}
              / {{ $products->total() }} sản phẩm
            </small>
          @else
            <small class="text-muted inline m-t-sm m-b-sm">
              Không có sản phẩm nào.
            </small>
          @endif
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $products->links('pagination::bootstrap-4') }}
        </div>

      </div>
    </footer>

  </div>
</div>

@endsection
