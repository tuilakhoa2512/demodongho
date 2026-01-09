@extends('pages.layout')

@section('content')

<h2 class="title text-center">
    SẢN PHẨM ĐANG GIẢM GIÁ
</h2>

<div class="features_items">
    <div class="row">

        @forelse($saleProducts as $product)
            @include('pages.partials.product_card', [
                'product' => $product,                
            ])
        @empty
            <div class="col-sm-12">
                <p class="text-center" style="font-size:16px;">
                    Hiện chưa có sản phẩm nào đang giảm giá
                </p>
            </div>
        @endforelse
    </div>
</div>

<div class="text-center">
    {{ $saleProducts->links('vendor.pagination.number-only') }}
</div>

@endsection
