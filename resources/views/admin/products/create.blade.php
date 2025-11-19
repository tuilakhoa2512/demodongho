@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading" style="font-size: 18px; font-weight: 600;">
                Thêm Sản Phẩm Mới
            </header>

            <div class="panel-body">
                <div class="position-center">

                    <form role="form"
                        method="POST"
                        action="{{ URL::to('/admin/products') }}"
                        enctype="multipart/form-data">
                        @csrf

                       
                        <div class="form-group">
                            <label for="storage_id">Chọn lô hàng từ Kho</label>
                            <select name="storage_id" id="storage_id" class="form-control" required style="color:black;">
                                <option value="">-- Chọn lô hàng --</option>
                                @foreach($storages as $item)
                                    <option value="{{ $item->id }}">
                                        Lô #{{ $item->id }} – {{ $item->product_name }}
                                        (SL: {{ $item->import_quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                       
                        <div class="form-group">
                            <label for="quantity">Số lượng (từ kho)</label>
                            <input type="number" id="quantity" class="form-control" readonly style="color:black;">
                        </div>

                       
                        <div class="form-group">
                            <label for="category_id">Loại sản phẩm</label>
                            <select name="category_id" id="category_id" class="form-control" required style="color:black;">
                                <option value="">-- Chọn loại sản phẩm --</option>
                                @foreach($productTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                       
                        <div class="form-group">
                            <label for="brand_id">Thương hiệu</label>
                            <select name="brand_id" id="brand_id" class="form-control" required style="color:black;">
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="name">Tên sản phẩm</label>
                            <input type="text" name="name" class="form-control" placeholder="VD: Rolex DateJust 41mm"
                                   required style="color:black;">
                        </div>

                        <div class="form-group">
                            <label for="description">Mô tả sản phẩm</label>
                            <textarea name="description" rows="5" class="form-control" style="resize:none; color:black;"
                                      placeholder="Mô tả chi tiết sản phẩm"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="gender">Giới tính</label>
                            <select name="gender" class="form-control" required style="color:black;">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="unisex">Unisex</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dial_size">Kích thước mặt (mm)</label>
                            <input type="number"
                                step="0.01"
                                min="0"
                                name="dial_size"
                                id="dial_size"
                                class="form-control"
                                style="color:black;"
                                placeholder="VD: 40.5">
                        </div>


                        <div class="form-group">
                            <label for="strap_material">Chất liệu dây</label>
                            <input type="text" name="strap_material" class="form-control" style="color:black;"
                                   placeholder="VD: Da, Cao su, Thép không gỉ">
                        </div>

                  
                        <div class="form-group">
                            <label for="price">Giá bán (VNĐ)</label>
                            <input type="number" name="price" min="0" class="form-control"
                                   placeholder="VD: 2500000" required style="color:black;">
                        </div>

                        <div class="form-group">
                            <label>Ảnh 1 (ảnh chính)</label>
                            <input type="file" name="image_1" accept="image/*" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Ảnh 2</label>
                            <input type="file" name="image_2" accept="image/*" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Ảnh 3</label>
                            <input type="file" name="image_3" accept="image/*" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Ảnh 4</label>
                            <input type="file" name="image_4" accept="image/*" class="form-control">
                        </div>


                        <button type="submit" class="btn btn-info">Thêm sản phẩm</button>

                    </form>

                </div>
            </div>
        </section>
    </div>
</div>

{{-- JS để tự động hiển thị số lượng nhập từ kho --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const storageSelect = document.getElementById('storage_id');
        const quantityInput = document.getElementById('quantity');

        // map { storage_id : import_quantity }
        const storageQuantities = @json($storages->pluck('import_quantity', 'id'));

        storageSelect.addEventListener('change', function () {
            const selectedId = this.value;

            if (storageQuantities[selectedId]) {
                quantityInput.value = storageQuantities[selectedId];
            } else {
                quantityInput.value = '';
            }
        });
    });
</script>

@endsection
