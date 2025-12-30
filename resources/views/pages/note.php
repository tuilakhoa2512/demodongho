<div class="col-sm-4 {{ isset($is_recommended) ? 'recommended-card' : '' }}">

    <div class="product-image-wrapper">
        <div class="single-products">
            

            @php
                // ====== GIÁ ======
                $salePrice   = $product->discounted_price ?? null;
                $basePrice   = (float) $product->price;
                $hasDiscount = !is_null($salePrice) && $salePrice < $basePrice;

                // ====== YÊU THÍCH ======
                $user_id     = Session::get('id');
                $is_favorite = isset($favorite_ids)
                                ? in_array($product->id, $favorite_ids)
                                : false;
            @endphp

            <div class="productinfo text-center">
                <div class="product-img-box">
                    <img class="main-img" src="{{ $product->main_image_url }}" alt="{{ $product->name }}">
                </div>
                <br>

                <h2 style="margin-bottom:4px; font-size:18px; font-weight:700;">
                    @if($hasDiscount)
                        {{ number_format($salePrice, 0, ',', '.') }} VND
                    @else
                        {{ number_format($basePrice, 0, ',', '.') }} VND
                    @endif
                </h2>

                @if($hasDiscount)
                    <div style="color:#999; font-size:14px; text-decoration:line-through;">
                        {{ number_format($basePrice, 0, ',', '.') }} VND
                    </div>
                @endif

                <p>{{ $product->name }}</p>
            </div>

            <div class="product-overlay">
                <img class="overlay-img" src="{{ $product->hover_image_url }}" alt="{{ $product->name }}">
                <div class="overlay-content">
                    <a href="{{ url('/product/'.$product->id) }}"
                       class="btn btn-default add-to-cart">
                        <i class="fa fa-eye"></i> Xem Chi Tiết
                    </a>
                </div>
            </div>

        </div>

        @if(!isset($hideActions) || $hideActions === false)
        <div class="choose">
            <ul class="nav nav-pills nav-justified">
                
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
        @endif
    </div>
</div>
