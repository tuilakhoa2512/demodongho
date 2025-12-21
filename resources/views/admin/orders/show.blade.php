@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        Chi tiết đơn hàng: <b style="color:#e60012;">{{ $order->order_code }}</b>
      </div>
      <a href="{{ URL::to('/admin/orders') }}" class="btn btn-sm btn-default">
        ← Quay lại danh sách
      </a>
    </div>

  

    @php
      $st = $order->status ?? 'pending';
      $badgeClass = match($st) {
        'confirmed' => 'st-confirmed',
        'shipping'  => 'st-shipping',
        'success'   => 'st-success',
        'canceled'  => 'st-canceled',
        default     => 'st-pending',
      };

      $receiverName  = $order->receiver_name ?? ($order->user_fullname ?? '—');
      $receiverEmail = $order->receiver_email ?? ($order->user_email ?? '—');
      $receiverPhone = $order->receiver_phone ?? '—';
      $receiverAddr  = $order->receiver_address ?? '—';
    @endphp

    {{-- THÔNG TIN ĐƠN + GIAO HÀNG --}}
    <div class="panel-body">

      <div class="row-flex">
        <div class="col-box box">
          <h4 style="margin-top:0;">Thông Tin Đơn Hàng</h4> <br>

          <p><span class="k">Mã đơn:</span> <span class="v">{{ $order->order_code }}</span></p>
          <p><span class="k">Ngày đặt:</span>
            <span class="v">
            {{ $order->created_at
                ? \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y')
                : '—'
            }}
            </span>
          </p>

          <p><span class="k">Thanh toán:</span> <span class="v">{{ $order->payment_method ?? '—' }}</span></p>

          <p>
            <span class="k">Trạng thái:</span>
            <span class="label-status {{ $badgeClass }}">{{ $statuses[$st] ?? $st }}</span>
          </p>

          {{-- ĐỔI TRẠNG THÁI NGAY TRONG SHOW --}}
          <p>
          <form method="POST" action="{{ URL::to('/admin/orders/'.$order->order_code.'/status') }}" style="margin-top:10px;">
            @csrf
            <div style="display:flex; gap:8px; align-items:center;">
            <span class="k"> Cập nhật Trạng thái:</span>
              <select name="status" class="form-control input-sm status-select" id="statusSelect">
                @foreach($statuses as $key => $label)
                  <option value="{{ $key }}" {{ $st === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
              <button class="btn btn-sm btn-primary" type="submit">Cập Nhật</button>
            </div>
          </form>
          </p>

        </div>

        <div class="col-box box">
          <h4 style="margin-top:0;">Thông Tin Giao Hàng</h4> <br>

          <p><span class="k">Người nhận:</span> <span class="v">{{ $receiverName }}</span></p>
          <p><span class="k">Email:</span> <span class="v">{{ $receiverEmail }}</span></p>
          <p><span class="k">SĐT:</span> <span class="v">{{ $receiverPhone }}</span></p>
          <p><span class="k">Địa chỉ:</span> <span class="v">{{ $receiverAddr }}</span></p>

          @if(!empty($order->province_id) || !empty($order->district_id) || !empty($order->ward_id))
            <p style="margin-top:10px; color:#777;">
              (Đã lưu province/district/ward theo ID trong orders)
            </p>
          @endif
        </div>
      </div>

      {{-- CHI TIẾT SẢN PHẨM --}}
      <div class="box">
        <h4 style="margin-top:0;">Chi Tiết Đơn Hàng</h4>

        <div class="table-responsive">
          <table class="table table-striped b-t b-light">
            <thead>
              <tr>
                <th style="width:70px;">Ảnh</th>
                <th>Sản phẩm</th>
                <th style="width:110px;">SL</th>
                <th style="width:160px;"> Đơn Giá</th>
                <th style="width:180px;">Thành tiền</th>
              </tr>
            </thead>

            <tbody>
              @foreach($items as $it)
                @php
                  $line = (float)$it->price * (int)$it->quantity;
                @endphp
                <tr>
                  <td>
                    @if(!empty($it->product_image))
                      <img class="img-thumb" src="{{ asset('storage/'.$it->product_image) }}" alt="">
                    @else
                      —
                    @endif
                  </td>

                  <td style="text-align:left !important;">
                    <b>{{ $it->product_name }}</b>
                  </td>

                  <td>x{{ (int)$it->quantity }}</td>

                  <td class="money">
                    {{ number_format((float)$it->price, 0, ',', '.') }} đ
                  </td>

                  <td class="money">
                    {{ number_format($line, 0, ',', '.') }} đ
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div style="display:flex; justify-content:flex-end;">
          <div style="min-width:320px;">
            <p style="display:flex; justify-content:space-between;">
              <span>Tạm tính</span>
              <b>{{ number_format((float)$subtotal, 0, ',', '.') }} đ</b>
            </p>

            <p style="display:flex; justify-content:space-between;">
              <span>Ưu đãi hóa đơn</span>
              <b>- {{ number_format((float)$discountValue, 0, ',', '.') }} đ</b>
            </p>

            <hr style="margin:8px 0;">

            <p style="display:flex; justify-content:space-between; font-size:16px;">
              <span>Tổng thanh toán</span>
              <b class="money">{{ number_format((float)$grandTotal, 0, ',', '.') }} đ</b>
            </p>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

{{-- confirm đổi trạng thái cho đẹp --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form[action*="/status"]');
  const select = document.getElementById('statusSelect');
  if (!form || !select) return;

  const current = select.value;

  select.addEventListener('change', function (e) {
    // chỉ đổi select, chưa submit
  });

  form.addEventListener('submit', function (e) {
    const next = select.value;
    if (next === current) return;

    e.preventDefault();
    Swal.fire({
      title: 'Xác nhận',
      html: 'Đổi trạng thái đơn <b>{{ $order->order_code }}</b>?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Đổi',
      cancelButtonText: 'Hủy'
    }).then((result) => {
      if (result.isConfirmed) form.submit();
    });
  });
});
</script>

<style>
      .box { background:#fff; border:1px solid #eee; border-radius:8px; padding:15px; margin-bottom:15px; }
      .row-flex { display:flex; gap:15px; flex-wrap:wrap; }
      .col-box { flex:1; min-width:320px; }
      .k { color:#666; width:150px; display:inline-block; }
      .v { font-weight:600; }

      table td, table th { text-align:center !important; vertical-align:middle !important; }
      .label-status { padding: 4px 10px; border-radius: 6px; font-weight: 600; display: inline-block; }
      .st-pending   { background:#eee;     color:#333; }
      .st-confirmed { background:#5bc0de;  color:#fff; }
      .st-shipping  { background:#f0ad4e;  color:#fff; }
      .st-success   { background:#5cb85c;  color:#fff; }
      .st-canceled  { background:#d9534f;  color:#fff; }

      .money { color:#e60012; font-weight:800; }
      .img-thumb { width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid #eee; }
      .status-select { width:180px; margin:0 auto; }
</style>

@endsection
