@extends('pages.layout')

@section('content')

@php 
    use Illuminate\Support\Facades\Storage;
@endphp

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
    text-align: center;
    color: #fff;
}

.product-overlay:hover {
    background: rgba(0,0,0,0.3);
}
</style>


<h2 class="title text-center">Sản phẩm yêu thích của bạn</h2>

<div class="features_items">

@forelse($favorites as $fav)

    @php 
        $product = $fav->product;
        if(!$product) continue;

        // Lấy ảnh từ bảng product_images
        $img = $product->productImage;

        $main = ($img && $img->image_1)
            ? Storage::url($img->image_1)
            : asset('images/no-image.png');

        $hover = ($img && $img->image_2)
            ? Storage::url($img->image_2)
            : $main;

        $product_url = url('product/' . $product->id);
    @endphp


    <div class="col-sm-4">
        <div class="product-image-wrapper">
            <div class="single-products">

                <div class="productinfo text-center">

                    <div class="product-img-box">
                        <img class="main-img" src="{{ $main }}" alt="{{ $product->name }}">
                        <img class="hover-img" src="{{ $hover }}" alt="{{ $product->name }}">
                    </div>

                    <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                    <p>{{ $product->name }}</p>
                </div>


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

            <div class="choose">
                <ul class="nav nav-pills nav-justified">
                    <li>
                        <a href="{{ route('favorite.remove', $product->id) }}">
                            <i class="fa fa-times"></i> Xoá khỏi yêu thích
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

@empty

    <h3 class="text-center" style="margin: 50px 0;">
        Bạn chưa yêu thích sản phẩm nào.
    </h3>

@endforelse

    <div class="clearfix"></div>
</div>

@endsection
