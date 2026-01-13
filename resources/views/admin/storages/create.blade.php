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
                    <form role="form" method="POST" action="{{ route('admin.storages.store') }}">
                         @csrf

                        <div class="form-group">
                            <label for="supplier_name">Tên nhà cung cấp</label>
                            <input type="text"
                                name="supplier_name"
                                class="form-control"
                                id="supplier_name"
                                value="{{ old('supplier_name') }}"
                                placeholder="Nhập tên nhà cung cấp (tối đa 50 ký tự)"
                                maxlength="50">
                        </div>

                        <div class="form-group">
                            <label for="supplier_email">Email nhà cung cấp</label>
                            <input type="email"
                                name="supplier_email"
                                class="form-control"
                                id="supplier_email"
                                value="{{ old('supplier_email') }}"
                                placeholder="Email phải có đuôi @gmail.com"
                                pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                                title="Vui lòng nhập email có đuôi @gmail.com"
                                maxlength="30">
                        </div>

                        <div class="form-group">
                            <label for="import_date">Ngày nhập hàng</label>
                            <input type="date"
                                name="import_date"
                                class="form-control"
                                id="import_date"
                                value="{{ old('import_date') }}">
                        </div>

                        <div class="form-group">
                            <label for="note">Ghi chú</label>
                            <textarea name="note"
                                    id="note"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Tối đa 200 ký tự"
                                    maxlength="200">{{ old('note') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-info">Thêm Lô Hàng</button>
                        <a href="{{ route('admin.storages.index') }}" class="btn btn-default">Quay lại</a>
                    </form>


                </div>
            </div>

        </section>
    </div>
</div>


@endsection
