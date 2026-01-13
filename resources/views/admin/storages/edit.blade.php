@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading" style="color:#000; font-weight:600;">
                Chỉnh sửa lô hàng: {{ $storage->batch_code }}
            </header>

            <div class="panel-body">
                <div class="position-center">
                    <form role="form"
                          method="POST"
                          action="{{ route('admin.storages.update', $storage->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Mã lô hàng: chỉ hiển thị, không cho sửa --}}
                        <div class="form-group">
                            <label for="batch_code">Mã lô hàng</label>
                            <input type="text"
                                   id="batch_code"
                                   class="form-control"
                                   value="{{ $storage->batch_code }}"
                                   readonly>
                            <small class="text-muted">
                                Mã lô được hệ thống tạo tự động, không thể chỉnh sửa.
                            </small>
                        </div>

                        {{-- Nhà cung cấp --}}
                        <div class="form-group">
                            <label for="supplier_name">Tên nhà cung cấp</label>
                            <input type="text"
                                   name="supplier_name"
                                   id="supplier_name"
                                   class="form-control"
                                   value="{{ old('supplier_name', $storage->supplier_name) }}"
                                   maxlength="50"
                                   placeholder="Tên nhà cung cấp (tối đa 50 ký tự)">
                        </div>

                        {{-- Email nhà cung cấp --}}
                        <div class="form-group">
                            <label for="supplier_email">Email nhà cung cấp</label>
                            <input type="email"
                                   name="supplier_email"
                                   id="supplier_email"
                                   class="form-control"
                                   value="{{ old('supplier_email', $storage->supplier_email) }}"
                                   maxlength="30"
                                   placeholder="VD: tencongty@gmail.com">
                        </div>

                        {{-- Ngày nhập --}}
                        <div class="form-group">
                            <label for="import_date">Ngày nhập hàng</label>
                            <input type="date"
                                   name="import_date"
                                   id="import_date"
                                   class="form-control"
                                   value="{{ old('import_date', optional($storage->import_date)->format('Y-m-d')) }}">
                            <small class="text-muted">
                                Nếu bỏ trống, hệ thống giữ nguyên ngày nhập hiện tại.
                            </small>
                        </div>

                        {{-- Ghi chú --}}
                        <div class="form-group">
                            <label for="note">Ghi chú</label>
                            <textarea name="note"
                                      id="note"
                                      class="form-control"
                                      rows="3"
                                      maxlength="200"
                                      placeholder="Ghi chú thêm cho lô hàng (tối đa 200 ký tự)">{{ old('note', $storage->note) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-info">Cập nhật Lô Hàng</button>
                        <a href="{{ route('admin.storages.index') }}" class="btn btn-default">
                            Quay lại
                        </a>

                    </form>

                </div>
            </div>

        </section>
    </div>
</div>

@endsection
