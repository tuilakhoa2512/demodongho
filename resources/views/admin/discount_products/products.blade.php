@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="color:#000; font-weight:600;">
      Quản lý sản phẩm thuộc ưu đãi:
      <span class="text-primary">{{ $discount->name }}</span>
      ({{ $discount->rate }}%)
      @if(!$discount->status)
        <span class="label label-default" style="margin-left:10px;">Chương trình đang TẮT</span>
      @endif
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin:15px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger" style="margin:15px;">{{ session('error') }}</div>
    @endif

    {{-- FORM GÁN SẢN PHẨM --}}
    <div style="margin:15px; padding:15px; border:1px solid #eee; border-radius:4px;">
      <form method="POST" action="{{ route('admin.discount-products.products.attach', $discount->id) }}" class="form-inline">
        @csrf

        <div class="form-group" style="margin-right:10px;">
          <label style="margin-right:6px;">Chọn sản phẩm</label>
          <select name="product_id" class="form-control" required>
            <option value="">-- Chọn --</option>
            @foreach($availableProducts as $p)
              <option value="{{ $p->id }}">
                #{{ $p->id }} - {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group" style="margin-right:10px;">
          <label style="margin-right:6px;">Hạn đến</label>
          <input type="date" name="expiration_date" class="form-control">
        </div>

        <button type="submit" class="btn btn-info btn-sm">Gán vào ưu đãi</button>
        <a href="{{ route('admin.discount-products.index') }}" class="btn btn-default btn-sm" style="margin-left:6px;">
          Quay lại danh sách ưu đãi
        </a>
      </form>

      @if ($errors->any())
        <div class="alert alert-danger" style="margin-top:10px;">
          <ul style="margin-bottom:0;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>

    {{-- DANH SÁCH SẢN PHẨM ĐÃ GÁN --}}
    <div class="table-responsive" style="margin-top:10px;">
      <style>
        table td, table th { text-align:center !important; vertical-align:middle !important; }
      </style>

      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>ID SP</th>
            <th>Hình</th>
            <th>Tên sản phẩm</th>
            <th>Thời Hạn</th>
            <th>Trạng thái hạn</th>
            <th>Áp dụng</th>
            <th style="width:140px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse($attached as $p)
            @php
              $exp = $p->pivot->expiration_date; // YYYY-MM-DD hoặc null
              $isExpired = $exp && $exp < now()->toDateString();
              $pivotOn = ((int)$p->pivot->status === 1);
              $discountOff = ((int)$discount->status === 0);
            @endphp

            <tr>
              <td>{{ $p->id }}</td>

              <td>
                @if($p->productImage && $p->productImage->image_1)
                  <img src="{{ asset('storage/' . $p->productImage->image_1) }}"
                       style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                @else
                  —
                @endif
              </td>

              <td>{{ $p->name }}</td>

              <td>
                {{-- cập nhật expiration_date --}}
                <form method="POST"
                      action="{{ route('admin.discount-products.products.update', [$discount->id, $p->id]) }}"
                      style="display:inline-block;">
                  @csrf
                  @method('PUT')

                  <input type="date"
                         name="expiration_date"
                         value="{{ $exp }}"
                         class="form-control input-sm"
                         style="width:160px; display:inline-block;">

                  <button type="submit" class="btn btn-xs btn-warning" title="Lưu hạn">
                    <i class="fa fa-save"></i>
                  </button>
                </form>
              </td>

              <td>
                @if(is_null($exp) || $exp === '')
                  <span class="label label-default">Không giới hạn</span>
                @elseif($isExpired)
                  <span class="label label-danger">Hết hạn</span>
                @else
                  <span class="label label-success">Còn hạn</span>
                @endif
              </td>

              <td>
                @if((int)$p->pivot->status === 1)
                    <span class="label label-success">Đang áp dụng</span>
                @else
                    <span class="label label-default">Ngừng</span>
                @endif
            </td>


              <td>
                {{-- Toggle pivot --}}
                @php
                  // Khóa bật nếu: chương trình tắt hoặc đã hết hạn
                  $disableToggleOn = (!$pivotOn) && ($discountOff || $isExpired);
                @endphp

                @if($disableToggleOn)
                  <button type="button"
                          title="{{ $discountOff ? 'Loại Ưu Đãi đang tắt. Không thể áp dụng' : 'Ưu đãi đã hết hạn. Không thể bật' }}"
                          style="background:none;border:none;padding:0; cursor:not-allowed; opacity:0.5;">
                    <i class="fa fa-eye text-warning" style="font-size:18px;"></i>
                  </button>
                @else
                  <form action="{{ route('admin.discount-products.products.toggle', [$discount->id, $p->id]) }}"
                        method="POST"
                        style="display:inline-block;">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            title="{{ $pivotOn ? 'Tắt áp dụng' : 'Bật áp dụng' }}"
                            style="background:none;border:none;padding:0;">
                      @if($pivotOn)
                        <i class="fa fa-eye-slash text-warning" style="font-size:18px;"></i>
                      @else
                        <i class="fa fa-eye text-warning" style="font-size:18px;"></i>
                      @endif
                    </button>
                  </form>
                @endif
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Chưa có sản phẩm nào được gán vào chương trình này.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-12 text-right text-center-xs">
          {{ $attached->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </footer>

  </div>
</div>

@endsection
