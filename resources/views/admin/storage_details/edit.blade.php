@extends('pages.admin_layout')
@section('admin_content')

@php
    /**
     * ===== CÁC BIẾN HỖ TRỢ LOGIC =====
     */
    $isSoldOut = ($detail->stock_status === 'sold_out');

    // SL đang bán + đã bán (được build từ controller index)
    $sellingQty = (int) ($detail->selling_qty ?? 0);
    $soldQty    = (int) ($detail->sold_qty ?? 0);

    // Số lượng nhập tối thiểu không được < đã bán + đang bán
    $minImportQty = max(1, $sellingQty + $soldQty);

    // (NẾU MUỐN) Giới hạn max số lượng nhập
    // Nếu KHÔNG muốn giới hạn → set = null
    $maxImportQty = 10000;
@endphp

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Sửa sản phẩm trong lô: {{ $storage->batch_code }}
      </header>

      <div class="panel-body">
        <div class="position-center">

          {{-- ===== CẢNH BÁO KHI ĐÃ SOLD OUT ===== --}}
          @if($isSoldOut)
            <div class="alert alert-warning">
              <strong>Lưu ý:</strong>
              Sản phẩm này đã <b>bán hết</b>, không thể chỉnh sửa số lượng nhập.
            </div>
          @endif

          <form method="POST" action="{{ route('admin.storage-details.update', $detail->id) }}">
            @csrf
            @method('PUT')

            {{-- MÃ LÔ (CHỈ XEM) --}}
            <div class="form-group">
              <label>Mã lô hàng</label>
              <input type="text"
                     class="form-control"
                     value="{{ $storage->batch_code }}"
                     readonly>
            </div>

            {{-- TÊN SẢN PHẨM TRONG LÔ --}}
            <div class="form-group">
              <label for="product_name">
                Tên sản phẩm (trong lô)
                <span class="text-danger">*</span>
              </label>

              <input type="text"
                     name="product_name"
                     id="product_name"
                     class="form-control"
                     maxlength="255"
                     value="{{ old('product_name', $detail->product_name) }}"
                     {{ $isSoldOut ? 'disabled' : '' }}
                     required>
            </div>

            {{-- SỐ LƯỢNG NHẬP --}}
            <div class="form-group">
              <label for="import_quantity">
                Số lượng nhập
                <span class="text-danger">*</span>
              </label>

              <input type="number"
                     name="import_quantity"
                     id="import_quantity"
                     class="form-control"
                     min="{{ $minImportQty }}"
                     @if($maxImportQty) max="{{ $maxImportQty }}" @endif
                     value="{{ old('import_quantity', $detail->import_quantity) }}"
                     {{ $isSoldOut ? 'disabled' : '' }}
                     required>

              <small class="text-muted">
                Tối thiểu: {{ $minImportQty }}
                @if($maxImportQty)
                  | Tối đa: {{ number_format($maxImportQty) }}
                @endif
              </small>
            </div>

            {{-- GHI CHÚ --}}
            <div class="form-group">
              <label for="note">Ghi chú</label>
              <textarea name="note"
                        id="note"
                        rows="3"
                        class="form-control"
                        placeholder="Ghi chú thêm (nếu có)"
                        {{ $isSoldOut ? 'disabled' : '' }}>{{ old('note', $detail->note) }}</textarea>
            </div>

            {{-- NÚT HÀNH ĐỘNG --}}
            <button type="submit"
                    class="btn btn-info"
                    {{ $isSoldOut ? 'disabled' : '' }}>
              Cập nhật sản phẩm
            </button>

            <a href="{{ route('admin.storage-details.by-storage', $storage->id) }}"
               class="btn btn-default">
              Quay lại
            </a>

          </form>

        </div>
      </div>
    </section>
  </div>
</div>

@endsection
