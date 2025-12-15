@extends('pages.layout')
@section('content')
<h2 class="title text-center">
        Tất Cả Sản Phẩm
</h2>

@if(session('success'))
    <div id="cart-alert" 
         style="background:#e60012; color:#fff; padding:12px 16px; border-radius:8px; 
                margin-bottom:16px; font-weight:500; font-size:15px; text-align:center;">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('cart-alert');
            if (alertBox) {
                alertBox.style.opacity = '0';
                alertBox.style.transform = 'translateY(-10px)';
                alertBox.style.transition = '0.5s';
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 1800);
    </script>
@endif

<div class="features_items"><!--features_items-->
@php
    $user_id = Session::get('id');
    if ($user_id) {
        // Đã login → lấy DB
        $favorite_ids = \App\Models\Favorite::where('user_id', $user_id)
                            ->pluck('product_id')
                            ->toArray();
    } else {
        // Chưa login → lấy session
        $favorite_ids = Session::get('favorite_guest', []);
    }
@endphp


    @forelse($all_product as $product)
        <div class="col-sm-4">
            <div class="product-image-wrapper">
                <div class="single-products">

                    <div class="productinfo text-center">
                        <div class="product-img-box">
                            <img class="main-img" src="{{ $product->main_image_url }}" alt="{{ $product->name }}">
                        </div>
                        <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                        <p>{{ $product->name }}</p>
                    </div>

                    <div class="product-overlay">
                        <img class="overlay-img" src="{{ $product->hover_image_url }}" alt="{{ $product->name }}">
                        <div class="overlay-content">
                            <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                            <p><a href="{{ url('/product/'.$product->id) }}" style="color: #fff;">{{ $product->name }}</a></p>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline-block;">
                                {{ csrf_field() }}
                                <input type="hidden" name="quantity" value="1">

                                <button type="submit" class="btn btn-default add-to-cart">
                                    <i class="fa fa-shopping-cart"></i> Thêm Vào Giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="choose">
                    <ul class="nav nav-pills nav-justified">                      
                        @php 
                            $is_favorite = in_array($product->id, $favorite_ids);
                        @endphp

                        <li>
                        <a href="{{ route('favorite.toggle', $product->id) }}"
                        style="color: {{ $is_favorite ? 'red' : '#555' }};">
                            <i class="fa fa-heart"
                            style="color: {{ $is_favorite ? 'red' : '#999' }};"></i>
                            {{ $is_favorite ? 'Đã Yêu Thích' : 'Yêu Thích' }}
                        </a>
                        </li>

                        <li>
                            <a href="{{ route('compare.add', $product->id) }}">
                                <i class="fa fa-plus-square"></i> So Sánh
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center">Hiện chưa có sản phẩm nào được đăng bán.</p>
    @endforelse


    <div class="clearfix"></div>

    <!-- phân trang -->
    <div class="pagination-area" style="width:100%; float:left; text-align:center;">
        {{ $all_product->onEachSide(0)->links('pagination::bootstrap-4') }}
    </div> 
    
</div>


<div class="recommended_items"><!--recommended_items-->
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
.recommended_items {
    position: relative;
    overflow: visible !important;
}

.recommended-item-control {
    position: absolute;
    top: 30%;
    transform: translateY(-50%);
    background: #d70018;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    z-index: 9999;
}

.left.recommended-item-control {
    left: -25px;
}

.right.recommended-item-control {
    right: -25px;
}

</style>

    <h2 class="title text-center">Recommended Items</h2>

    <div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">

            @foreach($recommended_products->chunk(3) as $chunkIndex => $chunk)
            <div class="item {{ $chunkIndex == 0 ? 'active' : '' }}">
                @foreach($chunk as $product)
                    @php
                        $main = $product->main_image_url;
                        $hover = $product->hover_image_url;
                        $user_id = Session::get('id');
                        $favorite_ids = \App\Models\Favorite::where('user_id', $user_id)->pluck('product_id')->toArray();
                        $is_favorite = in_array($product->id, $favorite_ids);
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
                                        <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                                        <p><a href="{{ url('/product/'.$product->id) }}" style="color:#fff;">{{ $product->name }}</a></p>
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-default add-to-cart">
                                                <i class="fa fa-shopping-cart"></i> Thêm Vào Giỏ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="choose">
                                <ul class="nav nav-pills nav-justified">
                                    <li>
                                        <a href="{{ route('favorite.toggle', $product->id) }}" style="color: {{ $is_favorite ? 'red' : '#555' }};">
                                            <i class="fa fa-heart" style="color: {{ $is_favorite ? 'red' : '#999' }};"></i>
                                            {{ $is_favorite ? 'Đã Yêu Thích' : 'Yêu Thích' }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('compare.add', $product->id) }}">
                                            <i class="fa fa-plus-square"></i> So Sánh
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endforeach

        </div>

        <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
            <i class="fa fa-angle-left"></i>
        </a>
        <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
            <i class="fa fa-angle-right"></i>
        </a>
    </div>
</div>


@php
    $compare = session('compare', []);
    $sp1 = isset($compare['sp1']) ? \App\Models\Product::find($compare['sp1']) : null;
    $sp2 = isset($compare['sp2']) ? \App\Models\Product::find($compare['sp2']) : null;
@endphp

@if($sp1 || $sp2)
<div id="compare-bar" 
style="position:fixed; bottom:0; left:300px; right:300px; 
            background:#fff; box-shadow:0 -3px 10px rgba(0,0,0,0.15); 
            padding:15px 20px; z-index:99999;">

    <div class="row">

        {{-- SLOT SP1 --}}
        <div class="col-sm-4 text-center">
            @if($sp1)
                <img src="{{ $sp1->main_image_url }}" width="60" height="60" style="object-fit:cover;">
                <p style="margin-top:6px;">{{ $sp1->name }}</p>
                <a href="{{ route('compare.remove','sp1') }}" class="compare-close"> 
                    <i class="fa fa-times"></i>
                </a>
            @else
                <a href="{{ route('compare.select','sp1') }}" 
                   style="border:2px dashed #bbb; padding:30px 20px; display:block; color:#666;">
                    + Chọn sản phẩm 1
                </a>
            @endif
        </div>

        <div class="compare-divider"></div>

        {{-- SLOT SP2 --}}
        <div class="col-sm-4 text-center">
            @if($sp2)
                <img src="{{ $sp2->main_image_url }}" width="60" height="60" style="object-fit:cover;">
                <p style="margin-top:6px;">{{ $sp2->name }}</p>
                <a href="{{ route('compare.remove','sp2') }}" class="compare-close">
                    <i class="fa fa-times"></i>
                </a>
            @else
                <a href="{{ route('compare.select','sp2') }}" 
                   style="border:2px dashed #bbb; padding:30px 20px; display:block; color:#666;">
                    + Chọn sản phẩm 2
                </a>
            @endif
        </div>

        {{-- NÚT SO SÁNH NGAY --}}
        <div style="margin-top: 4px;" class="col-sm-4 d-flex flex-column align-items-center justify-content-center">
            <br><br>

            <a href="{{ route('compare.view') }}" class="compare-now" style="margin-bottom: 10px;">
                So sánh ngay
            </a>

            <a href="{{ route('compare.clear') }}" class="compare-btn danger">
                Xoá tất cả
            </a>

        </div>


    </div>

</div>
@endif

@endsection