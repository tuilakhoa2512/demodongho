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
    @include('pages.partials.product_card', [
        'product' => $product,
        'favorite_ids' => $favorite_ids
    ])
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
    padding-top: 6px;
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

        @foreach($recommended_products->chunk(3) as $index => $chunk)
            <div class="item {{ $index == 0 ? 'active' : '' }}">
                <div class="row">
                    @foreach($chunk as $product)
                        @include('pages.partials.product_card', ['product' => $product])
                    @endforeach
                </div>
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

            <a href="{{ route('compare.view') }}" class="compare-now">
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