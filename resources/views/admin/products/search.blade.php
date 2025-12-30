@extends('pages.layout')
@section('content')

{{-- COMPARE BAR --}}
@include('pages.partials.compare_bar')

<div class="features_items">
    <h2 class="title text-center">
        Kết quả tìm kiếm: {{ $keywords }}
    </h2>

    @if($search_product->count())
        @foreach($search_product as $product)
            @include('pages.partials.product_card', [
                'product' => $product
            ])
        @endforeach
    @else
        <p class="text-center" style="margin-top:40px;">
            Không tìm thấy sản phẩm phù hợp
        </p>
    @endif

    <div class="clearfix"></div>
</div>

@endsection
