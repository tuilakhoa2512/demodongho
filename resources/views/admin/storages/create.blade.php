@extends('pages.admin_layout')
@section('admin_content')

<style>
    /* Làm input rõ hơn */
    .form-control {
        background-color: #fff !important;
        color: #000 !important;
        border: 1px solid #ccc !important;
    }

    .form-control:focus {
        border-color: #66afe9 !important;
        box-shadow: 0 0 5px rgba(102, 175, 233, 0.6) !important;
        background-color: #fff !important;
        color: #000 !important;
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
                Thêm lô hàng mới
            </header>

            <div class="panel-body">
                <div class="position-center">

                    <form role="form" method="POST" action="{{ URL::to('/admin/storages') }}">
                        @csrf

                        {{-- TÊN SẢN PHẨM --}}
                        <div class="form-group">
                            <label for="product_name">Tên sản phẩm</label>
                            <input type="text"
                                   name="product_name"
                                   class="form-control"
                                   id="product_name"
                                   placeholder="Nhập tên sản phẩm"
                                   required>
                        </div>

                        {{-- NHÀ CUNG CẤP --}}
                        <div class="form-group">
                            <label for="supplier_name">Nhà cung cấp</label>
                            <input type="text"
                                   name="supplier_name"
                                   class="form-control"
                                   id="supplier_name"
                                   placeholder="Tên nhà cung cấp (nếu có)">
                        </div>

                        {{-- NGÀY NHẬP --}}
                        <div class="form-group">
                            <label for="import_date">Ngày nhập hàng</label>
                            <input type="date"
                                   name="import_date"
                                   class="form-control"
                                   id="import_date"
                                   required>
                        </div>

                        {{-- SỐ LƯỢNG --}}
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

                        {{-- GIÁ NHẬP 1 SP --}}
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
