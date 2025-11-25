@extends('pages.layout')
@section('content')

<div class="features_items"><!--features_items-->
    <h2 class="title text-center">Tất cả sản phẩm</h2>

    @forelse($all_product as $product)
        <div class="col-sm-4">
            <div class="product-image-wrapper">
                <div class="single-products">

                    <div class="productinfo text-center">
                        <div class="product-img-box">
                            <img class="img-main" src="{{ $product->main_image_url }}" alt="{{ $product->name }}">
                        </div>
                        <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                        <p>{{ $product->name }}</p>
                    </div>

                    <div class="product-overlay">
                        <img class="overlay-img" src="{{ $product->hover_image_url }}" alt="{{ $product->name }}">
                        <div class="overlay-content">
                            <h2>{{ number_format($product->price, 0, ',', '.') }} VND</h2>
                            <p><a href="{{ url('/product/'.$product->id) }}" style="color: #fff;">{{ $product->name }}</a></p>
                            <a href="#" class="btn btn-default add-to-cart">
                                <i class="fa fa-shopping-cart"></i> Thêm vào giỏ
                            </a>
                        </div>
                    </div>
                </div>

                <div class="choose">
                    <ul class="nav nav-pills nav-justified">
                        <li><a href="#"><i class="fa fa-heart"></i> Yêu Thích</a></li>
                        <li><a href="#"><i class="fa fa-plus-square"></i> So Sánh</a></li>
                    </ul>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center">Hiện chưa có sản phẩm nào được đăng bán.</p>
    @endforelse


    <div class="clearfix"></div>
    {{-- PHÂN TRANG --}}
    <div class="pagination-area" style="width:100%; float:left; text-align:center;">
        {{ $all_product->onEachSide(0)->links('pagination::bootstrap-4') }}
    </div>

</div>


    <div class="recommended_items"><!--recommended_items-->
        <h2 class="title text-center">recommended items</h2>

        <div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="item active">
                    <div class="col-sm-4">
                        <div class="product-image-wrapper">
                            <div class="single-products">
                                <div class="productinfo text-center">
                                    <img src="{{asset('frontend/images/rcm1.jpg')}}" alt="" />
                                    <h2>10.000.000 VND</h2>
                                    <p>Rolex Gold</p>
                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="product-image-wrapper">
                            <div class="single-products">
                                <div class="productinfo text-center">
                                    <img src="{{asset('frontend/images/rcm2.webp')}}" alt="" />
                                    <h2>10.000.000 VND</h2>
                                    <p>Rolex Gold</p>
                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="product-image-wrapper">
                            <div class="single-products">
                                <div class="productinfo text-center">
                                    <img src="{{asset('frontend/images/rcm3.png')}}" alt="" />
                                    <h2>10.000.000 VND</h2>
                                    <p>Rolex Gold</p>
                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="col-sm-4">
                        <div class="product-image-wrapper">
                            <div class="single-products">
                                <div class="productinfo text-center">
                                    <img src="{{asset('frontend/images/rcm4.jpg')}}" alt="" />
                                    <h2>10.000.000 VND</h2>
                                    <p>Rolex Gold</p>
                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="product-image-wrapper">
                            <div class="single-products">
                                <div class="productinfo text-center">
                                    <img src="{{asset('frontend/images/rcm5.jpg')}}" alt="" />
                                    <h2>10.000.000 VND</h2>
                                    <p>Rolex Gold</p>
                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="product-image-wrapper">
                            <div class="single-products">
                                <div class="productinfo text-center">
                                    <img src="{{asset('frontend/images/rcm6.webp')}}" alt="" />
                                    <h2>10.000.000 VND</h2>
                                    <p>Rolex Gold</p>
                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
                <i class="fa fa-angle-left"></i>
              </a>
              <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
                <i class="fa fa-angle-right"></i>
              </a>
        </div>
    </div><!--/recommended_items-->

@endsection