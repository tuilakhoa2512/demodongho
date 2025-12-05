@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Thêm sản phẩm vào lô: {{ $storage->batch_code }}
      </header>

      <div class="panel-body">
      @if (session('message'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session("message") }}',
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif
        <div class="position-center">

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.storage-details.store', $storage->id) }}">
            @csrf

            <div class="form-group">
              <label for="product_name">Tên sản phẩm (trong lô) <span class="text-danger">*</span></label>
              <input type="text"
                     name="product_name"
                     id="product_name"
                     class="form-control"
                     value="{{ old('product_name') }}"
                     required>
            </div>

            <div class="form-group">
              <label for="import_quantity">Số lượng nhập <span class="text-danger">*</span></label>
              <input type="number"
                     name="import_quantity"
                     id="import_quantity"
                     class="form-control"
                     min="1"
                     value="{{ old('import_quantity') }}"
                     required>
            </div>

            <div class="form-group">
              <label for="stock_status">Trạng thái kho (tùy chọn)</label>
              <select name="stock_status" id="stock_status" class="form-control">
                <option value="">-- Mặc định: Chờ bán --</option>
                <option value="pending"  {{ old('stock_status') === 'pending' ? 'selected' : '' }}>Chờ bán</option>
                <option value="selling"  {{ old('stock_status') === 'selling' ? 'selected' : '' }}>Đang bán</option>
                <option value="sold_out" {{ old('stock_status') === 'sold_out' ? 'selected' : '' }}>Hết hàng</option>
                <option value="stopped"  {{ old('stock_status') === 'stopped' ? 'selected' : '' }}>Ngừng bán</option>
              </select>
            </div>

            <div class="form-group">
              <label for="note">Ghi chú</label>
              <textarea name="note"
                        id="note"
                        rows="3"
                        class="form-control"
                        placeholder="Ghi chú thêm (nếu có)">{{ old('note') }}</textarea>
            </div>

            <button type="submit" class="btn btn-info">Lưu vào lô</button>
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
