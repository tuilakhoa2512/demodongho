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

    {{-- Thông báo lỗi --}}
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

    {{-- Hàng công cụ --}}
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

            <th>Ưu đãi</th>
            <th>Giá sau ưu đãi</th>

            <th>Trạng thái bán</th>
            <th>Hiển Thị</th>
            <th style="width:140px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse($products as $product)
            @php
              $storageDetail = optional($product->storageDetail);
              $storage       = optional($storageDetail->storage);

              // ✅ NEW promotion fields from controller (PromotionService)
              $hasPromo = !empty($product->promo_has_sale);
              $finalPrice = isset($product->final_price) ? (float)$product->final_price : (float)$product->price;
              $promoName = $product->promo_name ?? null;
              $promoLabel = $product->promo_label ?? null;
            @endphp

            <tr>
              <td>
                <label class="i-checks m-b-none">
                  <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"><i></i>
                </label>
              </td>

              <td>{{ $product->id }}</td>

              <td>
                @if ($product->productImage && $product->productImage->image_1)
                  <img src="{{ asset('storage/' . $product->productImage->image_1) }}"
                       alt="{{ $product->name }}"
                       style="width: 60px; height: 60px; object-fit: cover; border-radius:4px;">
                @else
                  <span>Không có ảnh</span>
                @endif
              </td>

              <td>{{ $product->name }}</td>

              <td>{{ number_format($product->quantity) }}</td>

              <td>
                <div style="line-height:1.2;">
                  <div style="font-weight:800;">
                    {{ number_format($product->price, 0, ',', '.') }} đ
                  </div>
                </div>
              </td>

              {{-- ✅ NEW: Ưu đãi (promotion) --}}
              <td>
                @if($hasPromo)
                  <span class="label label-info">
                    {{ $promoName ?? 'Ưu đãi' }}
                    @if(!empty($promoLabel))
                      ({{ $promoLabel }})
                    @endif
                  </span>
                @else
                  —
                @endif
              </td>

              {{-- ✅ NEW: Giá sau ưu đãi --}}
              <td>
                @if($hasPromo)
                  <strong style="color:#e60012;">
                    {{ number_format($finalPrice, 0, ',', '.') }} đ
                  </strong>
                @else
                  —
                @endif
              </td>

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

              <td>
                @if($product->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>

              <td>

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

                <a href="{{ route('admin.products.show', $product->id) }}"
                   title="Xem chi tiết"
                   style="margin-right:6px;">
                  <i class="fa fa-info-circle text-info" style="font-size:18px;"></i>
                </a>

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
              <td colspan="11" class="text-center">Chưa có sản phẩm nào.</td>
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
          {{ $products->links('vendor.pagination.number-only') }}
        </div>

      </div>
    </footer>

  </div>
</div>

@endsection
