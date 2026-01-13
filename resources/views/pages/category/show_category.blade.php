@extends('pages.layout')

@section('content')

<h2 class="title text-center">
    Sản phẩm {{ $category_name }}
</h2>

@if(session('success'))
    <div id="cart-alert"
         style="background:#e60012;color:#fff;padding:12px 16px;border-radius:8px;
                margin-bottom:16px;font-weight:500;text-align:center;">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const el = document.getElementById('cart-alert');
            if (el) el.remove();
        }, 1800);
    </script>
@endif

<div class="features_items">
    @php
        $user_id = Session::get('id');
        $favorite_ids = \App\Models\Favorite::where('user_id', $user_id)
                            ->pluck('product_id')
                            ->toArray();
    @endphp

    @foreach($category_by_id as $product)
        @include('pages.partials.product_card', [
            'product' => $product,
            'favorite_ids' => $favorite_ids
        ])
    @endforeach

    <div class="clearfix"></div>
</div>

{{-- ====== COMPARE BAR GIỮ NGUYÊN ====== --}}
@php
    $compare = session('compare', []);
    $sp1 = isset($compare['sp1']) ? \App\Models\Product::find($compare['sp1']) : null;
    $sp2 = isset($compare['sp2']) ? \App\Models\Product::find($compare['sp2']) : null;
@endphp

@if($sp1 || $sp2)
<div id="compare-bar" class="compare-bar"
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
@endif
<div class="col-sm-7 text-right text-center-xs">
    {{ $category_by_id->links('vendor.pagination.number-only') }}
</div>
@endsection
