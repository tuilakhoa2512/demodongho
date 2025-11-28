@extends('pages.layout')

@section('content')

<h2 class="title text-center">
        THANH TOÁN
    </h2>

<div class="payment-page">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-6">
            <div class="payment-box">
                <h3>Thông Tin Nhận Hàng</h3>

                <form action="{{ route('payment.place') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Họ tên người nhận <span style="color:red">*</span></label>
                        <input type="text"
                               name="customer_name"
                               class="form-control"
                               value="{{ old('customer_name') }}"
                               required
                               style="color:black;">
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại <span style="color:red">*</span></label>
                        <input type="text"
                               name="customer_phone"
                               class="form-control"
                               value="{{ old('customer_phone') }}"
                               required
                               style="color:black;">
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ nhận hàng <span style="color:red">*</span></label>
                        <textarea name="customer_address"
                                  rows="3"
                                  class="form-control"
                                  required
                                  style="resize:none; color:black;">{{ old('customer_address') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú thêm</label>
                        <textarea name="customer_note"
                                  rows="3"
                                  class="form-control"
                                  style="resize:none; color:black;"
                                  placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao...">{{ old('customer_note') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-danger btn-block">
                        Xác nhận đặt hàng
                    </button>
                </form>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="payment-box">
                <h3>Đơn Hàng Của Bạn</h3>

                <div class="payment-cart-summary">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-right">Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp

                            @foreach($cart as $item)
                                @php
                                    $lineTotal = $item['price'] * $item['quantity'];
                                    $total += $lineTotal;
                                @endphp
                                <tr>
                                    <td>
                                        {{ $item['name'] }}<br>
                                        <small>x {{ $item['quantity'] }}</small>
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($lineTotal, 0, ',', '.') }} VND
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Tổng cộng</th>
                                <th class="text-right">
                                    {{ number_format($total, 0, ',', '.') }} VND
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="payment-note">
                    Đơn hàng của bạn sẽ được xử lý sau khi nhấn nút
                    <strong>"Xác nhận đặt hàng"</strong>.
                    Nhân viên sẽ liên hệ để xác nhận nếu cần thiết.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-page {
        padding: 20px 0 40px;
    }

    .payment-box {
        background: #fff;
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
        box-shadow: 0 0 4px rgba(0,0,0,0.05);
    }

    .payment-box h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .payment-cart-summary table {
        margin-bottom: 10px;
        font-size: 14px;
    }

    .payment-note {
        font-size: 13px;
        color: #555;
    }

    .alert {
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .alert-success {
        background: #dff0d8;
        border: 1px solid #d0e9c6;
        color: #3c763d;
    }

    .alert-danger {
        background: #f2dede;
        color: #a94442;
    }
</style>

@endsection
