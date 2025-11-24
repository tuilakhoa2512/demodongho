@extends('pages.layout')
@section('content')
<style>
.product-hover {
    position: relative;
    overflow: hidden;
}

.product-hover .hover-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: all 0.5s ease;
}

.product-hover:hover .hover-img {
    opacity: 1;
}

.product-hover:hover .main-img {
    opacity: 0;
}

</style>
<div class="features_items">
    <h2 class="title text-center">Kết quả tìm kiếm</h2>

    @foreach ($search_product as $product)
        <div class="col-sm-4">
            <div class="product-image-wrapper">
                <div class="single-products">
                    <div class="productinfo text-center">
                        <div class="product-hover">
                            <img class="main-img" src="{{ $product->main_image_url }}" alt="{{ $product->name }}" />
                            <img class="hover-img" src="{{ $product->hover_image_url }}" alt="{{ $product->name }}" />
                        </div>
                        <h2>{{ number_format($product->price) }} VND</h2>
                        <p>{{ $product->name }}</p>
                        <a href="#" class="btn btn-default add-to-cart">
                            <i class="fa fa-shopping-cart"></i> Thêm vào giỏ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>


@endsection
