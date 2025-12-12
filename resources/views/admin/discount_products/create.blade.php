@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Thêm Chương Trình Ưu Đãi Sản Phẩm
      </header>

      <div class="panel-body">
        <div class="position-center">

          {{-- Lỗi validate --}}
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul style="margin-bottom:0;">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.discount-products.store') }}">
            @csrf

            <div class="form-group">
              <label for="name">Tên chương trình <span class="text-danger">*</span></label>
              <input type="text"
                     name="name"
                     id="name"
                     class="form-control"
                     value="{{ old('name') }}"
                     required>
            </div>

            <div class="form-group">
              <label for="rate">% Giảm giá <span class="text-danger">*</span></label>
              <input type="number"
                     name="rate"
                     id="rate"
                     class="form-control"
                     min="1"
                     max="100"
                     value="{{ old('rate') }}"
                     required>
              <small class="text-muted">Phần trăm giảm giá từ 1 đến 100.</small>
            </div>

            <div class="form-group">
              <label>Trạng thái</label><br>
              <label class="radio-inline">
                <input type="radio" name="status" value="1"
                       {{ old('status', 1) == 1 ? 'checked' : '' }}>
                Đang hoạt động
              </label>
              <label class="radio-inline" style="margin-left:15px;">
                <input type="radio" name="status" value="0"
                       {{ old('status', 1) == 0 ? 'checked' : '' }}>
                Tắt
              </label>
            </div>

            <button type="submit" class="btn btn-info">Lưu</button>
            <a href="{{ route('admin.discount-products.index') }}" class="btn btn-default">
              Quay lại
            </a>

          </form>

        </div>
      </div>
    </section>
  </div>
</div>

@endsection
