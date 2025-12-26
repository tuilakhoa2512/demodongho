<div class="recommend-wrapper">

    <h2 class="title text-center">Sản phẩm gợi ý</h2>

    <div class="recommend-container">

        {{-- NÚT TRÁI --}}
        <button class="recommend-btn prev">
            <i class="fa fa-angle-left"></i>
        </button>

        {{-- KHUNG HIỂN THỊ --}}
        <div class="recommend-viewport">
            <div class="recommend-track">

                @foreach($recommendProducts as $product)
                    <div class="recommend-item">
                        @include('pages.partials.product_card', [
                            'product' => $product,
                            'favorite_ids' => $favorite_ids ?? []
                        ])
                    </div>
                @endforeach

            </div>
        </div>

        {{-- NÚT PHẢI --}}
        <button class="recommend-btn next">
            <i class="fa fa-angle-right"></i>
        </button>

    </div>
</div>
