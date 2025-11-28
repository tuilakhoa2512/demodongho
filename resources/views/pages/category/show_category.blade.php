@extends('pages.layout')

@section('content')

<style>
.product-img-box {
    position: relative;
    width: 100%;
    height: 240px;
    overflow: hidden;
}

.product-img-box img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    transition: 0.4s ease-in-out;
    position: absolute;
    top: 0;
    left: 0;
}

.product-img-box .hover-img {
    opacity: 0;
}

.product-img-box:hover .main-img {
    opacity: 0;
}

.product-img-box:hover .hover-img {
    opacity: 1;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 280px; 
    background: rgba(0,0,0,0.0);
    transition: 0.4s ease-in-out;
}

.product-overlay .overlay-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
}

.product-overlay .overlay-content {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 15px;
    /* background: rgba(0, 0, 0, 0.5); nền mờ để chữ nổi bật */
    text-align: center;
    color: #fff;
}

.product-overlay:hover {
    background: rgba(0,0,0,0.3);
}

</style>

  <h2 class="title text-center">
        Sản phẩm {{ $category_name }}
    </h2>

<div class="features_items">
    
  

    @foreach($category_by_id as $product)
        @php
            // Hình chính
            $main = $product->image_1
                    ? asset('storage/' . $product->image_1)
                    : asset('images/no-image.png');

            // Hình hover
            $hover = $product->image_2
                     ? asset('storage/' . $product->image_2)
                     : $main;
            $product_url = url('product/' . $product->id);
        @endphp


        <div class="col-sm-4">
            <div class="product-image-wrapper">
                <div class="single-products">

                    <div class="productinfo text-center">

                        {{-- Hình sản phẩm đổi khi hover --}}
                        <div class="product-img-box">
                            <img class="main-img" src="{{ $main }}" alt="{{ $product->name }}">
                            <img class="hover-img" src="{{ $hover }}" alt="{{ $product->name }}">
                        </div>

                        <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                        <p>{{ $product->name }}</p>
                    </div>


                    {{-- OVERLAY --}}
                    <div class="product-overlay">
                        <img class="overlay-img" src="{{ $hover }}" alt="{{ $product->name }}">
                        <div class="overlay-content">
                            <h2>{{ number_format($product->price,0,',','.') }} VND</h2>
                            <p><a href="{{ $product_url }}" style="color:#fff;">{{ $product->name }}</a></p>
                            <a href="#" class="btn btn-default add-to-cart">
                                <i class="fa fa-shopping-cart"></i> Thêm vào giỏ
                            </a>
                        </div>
                    </div>

                </div>

                {{-- Yêu thích + So sánh --}}
                <div class="choose">
                    <ul class="nav nav-pills nav-justified">
                        <li>
                            <a href="#"><i class="fa fa-heart"></i> Yêu Thích</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-plus-square"></i> So Sánh</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    @endforeach

    <div class="clearfix"></div>
</div>

@endsection
