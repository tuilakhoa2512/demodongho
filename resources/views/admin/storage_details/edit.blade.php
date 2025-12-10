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

            {{-- Mã lô: chỉ xem --}}
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
                     min="1"
                     value="{{ old('import_quantity', $detail->import_quantity) }}"
                     required>
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
