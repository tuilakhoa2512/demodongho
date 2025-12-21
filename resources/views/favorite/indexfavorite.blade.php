@extends('pages.layout')

@section('content')

<h2 class="title text-center">Sản phẩm yêu thích của bạn</h2>

<div class="features_items">

@php
    $favorite_ids = $favorites->pluck('product_id')->toArray();
@endphp

@forelse($favorites as $fav)

    @if($fav->product)
        @include('pages.partials.product_card', [
            'product' => $fav->product,
            'favorite_ids' => $favorite_ids
        ])
    @endif

@empty
    <h3 class="text-center" style="margin:50px 0;">
        Bạn chưa yêu thích sản phẩm nào.
    </h3>
@endforelse

<div class="clearfix"></div>
</div>
@endsection
