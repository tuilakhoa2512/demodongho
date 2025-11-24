@extends('pages.layout')

@section('content')

<div class="features_items">
    <h2 class="title text-center">
        Sản phẩm thuộc danh mục
    </h2>

    @foreach($category_by_id as $product)
        <div class="col-sm-4">
            <div class="product-image-wrapper">
                <div class="single-products">

                    <div class="productinfo text-center">

                        {{-- ẢNH SẢN PHẨM --}}
                        @php
                            // Nếu có image_1 thì show, nếu không có thì dùng ảnh mặc định
                            $imagePath = $product->image_1 
                                         ? asset('storage/' . $product->image_1)
                                         : asset('images/no-image.png');
                        @endphp

                        <img src="{{ $imagePath }}" 
                             alt="{{ $product->name }}" 
                             style="height: 260px; object-fit: cover;" />

                        {{-- GIÁ --}}
                        <h2>{{ number_format($product->price) }} VNĐ</h2>

                        {{-- TÊN SẢN PHẨM --}}
                        <p>{{ $product->name }}</p>

                        <a href="#" class="btn btn-default add-to-cart">
                            <i class="fa fa-shopping-cart"></i>Thêm vào giỏ
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

</div>

@endsection
