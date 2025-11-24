<div class="features_items">
    <h2 class="title text-center">Sản Phẩm Mới Nhất</h2>

    @forelse($all_product as $product)
        <div class="col-sm-4">
            <div class="product-image-wrapper">
                <div class="single-products">

                    <div class="productinfo text-center">
                        
                    
                        <div class="product-img-box">
                            
                            <img class="img-main"
                                 src="{{ $product->main_image_url }}"
                                 alt="{{ $product->name }}">
                        </div>
                      
                        <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                        <p>{{ $product->name }}</p>
                    </div>

                  
                    <div class="product-overlay">
                    
                        <img class="overlay-img"
                             src="{{ $product->hover_image_url }}"
                             alt="{{ $product->name }}">

                        <div class="overlay-content">
                            <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                            <p>{{ $product->name }}</p>
                            <a href="#" class="btn btn-default add-to-cart">
                                <i class="fa fa-shopping-cart"></i> Thêm vào giỏ
                            </a>
                        </div>
                    </div>

                </div>

              
                <div class="choose">
                    <ul class="nav nav-pills nav-justified">
                        <li>
                            <a href="#"><i class="fa fa-heart"></i> Yêu Thích</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-plus-square"></i> So Sánh</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center">Hiện chưa có sản phẩm nào được đăng bán.</p>
    @endforelse

    <div class="clearfix"></div>
</div>