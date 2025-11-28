@extends('pages.layout')

@section('content')

@php
    $cart = Session::get('cart', []);
@endphp

<div class="cart-page">
    <h2 class="title text-center">
        Giỏ Hàng Của Bạn
    </h2>

    @if(empty($cart) || count($cart) === 0)
        <p class="cart-empty">Hiện chưa có sản phẩm nào trong giỏ hàng.</p>
    @else

        <form action="{{ route('cart.update') }}" method="POST">
            @csrf

            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản Phẩm</th>
                            <th>Đơn Giá</th>
                            <th>Số Lượng</th>
                            <th>Tạm Tính</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $item)
                            <tr>
                                <td class="cart-product">
                                    <div class="cart-product-box">
                                        <div class="cart-product-img">
                                            @if(!empty($item['image']))
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                            @else
                                                <img src="{{ asset('frontend/images/noimage.jpg') }}" alt="{{ $item['name'] }}">
                                            @endif
                                        </div>
                                        <div class="cart-product-name">
                                            {{ $item['name'] }}
                                        </div>
                                    </div>
                                </td>

                                <td class="cart-price">
                                    {{ number_format($item['price'], 0, ',', '.') }} VND
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        min="1"
                                        name="quantities[{{ $item['id'] }}]"
                                        value="{{ $item['quantity'] }}"
                                        class="cart-qty-input">
                                </td>

                                <td class="cart-subtotal">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} VND
                                </td>

                                <td class="cart-remove">
                                    <button
                                        type="submit"
                                        class="cart-remove-btn"
                                        formaction="{{ route('cart.remove') }}"
                                        formmethod="POST"
                                        name="product_id"
                                        value="{{ $item['id'] }}"
                                    >
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="cart-footer">
                <div class="cart-total">
                    <span>Tổng cộng:</span>
                    <strong>{{ number_format($total, 0, ',', '.') }} VND</strong>
                </div>

                <div class="cart-actions">
                    <a href="{{ url('/trang-chu') }}" class="btn btn-default cart-continue">
                        Tiếp tục mua sắm
                    </a>

                    <button type="submit" class="btn cart-update">
                        Cập nhật giỏ hàng
                    </button>

                    @php
                        $id = Session::get('id');
                    @endphp

                    @if($id != null)
                        <a href="{{ URL::to('/payment') }}" class="btn cart-checkout">
                            Thanh toán (demo)
                        </a>
                    @else
                        <a href="{{ URL::to('/login-checkout') }}" class="btn cart-checkout">
                            Thanh toán
                        </a>
                    @endif
                </div>
            </div>
        </form>
    @endif
</div>

<style>
    .cart-page {
        padding: 20px 0 40px;
    }

    .cart-title {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .cart-empty {
        font-size: 15px;
        margin-top: 10px;
    }

    .cart-table-wrapper {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 20px;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background: #fff;
    }

    .cart-table thead {
        background: #f5f5f5;
    }

    .cart-table th,
    .cart-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
        text-align: left;
        white-space: nowrap;
    }

    .cart-table th {
        color: white;
        background-color: #e60012;
        font-weight: 600;
    }

    .cart-product {
        white-space: normal;
    }

    .cart-product-box {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cart-product-img {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .cart-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-product-name {
        font-weight: 500;
        line-height: 1.3;
    }

    .cart-price,
    .cart-subtotal {
        font-weight: 500;
    }

    .cart-qty-input {
        width: 70px;
        text-align: center;
        padding: 4px 6px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .cart-footer {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .cart-total span {
        font-size: 15px;
        margin-right: 6px;
    }

    .cart-total strong {
        font-size: 18px;
        color: #e60012;
    }

    .cart-actions .btn {
        margin-left: 5px;
    }

    .cart-continue {
        background: #ffffff;
        border: 1px solid #e60012;
        color: #e60012;
        border-radius: 4px;
    }

    .cart-continue:hover {
        background: #524e4e;
        color: white;
        border-radius: 4px;
    }

    .cart-update {
        background: #f0ad4e;
        border-color: #f0ad4e;
        color: #fff;
        border-radius: 4px;
    }

    .cart-update:hover {
        background: #ec971f;
        border-color: #d58512;
        color: #fff;
    }

    .cart-checkout {
        background: #e60012;
        border-color: #e60012;
        color: #fff;
        border-radius: 4px;
    }

    .cart-checkout:hover {
        background: #b80010;
        border-color: #b80010;
        color: #fff;
    }

    @media (max-width: 767px) {
        .cart-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .cart-actions {
            width: 100%;
        }

        .cart-actions .btn {
            width: 100%;
            margin: 5px 0 0 0;
            text-align: center;
        }
    }

    .cart-remove-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        background: #e60012;
        padding: 6px 14px;
        border-radius: 6px;
        transition: 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .cart-remove-btn:hover {
        background: #b80010;
    }
</style>

@endsection
