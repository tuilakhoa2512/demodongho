@extends('pages.layout')
@section('content')

<h2 class="title text-center">
    Chọn sản phẩm để so sánh
</h2>

<div class="features_items">

@foreach($products as $product)

    <div class="col-sm-4">
        <div class="product-image-wrapper">

            <div class="single-products">

                {{-- chọn sp nhưng ko hover --}}
                <div class="productinfo text-center">

                    <img src="{{ $product->main_image_url }}"
                         style="width:100%; height:280px; object-fit:cover;">

                    <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>

                    <p>{{ $product->name }}</p>

                    <a href="{{ route('compare.add', $product->id) }}"
                       class="btn btn-default add-to-cart">
                        <i class="fa fa-plus-square"></i>
                        Chọn để so sánh
                    </a>

                </div>

            </div>

        </div>
    </div>

@endforeach

<div class="clearfix"></div>

</div>

@endsection
