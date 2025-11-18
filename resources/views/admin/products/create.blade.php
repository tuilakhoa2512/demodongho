@extends('pages.admin_layout')
@section('admin_content')

<style>
    .form-control {
        background-color: #fff !important;
        color: #000 !important;
        border: 1px solid #ccc !important;
    }
    .form-control:focus {
        border-color: #66afe9 !important;
        box-shadow: 0 0 5px rgba(102, 175, 233, 0.6) !important;
    }
    label {
        color: #000;
        font-weight: 500;
    }
    section.panel {
        background: #fff !important;
        color: #000 !important;
        border-radius: 6px;
        padding: 20px;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading" style="color:#000; font-weight:600;">
                Thêm sản phẩm mới
            </header>

            <div class="panel-body">
                <div class="position-center">

                    <form role="form" method="POST" action="{{ URL::to('/admin/products') }}">
                        @csrf

                        {{-- CHỌN LÔ HÀNG TỪ KHO --}}
                        <div class="form-group">
                            <label for="storage_id">Chọn lô hàng từ kho</label>
                            <select name="storage_id" id="storage_id" class="form-control" required>
                                <option value="">-- Chọn lô hàng chưa đăng bán --</option>
                                @foreach($storages as $storage)
                                    <option value="{{ $storage->id }}">
                                        Lô #{{ $storage->id }} - {{ $storage->product_name }}
                                        (SL: {{ $storage->import_quantity }},
                                         Ngày nhập: {{ \Carbon\Carbon::parse($storage->import_date)->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TÊN SẢN PHẨM HIỂN THỊ --}}
                        <div class="form-group">
                            <label for="name">Tên sản phẩm hiển thị</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   id="name"
                                   placeholder="Tên hiển thị trên website"
                                   required>
                        </div>

                        {{-- MÔ TẢ --}}
                        <div class="form-group">
                            <label for="description">Mô tả sản phẩm</label>
                            <textarea name="description"
                                      id="description"
                                      rows="5"
                                      class="form-control"
                                      placeholder="Mô tả chi tiết sản phẩm"></textarea>
                        </div>

                        {{-- GIỚI TÍNH --}}
                        <div class="form-group">
                            <label for="gender">Giới tính</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">Không xác định</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Unisex">Unisex</option>
                            </select>
                        </div>

                        {{-- KÍCH THƯỚC MẶT --}}
                        <div class="form-group">
                            <label for="dial_size">Kích thước mặt (mm)</label>
                            <input type="text"
                                   name="dial_size"
                                   class="form-control"
                                   id="dial_size"
                                   placeholder="Ví dụ: 40, 42, 36...">
                        </div>

                        {{-- CHẤT LIỆU DÂY --}}
                        <div class="form-group">
                            <label for="strap_material">Chất liệu dây</label>
                            <input type="text"
                                   name="strap_material"
                                   class="form-control"
                                   id="strap_material"
                                   placeholder="Da, thép không gỉ, cao su...">
                        </div>

                        {{-- GIÁ BÁN --}}
                        <div class="form-group">
                            <label for="price">Giá bán</label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="price"
                                   class="form-control"
                                   id="price"
                                   placeholder="VD: 2500000"
                                   required>
                        </div>

                        {{-- TRẠNG THÁI --}}
                        <div class="form-group">
                            <label for="status">Trạng thái hiển thị</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info">Thêm sản phẩm</button>

                    </form>

                </div>
            </div>

        </section>
    </div>
</div>

@endsection
