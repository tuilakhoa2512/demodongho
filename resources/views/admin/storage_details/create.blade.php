@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Thêm sản phẩm vào lô: {{ $storage->batch_code }}
      </header>
      <div class="panel-body">
        <div class="position-center">
          {{-- THÔNG TIN LÔ HIỆN TẠI --}}
          <div class="alert alert-info">
            <strong>Thông Tin Lô Hàng:</strong><br>
            Mã lô: <strong>{{ $storage->batch_code }}</strong><br>
            Ngày nhập:
            <strong>
              {{ $storage->import_date ? \Carbon\Carbon::parse($storage->import_date)->format('d/m/Y') : '—' }}
            </strong>
          </div>

          
          <form method="POST" action="{{ route('admin.storage-details.store', $storage->id) }}">
            @csrf

           
            <div class="form-group">
              <label for="product_name">Tên sản phẩm (trong lô) <span class="text-danger">*</span></label>
              <input type="text"
                     name="product_name"
                     id="product_name"
                     class="form-control"
                     value="{{ old('product_name') }}"
                     placeholder="VD: Đồng hồ Orient nam dây da..."
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
