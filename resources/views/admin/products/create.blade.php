@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Đăng sản phẩm mới từ kho
      </header>
      <div class="panel-body">
        <div class="position-center">
          <form method="POST"
                action="{{ route('admin.products.store') }}"
                enctype="multipart/form-data">
            @csrf

            {{-- Chọn sản phẩm trong kho (StorageDetail) --}}
            <div class="form-group">
              <label for="storage_detail_id">
                Chọn sản phẩm trong kho <span class="text-danger">*</span>
              </label>

              <select name="storage_detail_id" id="storage_detail_id" class="form-control" required>
                <option value="">-- Chọn từ kho --</option>

                @foreach($storageDetails as $detail)
                  <option value="{{ $detail->id }}"
                          data-name="{{ $detail->product_name ?? '' }}"
                          data-qty="{{ (int)($detail->import_quantity ?? 0) }}"
                          {{ old('storage_detail_id') == $detail->id ? 'selected' : '' }}>
                    [Lô {{ optional($detail->storage)->batch_code ?? 'N/A' }}]
                    {{ $detail->product_name ?? 'Chưa đặt tên' }}
                    - SL: {{ $detail->import_quantity ?? 0 }}
                    - TT: {{ $detail->stock_status ?? 'pending' }}
                  </option>
                @endforeach
              </select>

              <small class="text-muted">
                Chỉ hiển thị các dòng kho <strong>đang hiển thị</strong> và
                có trạng thái <strong>pending</strong>.
              </small>
            </div>

            {{-- Số lượng dùng để bán (readonly, auto-fill từ kho) --}}
            <div class="form-group">
              <label for="quantity_display">Số lượng dùng để bán</label>

              <input type="number"
                     id="quantity_display"
                     class="form-control"
                     value="{{ old('quantity') }}"
                     readonly
                     placeholder="Hãy chọn sản phẩm trong kho trước">

              {{-- Giá trị thật gửi lên server --}}
              <input type="hidden"
                     name="quantity"
                     id="quantity_real"
                     value="{{ old('quantity') }}">

              <small class="text-muted">
                Hệ thống sẽ tự động dùng số lượng đã nhập ở Kho Hàng
              </small>
            </div>

            {{-- Thương hiệu --}}
            <div class="form-group">
              <label for="brand_id">Thương hiệu <span class="text-danger">*</span></label>
              <select name="brand_id" id="brand_id" class="form-control" required>
                <option value="">-- Chọn thương hiệu --</option>
                @foreach($brands as $brand)
                  <option value="{{ $brand->id }}"
                    {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Loại đồng hồ --}}
            <div class="form-group">
              <label for="category_id">Loại đồng hồ <span class="text-danger">*</span></label>
              <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Chọn loại --</option>
                @foreach($categories as $cate)
                  <option value="{{ $cate->id }}"
                    {{ old('category_id') == $cate->id ? 'selected' : '' }}>
                    {{ $cate->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Tên sản phẩm --}}
            <div class="form-group">
              <label for="name">Tên sản phẩm hiển thị</label>
              <input type="text"
                     name="name"
                     id="name"
                     class="form-control"
                     value="{{ old('name') }}"
                     placeholder="Nếu để trống sẽ dùng tên trong kho">
            </div>

            {{-- Mô tả --}}
            <div class="form-group">
              <label for="description">Mô tả</label>
              <textarea name="description"
                        id="description"
                        rows="4"
                        class="form-control"
                        placeholder="Mô tả chi tiết sản phẩm...">{{ old('description') }}</textarea>
            </div>

            {{-- Chất liệu dây --}}
            <div class="form-group">
              <label for="strap_material">Chất liệu dây</label>
              <input type="text"
                     name="strap_material"
                     id="strap_material"
                     class="form-control"
                     value="{{ old('strap_material') }}"
                     placeholder="VD: Thép không gỉ, Da, Cao su...">
            </div>

            {{-- Kích thước mặt (mm) --}}
            <div class="form-group">
              <label for="dial_size">Kích thước mặt (mm)</label>
              <input type="number"
                     step="0.01"
                     min="0"
                     max="99.99"
                     name="dial_size"
                     id="dial_size"
                     class="form-control"
                     value="{{ old('dial_size') }}"
                     placeholder="VD: 40, 40.5">
            </div>

            {{-- Giới tính --}}
            <div class="form-group">
              <label for="gender">Giới tính</label>
              <select name="gender" id="gender" class="form-control">
                <option value="male"   {{ old('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                <option value="unisex" {{ old('gender') === 'unisex' ? 'selected' : '' }}>Unisex</option>
              </select>
            </div>

            {{-- Giá bán --}}
            <div class="form-group">
              <label for="price">Giá bán (VNĐ) <span class="text-danger">*</span></label>
              <input type="number"
                     name="price"
                     id="price"
                     class="form-control"
                     min="0"
                     max="99999999"
                     step="1000"
                     value="{{ old('price') }}"
                     required>
            </div>

            {{-- Ảnh sản phẩm --}}
            <div class="form-group">
              <label>Ảnh sản phẩm (tối đa 4 ảnh)</label>
              <div style="margin-bottom:5px;">
                <strong>Ảnh 1 (chính) <span class="text-danger">*</span></strong>
                <input type="file" name="image_1" class="form-control">
              </div>
              <div style="margin-bottom:5px;">
                <strong>Ảnh 2 (hover)</strong>
                <input type="file" name="image_2" class="form-control">
              </div>
              <div style="margin-bottom:5px;">
                <strong>Ảnh 3 (phụ)</strong>
                <input type="file" name="image_3" class="form-control">
              </div>
              <div>
                <strong>Ảnh 4 (phụ)</strong>
                <input type="file" name="image_4" class="form-control">
              </div>
            </div>

            <button type="submit" class="btn btn-info">Đăng sản phẩm</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-default">Quay lại</a>

          </form>

        </div>
      </div>
    </section>
  </div>
</div>

{{-- JS: tự đổ số lượng + tên sản phẩm theo kho --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectDetail    = document.getElementById('storage_detail_id');
    const quantityDisplay = document.getElementById('quantity_display');
    const quantityReal    = document.getElementById('quantity_real');
    const nameInput       = document.getElementById('name');

    // admin đã sửa tên hay chưa
    let nameTouched = false;

    // Nếu có old('name') => coi như đã sửa (validate fail quay lại)
    if (nameInput && nameInput.value.trim() !== '') {
        nameTouched = true;
    }

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            // nếu user xóa trống -> cho phép auto-fill lại theo kho
            nameTouched = (this.value.trim() !== '');
        });
    }

    function getSelectedOption() {
        return selectDetail.options[selectDetail.selectedIndex] || null;
    }

    function updateQuantity() {
        const opt = getSelectedOption();
        const qty = opt ? (opt.dataset.qty ?? '') : '';
        quantityDisplay.value = qty;
        quantityReal.value    = qty;
    }

    function updateName() {
        if (!nameInput) return;
        const opt = getSelectedOption();
        const storageName = opt ? (opt.dataset.name ?? '').trim() : '';

        // chỉ auto-fill khi admin chưa gõ tay
        if (!nameTouched && storageName) {
            nameInput.value = storageName;
        }
    }

    selectDetail.addEventListener('change', function () {
        updateQuantity();
        updateName();
    });

    // init nếu đã có selected (old)
    if (selectDetail.value) {
        updateQuantity();
        updateName();
    }
});
</script>

@endsection
