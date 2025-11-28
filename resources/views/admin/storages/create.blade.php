@extends('pages.admin_layout')
@section('admin_content')



<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading" style="color:#000; font-weight:600;">
                Thêm lô hàng mới
            </header>

            <div class="panel-body">
                <div class="position-center">

                    <form role="form" method="POST" action="{{ URL::to('/admin/storages') }}">
                        @csrf
                   
                        <div class="form-group">
                            <label for="product_name">Tên sản phẩm</label>
                            <input type="text"
                                name="product_name"
                                class="form-control"
                                id="product_name"
                                placeholder="Nhập tên sản phẩm"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="supplier_name">Nhà cung cấp</label>
                            <input type="text"
                                name="supplier_name"
                                class="form-control"
                                id="supplier_name"
                                placeholder="Tên nhà cung cấp (nếu có)">
                        </div>

                        <div class="form-group">
                            <label for="import_date">Ngày nhập hàng</label>
                            <input type="date"
                                name="import_date"
                                class="form-control"
                                id="import_date"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="import_quantity">Số lượng nhập</label>
                            <input type="number"
                                min="1"
                                name="import_quantity"
                                class="form-control"
                                id="import_quantity"
                                placeholder="Nhập số lượng"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="unit_import_price">Giá nhập (1 sản phẩm)</label>
                            <input type="number"
                                step="0.01"
                                min="0"
                                name="unit_import_price"
                                class="form-control"
                                id="unit_import_price"
                                placeholder="VD: 1500000"
                                required>
                        </div>

                        <button type="submit" class="btn btn-info">Thêm Lô Hàng</button>

                    </form>

                </div>
            </div>

        </section>
    </div>
</div>

@endsection