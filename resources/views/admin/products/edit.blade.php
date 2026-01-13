@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading" style="color:#000; font-weight:600;">
                Chỉnh sửa sản phẩm: {{ $product->name }}
            </header>

            <div class="panel-body">
                <div class="position-center">
                    {{-- Thông tin lô & kho (chỉ xem) --}}
                    @php
                        $storageDetail = optional($product->storageDetail);
                        $storage       = optional($storageDetail->storage);
                    @endphp

                    <div class="alert alert-info">
                        <strong>Thông tin lô & kho:</strong><br>
                        Mã lô:
                        <strong>{{ $storage->batch_code ?? 'N/A' }}</strong><br>
                        Sản phẩm trong kho:
                        <strong>{{ $storageDetail->product_name ?? 'N/A' }}</strong><br>
                        Số lượng nhập kho ban đầu:
                        <strong>{{ $storageDetail->import_quantity ?? 0 }}</strong><br>
                        Trạng thái kho:
                        <strong>{{ $storageDetail->stock_status ?? 'N/A' }}</strong>
                    </div>

                    <form method="POST"
                          action="{{ route('admin.products.update', $product->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Thương hiệu --}}
                        <div class="form-group">
                            <label for="brand_id">Thương hiệu <span class="text-danger">*</span></label>
                            <select name="brand_id" id="brand_id" class="form-control" required>
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
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
                                        {{ old('category_id', $product->category_id) == $cate->id ? 'selected' : '' }}>
                                        {{ $cate->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tên sản phẩm --}}
                        <div class="form-group">
                            <label for="name">Tên sản phẩm hiển thị <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control"
                                   value="{{ old('name', $product->name) }}"
                                   required>
                        </div>

                        {{-- Mô tả --}}
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea name="description"
                                      id="description"
                                      rows="4"
                                      class="form-control"
                                      placeholder="Mô tả chi tiết sản phẩm...">{{ old('description', $product->description) }}</textarea>
                        </div>

                        {{-- Chất liệu dây --}}
                        <div class="form-group">
                            <label for="strap_material">Chất liệu dây</label>
                            <input type="text"
                                   name="strap_material"
                                   id="strap_material"
                                   class="form-control"
                                   value="{{ old('strap_material', $product->strap_material) }}"
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
                                   value="{{ old('dial_size', $product->dial_size) }}"
                                   placeholder="VD: 40, 40.5">
                        </div>

                        {{-- Giới tính --}}
                        <div class="form-group">
                            <label for="gender">Giới tính</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">-- Không phân loại --</option>
                                <option value="male"   {{ old('gender', $product->gender) === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender', $product->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="unisex" {{ old('gender', $product->gender) === 'unisex' ? 'selected' : '' }}>Unisex</option>
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
                                   step="1000"
                                   value="{{ old('price', $product->price) }}"
                                   required>
                        </div>

                        {{-- Số lượng tồn kho (chỉ xem, không submit) --}}
                        <div class="form-group">
                            <label>Số lượng tồn kho hiện tại</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $product->quantity }}"
                                   readonly>
                            <small class="text-muted">
                                Số lượng này được cập nhật tự động theo nhập / xuất kho (đơn hàng),
                                không thể chỉnh sửa trực tiếp tại đây.
                            </small>
                        </div>

                        {{-- Trạng thái bán (stock_status) --}}
                        <div class="form-group">
                            <label for="stock_status">Trạng thái bán</label>
                            <select name="stock_status" id="stock_status" class="form-control">
                                <option value="selling"
                                    {{ old('stock_status', $product->stock_status) === 'selling' ? 'selected' : '' }}>
                                    Đang bán
                                </option>
                                <option value="sold_out"
                                    {{ old('stock_status', $product->stock_status) === 'sold_out' ? 'selected' : '' }}>
                                    Hết hàng
                                </option>
                                <option value="stopped"
                                    {{ old('stock_status', $product->stock_status) === 'stopped' ? 'selected' : '' }}>
                                    Ngừng bán
                                </option>
                            </select>
                            <small class="text-muted">
                                Thay đổi trạng thái bán sẽ tự đồng bộ sang Kho Hàng (Storage Detail).
                            </small>
                        </div>

                        {{-- Ảnh sản phẩm --}}
                        <div class="form-group">
                            <label>Ảnh sản phẩm</label>

                            @php
                                $images = $product->productImage;
                            @endphp

                            <div class="row">

                                {{-- Ảnh 1 --}}
                                <div class="col-sm-6" style="margin-bottom:15px;">
                                    <label>Ảnh 1 (chính)</label><br>
                                    @if($images && $images->image_1)
                                        <img src="{{ asset('storage/' . $images->image_1) }}"
                                             alt="image_1"
                                             style="width:100px; height:100px; object-fit:cover; border-radius:4px; margin-bottom:5px;">
                                    @else
                                        <p><em>Chưa có ảnh.</em></p>
                                    @endif
                                    <input type="file" name="image_1" class="form-control">
                                </div>

                                {{-- Ảnh 2 --}}
                                <div class="col-sm-6" style="margin-bottom:15px;">
                                    <label>Ảnh 2 (hover)</label><br>
                                    @if($images && $images->image_2)
                                        <img src="{{ asset('storage/' . $images->image_2) }}"
                                             alt="image_2"
                                             style="width:100px; height:100px; object-fit:cover; border-radius:4px; margin-bottom:5px;">
                                    @else
                                        <p><em>Chưa có ảnh.</em></p>
                                    @endif
                                    <input type="file" name="image_2" class="form-control">
                                </div>

                                {{-- Ảnh 3 --}}
                                <div class="col-sm-6" style="margin-bottom:15px;">
                                    <label>Ảnh 3 (phụ)</label><br>
                                    @if($images && $images->image_3)
                                        <img src="{{ asset('storage/' . $images->image_3) }}"
                                             alt="image_3"
                                             style="width:100px; height:100px; object-fit:cover; border-radius:4px; margin-bottom:5px;">
                                    @else
                                        <p><em>Chưa có ảnh.</em></p>
                                    @endif
                                    <input type="file" name="image_3" class="form-control">
                                </div>

                                {{-- Ảnh 4 --}}
                                <div class="col-sm-6" style="margin-bottom:15px;">
                                    <label>Ảnh 4 (phụ)</label><br>
                                    @if($images && $images->image_4)
                                        <img src="{{ asset('storage/' . $images->image_4) }}"
                                             alt="image_4"
                                             style="width:100px; height:100px; object-fit:cover; border-radius:4px; margin-bottom:5px;">
                                    @else
                                        <p><em>Chưa có ảnh.</em></p>
                                    @endif
                                    <input type="file" name="image_4" class="form-control">
                                </div>
                            </div>

                            <small class="text-muted">
                                Nếu không chọn file mới, ảnh cũ sẽ được giữ nguyên.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-info">Lưu thay đổi</button>
                        {{-- Nút quay lại: về trang chi tiết sản phẩm --}}
                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-default">
                            Quay lại
                        </a>

                    </form>

                </div>
            </div>
        </section>
    </div>
</div>

@endsection
