@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                CẬP NHẬT LÔ HÀNG
            </header>

            <div class="panel-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin-bottom: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <br>
                @endif

                <div class="position-center">
                    <form
                        role="form"
                        action="{{ URL::to('/admin/storages/'.$storage->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="product_name">Tên sản phẩm (lô hàng)</label>
                            <input
                                type="text"
                                name="product_name"
                                id="product_name"
                                class="form-control"
                                value="{{ old('product_name', $storage->product_name) }}"
                                placeholder="Nhập tên sản phẩm của lô hàng"
                                required
                            >
                        </div>

                    
                        <div class="form-group">
                            <label for="supplier_name">Nhà cung cấp</label>
                            <input
                                type="text"
                                name="supplier_name"
                                id="supplier_name"
                                class="form-control"
                                value="{{ old('supplier_name', $storage->supplier_name) }}"
                                placeholder="Nhập tên nhà cung cấp (nếu có)"
                            >
                        </div>

                  
                        <div class="form-group">
                            <label for="import_date">Ngày nhập</label>
                            <input
                                type="date"
                                name="import_date"
                                id="import_date"
                                class="form-control"
                                value="{{ old('import_date', \Carbon\Carbon::parse($storage->import_date)->format('Y-m-d')) }}"
                                required
                            >
                        </div>

                
                        <div class="form-group">
                            <label for="import_quantity">
                                Số lượng nhập
                                @if($storage->product)
                                    <small class="text-muted">
                                        (Đang đăng bán, không thể sửa SL nhập)
                                    </small>
                                @endif
                            </label>

                            @if(!$storage->product)
                                <input
                                    type="number"
                                    name="import_quantity"
                                    id="import_quantity"
                                    class="form-control"
                                    min="1"
                                    value="{{ old('import_quantity', $storage->import_quantity) }}"
                                    required
                                >
                            @else
                                
                                <input
                                    type="number"
                                    id="import_quantity"
                                    class="form-control"
                                    value="{{ $storage->import_quantity }}"
                                    readonly
                                >
                            @endif
                        </div>

                 
                        <div class="form-group">
                            <label for="unit_import_price">Giá nhập (1 sản phẩm)</label>
                            <input
                                type="number"
                                step="1000"
                                min="0"
                                name="unit_import_price"
                                id="unit_import_price"
                                class="form-control"
                                value="{{ old('unit_import_price', $storage->unit_import_price) }}"
                                required
                            >
                        </div>

                  
                        <div class="form-group">
                            <label>Tổng tiền nhập (hiện tại)</label>
                            <input
                                type="text"
                                class="form-control"
                                id="total_import_price_display"
                                value="{{ number_format($storage->total_import_price, 0, ',', '.') }} đ"
                                disabled
                            >
                            <small class="help-block">
                                Tổng tiền sẽ tự động cập nhật khi hoàn tất chỉnh sửa.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-info">
                            Cập nhật lô hàng
                        </button>

                        <a href="{{ URL::to('/admin/storages') }}" class="btn btn-default">
                            Quay lại danh sách
                        </a>

                    </form>
                </div>

            </div>
        </section>
    </div>
</div>

@endsection
