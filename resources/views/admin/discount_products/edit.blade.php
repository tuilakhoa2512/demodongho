@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Chỉnh Sửa Chương Trình Ưu Đãi: {{ $discount->name }}
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

          <form method="POST" action="{{ route('admin.discount-products.update', $discount->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
              <label for="name">Tên chương trình <span class="text-danger">*</span></label>
              <input type="text"
                     name="name"
                     id="name"
                     class="form-control"
                     value="{{ old('name', $discount->name) }}"
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
                     value="{{ old('rate', $discount->rate) }}"
                     required>
            </div>

            <div class="form-group">
              <label>Trạng thái</label><br>
              <label class="radio-inline">
                <input type="radio" name="status" value="1"
                       {{ old('status', $discount->status) == 1 ? 'checked' : '' }}>
                Đang hoạt động
              </label>
              <label class="radio-inline" style="margin-left:15px;">
                <input type="radio" name="status" value="0"
                       {{ old('status', $discount->status) == 0 ? 'checked' : '' }}>
                Tắt
              </label>
            </div>

            <button type="submit" class="btn btn-info">Lưu thay đổi</button>
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
