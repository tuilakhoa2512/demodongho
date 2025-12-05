@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Sửa sản phẩm trong lô: {{ $storage->batch_code }}
      </header>

      <div class="panel-body">
        <div class="position-center">

          {{-- Hiển thị lỗi validate --}}
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.storage-details.update', $detail->id) }}">
            @csrf
            @method('PUT')

            {{-- Mã lô (chỉ xem, không sửa) --}}
            <div class="form-group">
              <label>Mã lô hàng</label>
              <input type="text"
                     class="form-control"
                     value="{{ $storage->batch_code }}"
                     readonly>
            </div>

            {{-- Tên sản phẩm trong lô --}}
            <div class="form-group">
              <label for="product_name">Tên sản phẩm (trong lô) <span class="text-danger">*</span></label>
              <input type="text"
                     name="product_name"
                     id="product_name"
                     class="form-control"
                     maxlength="255"
                     value="{{ old('product_name', $detail->product_name) }}"
                     required>
            </div>

            {{-- Số lượng nhập --}}
            <div class="form-group">
              <label for="import_quantity">Số lượng nhập <span class="text-danger">*</span></label>
              <input type="number"
                     name="import_quantity"
                     id="import_quantity"
                     class="form-control"
                     min="0"
                     value="{{ old('import_quantity', $detail->import_quantity) }}"
                     required>
            </div>

            {{-- Trạng thái kho --}}
            <div class="form-group">
              <label for="stock_status">Trạng thái kho <span class="text-danger">*</span></label>
              <select name="stock_status" id="stock_status" class="form-control" required>
                <option value="pending"  {{ old('stock_status', $detail->stock_status) === 'pending'  ? 'selected' : '' }}>Chờ bán</option>
                <option value="selling"  {{ old('stock_status', $detail->stock_status) === 'selling'  ? 'selected' : '' }}>Đang bán</option>
                <option value="sold_out" {{ old('stock_status', $detail->stock_status) === 'sold_out' ? 'selected' : '' }}>Hết hàng</option>
                <option value="stopped"  {{ old('stock_status', $detail->stock_status) === 'stopped'  ? 'selected' : '' }}>Ngừng bán</option>
              </select>
            </div>

            {{-- Ghi chú --}}
            <div class="form-group">
              <label for="note">Ghi chú</label>
              <textarea name="note"
                        id="note"
                        rows="3"
                        class="form-control"
                        placeholder="Ghi chú thêm (nếu có)">{{ old('note', $detail->note) }}</textarea>
            </div>

            <button type="submit" class="btn btn-info">Cập nhật sản phẩm</button>
            <a href="{{ route('admin.storage-details.by-storage', $storage->id) }}" class="btn btn-default">
              Quay lại
            </a>

          </form>

        </div>
      </div>
    </section>
  </div>
</div>

@endsection
