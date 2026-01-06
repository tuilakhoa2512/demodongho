<div class="col-sm-4 {{ isset($is_recommended) ? 'recommended-card' : '' }}">
  <div class="product-image-wrapper">
    <div class="single-products">

      @php
        $basePrice  = (float) ($product->price ?? 0);
        $finalPrice = isset($product->final_price) ? (float) $product->final_price : $basePrice;

        $hasDiscount = ($basePrice > 0) && ($finalPrice > 0) && ($finalPrice < $basePrice);

        $promoLabel = $product->promo_label ?? null;
        $promoName  = $product->promo_name ?? null;

        $favorite_ids = $favorite_ids ?? [];
        $is_favorite  = in_array($product->id, $favorite_ids);

        $hideActions = $hideActions ?? false;
      @endphp

      <div class="productinfo text-center">
        <div class="product-img-box" style="position:relative;">
          <img class="main-img" src="{{ $product->main_image_url }}" alt="{{ $product->name }}">

          @if($hasDiscount && !empty($promoLabel))
            <span style="position:absolute;top:8px;left:8px;background:#e60012;color:#fff;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:700;">
              {{ $promoLabel }}
            </span>
          @endif
        </div>

        <br>

        <h2 style="margin-bottom:4px;font-size:18px;font-weight:700;">
          @if($hasDiscount)
            <span style="color:#e60012;">{{ number_format($finalPrice, 0, ',', '.') }} VND</span>
          @else
            {{ number_format($basePrice, 0, ',', '.') }} VND
          @endif
        </h2>

        @if($hasDiscount)
          <div style="color:#999;font-size:14px;text-decoration:line-through;">
            {{ number_format($basePrice, 0, ',', '.') }} VND
          </div>
        @endif

        <p style="margin-top:8px;">{{ $product->name }}</p>
      </div>

      @if(!$hideActions)
        <div class="product-overlay">
          <img class="overlay-img" src="{{ $product->hover_image_url }}" alt="{{ $product->name }}">
          <div class="overlay-content">
            <a href="{{ url('/product/'.$product->id) }}" class="btn btn-default add-to-cart">
              <i class="fa fa-eye"></i> Xem Chi Tiết
            </a>
          </div>
        </div>
      @endif

    </div>

    @if(!$hideActions)
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
    @endif

  </div>
</div>