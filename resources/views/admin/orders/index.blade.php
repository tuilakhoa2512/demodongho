@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading">
      Danh sách đơn hàng
      @if($filterStatus !== '')
        <small>(Trạng Thái: {{ $statuses[$filterStatus] ?? $filterStatus }})</small>
      @endif
    </div>

    @if (session('success'))
      <script>
        Swal.fire({
          title: "Thành công!",
          text: "{{ session('success') }}",
          icon: "success",
          confirmButtonText: "OK",
        });
      </script>
    @endif

    @if (session('error'))
      <div class="alert alert-danger" style="margin:15px;">
        {{ session('error') }}
      </div>
    @endif

    <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">
        <form method="GET" action="{{ URL::to('/admin/orders') }}" class="form-inline">
          <select name="status" class="input-sm form-control w-sm inline v-middle">
            <option value="">Lọc trạng thái (Tất cả)</option>
            @foreach($statuses as $key => $label)
              <option value="{{ $key }}" {{ $filterStatus === $key ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>

          <input type="hidden" name="keyword" value="{{ $keyword }}">

          <button type="submit" class="btn btn-sm btn-default" style="margin-left:5px;">
            Áp dụng
          </button>
        </form>
      </div>

      <div class="col-sm-4"></div>

      <div class="col-sm-3">
        <form method="GET" action="{{ URL::to('/admin/orders') }}">
          <div class="input-group">
            <input type="text"
                   name="keyword"
                   value="{{ $keyword }}"
                   class="input-sm form-control"
                   placeholder="Tìm mã đơn / người nhận / SĐT...">
            <input type="hidden" name="status" value="{{ $filterStatus }}">
            <span class="input-group-btn">
              <button class="btn btn-sm btn-default" type="submit">Tìm</button>
            </span>
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th style="width:20px;"></th>
            <th>ID</th>
            <th>Mã đơn</th>
            <th>Người nhận</th>
            <th>SĐT</th>
            <th>Thanh toán</th>
            <th>Tổng tiền</th>
            <th>Ngày đặt</th>
            <th style="width:120px;">Trạng thái</th>
            <th style="width:140px;">Đổi trạng thái</th>
            <th style="width:60px;">Xem</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($orders as $o)
            @php
              $st = $o->status ?? 'pending';

              $badgeClass = match($st) {
                'confirmed' => 'st-confirmed',
                'shipping'  => 'st-shipping',
                'success'   => 'st-success',
                'canceled'  => 'st-canceled',
                default     => 'st-pending',
              };

              $receiverName  = $o->receiver_name  ?? ($o->user_fullname ?? '—');
              $receiverPhone = $o->receiver_phone ?? '—';

              $promoDiscount = (int)($o->promo_discount_amount ?? 0);
              $promoCode     = $o->promo_code ?? null;
              $promoName     = $o->promo_campaign_name ?? null;
            @endphp

            <tr>
              <td></td>

              <td>{{ $o->id }}</td>

              <td><b>{{ $o->order_code }}</b></td>

              <td>{{ $receiverName }}</td>
              <td>{{ $receiverPhone }}</td>

              <td>{{ $o->payment_method ?? '—' }}</td>

              <td style="color:#e60012; font-weight:800;">
                {{ number_format((float)$o->total_price, 0, ',', '.') }} đ
              </td>

              <td>
                @if($o->created_at)
                  <div style="line-height:1.2;">
                    <div>{{ \Carbon\Carbon::parse($o->created_at)->format('H:i') }}</div>
                    <div style="font-size:12px; color:#777; font-weight: bold;">
                      {{ \Carbon\Carbon::parse($o->created_at)->format('d/m/Y') }}
                    </div>
                  </div>
                @else
                  —
                @endif
              </td>

              <td>
                <span class="label-status {{ $badgeClass }}">
                  {{ $statuses[$st] ?? $st }}
                </span>
              </td>

              <td>
                <form method="POST"
                      action="{{ URL::to('/admin/orders/'.$o->order_code.'/status') }}"
                      data-order="{{ $o->order_code }}"
                      data-current="{{ $st }}"
                      style="margin:0;">
                  @csrf

                  <select name="status"
                          class="form-control input-sm order-status-select js-status-select">
                    @foreach($statuses as $key => $label)
                      <option value="{{ $key }}" {{ $st === $key ? 'selected' : '' }}>
                        {{ $label }}
                      </option>
                    @endforeach
                  </select>
                </form>
              </td>

              <td>
                <a href="{{ URL::to('/admin/orders/'.$o->order_code) }}" title="Chi tiết">
                  <i class="fa fa-eye text-info" style="font-size:18px;"></i>
                </a>
              </td>

            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-5 text-center">
          <small class="text-muted inline m-t-sm m-b-sm">
            Hiển thị {{ $orders->firstItem() }} - {{ $orders->lastItem() }}
            / {{ $orders->total() }} đơn hàng
          </small>
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $orders->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </footer>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.js-status-select').forEach(select => {
    select.addEventListener('change', function () {
      const form = this.closest('form');
      const current = form.dataset.current;
      const next = this.value;
      const orderCode = form.dataset.order;

      if (current === next) return;

      Swal.fire({
        title: 'Xác nhận',
        html: `Đổi trạng thái đơn <b>${orderCode}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đổi',
        cancelButtonText: 'Hủy'
      }).then(result => {
        if (result.isConfirmed) {
          form.submit();
        } else {
          this.value = current;
        }
      });
    });
  });
});
</script>

<style>
table td, table th {
  text-align: center !important;
  vertical-align: middle !important;
}

/* badge trạng thái */
.label-status {
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
  display: inline-block;
  white-space: nowrap;
}

.st-pending   { background:#eee;     color:#333; }
.st-confirmed { background:#5bc0de;  color:#fff; }
.st-shipping  { background:#f0ad4e;  color:#fff; }
.st-success   { background:#5cb85c;  color:#fff; }
.st-canceled  { background:#d9534f;  color:#fff; }

/* dropdown đổi trạng thái gọn */
.order-status-select {
  width: 130px;
  min-width: 130px;
  height: 32px;
  padding: 4px 6px;
  font-size: 13px;
}
</style>

@endsection
