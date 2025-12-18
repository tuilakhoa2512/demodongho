@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Sửa ưu đãi: {{ $discountBill->name }}
      </header>

      <div class="panel-body">
        <div class="position-center">

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul style="margin-bottom:0;">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.discount-bills.update', $discountBill->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
              <label for="name">Tên ưu đãi <span class="text-danger">*</span></label>
              <input type="text"
                     name="name"
                     id="name"
                     class="form-control"
                     value="{{ old('name', $discountBill->name) }}"
                     maxlength="150"
                     required>
            </div>

            <div class="form-group">
              <label for="min_subtotal">Ngưỡng tối thiểu (VNĐ) <span class="text-danger">*</span></label>
              <input type="number"
                     name="min_subtotal"
                     id="min_subtotal"
                     class="form-control"
                     value="{{ old('min_subtotal', $discountBill->min_subtotal) }}"
                     min="0"
                     step="1000"
                     required>
              <small class="text-muted">Ví dụ: 1000000 (tức 1.000.000đ)</small>
            </div>

            <div class="form-group">
              <label for="rate">% giảm <span class="text-danger">*</span></label>
              <input type="number"
                     name="rate"
                     id="rate"
                     class="form-control"
                     value="{{ old('rate', $discountBill->rate) }}"
                     min="1"
                     max="100"
                     required>
            </div>

            {{-- Không cho sửa status ở đây để tránh rối, status dùng icon toggle ở index --}}
            <div class="form-group">
              <label>Trạng thái hiện tại</label><br>
              @if($discountBill->status)
                <span class="label label-success">Hoạt động</span>
              @else
                <span class="label label-default">Tắt</span>
              @endif
              <small class="text-muted" style="display:block;margin-top:6px;">
                Trạng thái ưu đãi được bật/tắt ở trang danh sách.
              </small>
            </div>

            <button type="submit" class="btn btn-info">Lưu</button>
            <a href="{{ route('admin.discount-bills.index') }}" class="btn btn-default">Quay lại</a>
          </form>

        </div>
      </div>
    </section>
  </div>
</div>

@endsection
