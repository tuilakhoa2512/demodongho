<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Home | UnK-Store</title>
	<link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/prettyPhoto.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/price-range.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/animate.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/main.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/responsive.css') }}" rel="stylesheet">
	@stack('css')	

	<link rel="shortcut icon" href="{{ asset('frontend/images/ico/favicon.ico')}}">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="images/ico/apple-touch-icon-57-precomposed.png">
	<link rel="stylesheet" href="{{ asset('frontend/css/bootstrap-slider.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/recommended.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/custom-menu.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/ai-chatbox.css') }}">

	
</head><!--/head-->

<body>
	<header id="header"><!--header-->
		<div class="header_top"><!--header_top-->
			<div class="container">
				<div class="row">
					<div class="col-sm-6">
						<div class="contactinfo">
							<ul class="nav nav-pills">
								<li><a href="#"><i class="fa fa-phone"></i> +84 983 567 891</a></li>
								<li><a href="#"><i class="fa fa-envelope"></i> unkstore@gmail.com</a></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="social-icons pull-right">
							<ul class="nav navbar-nav">
								<li><a href="#"><i class="fa fa-facebook"></i></a></li>
								<li><a href="#"><i class="fa fa-instagram"></i></a></li>
								<li><a href="#"><i class="fa fa-google-plus"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!--/header_top-->

		<div class="header-middle"><!--header-middle-->
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<div class="logo pull-left" style="display: flex; align-items: center;">
							<a href="{{ URL::to('/trang-chu') }}"><img src="http://127.0.0.1:8000/frontend/images/logounk2.jpg" alt="" class="logo"></a>
							<span class="store-name">
								<span class="store-name-main">UnK</span>
								<span class="store-name-sub">STORE</span>
							</span>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="shop-menu pull-right">
							<ul class="nav navbar-nav">
								<li><a href="{{ URL::to('/yeu-thich') }}"><i class="fa fa-star"></i> Yêu Thích</a>
								</li>
								<?php
									$id = Session::get('id');
									if($id!=null){ 		
								?>
								<li><a href="{{ URL::to('/payment') }}"><i class="fa fa-credit-card"></i>Thanh toán </a></li>
								
								<?php
								 }else{
									?>
									<li><a href="{{ URL::to('/login-checkout') }}"><i class="fa fa-credit-card"></i> Thanh Toán</a></li>
									<?php
								 }
								?>
								
								<!-- <li><a href="{{ URL::to('/cart') }}"><i class="fa fa-shopping-cart"></i>Giỏ Hàng</a></li> -->
								 <li>
									<a href="{{ URL::to('/cart') }}" class="cart-link">
										<i class="fa fa-shopping-cart"></i>
										Giỏ Hàng

										@if(($cartCount ?? 0) > 0)
											<span id="cart-count" class="cart-badge">( {{ $cartCount }} )</span>
										@else
											<span id="cart-count" class="cart-badge" style="display:none;">0</span>
										@endif
									</a>
								</li>


								<?php
									$id = Session::get('id');
									$fullname = Session::get('fullname');
									$image = Session::get('images');
									?>

									@if($id != null)
										<li class="dropdown user-menu">
											<a href="#" class="dropdown-toggle">
												<i class="fa fa-user"></i> {{ $fullname }}
											</a>

											<ul class="dropdown-menu user-dropdown">
												<li><a href="{{ URL::to('/profile') }}"><i class="fa fa-info-circle"></i> Quản lý tài khoản</a></li>
												<li><a href="{{ URL::to('/my-orders') }}"><i class="fa fa-list-alt"></i> Đơn hàng</a></li>
												<li><a href="{{ URL::to('/logout-checkout') }}"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
											</ul>
										</li>
									@else
										<li><a href="{{ URL::to('/login-checkout') }}"><i class="fa fa-lock"></i>Đăng Nhập</a></li>
									@endif

								
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div><!--/header-middle-->

		<div class="header-bottom"><!--header-bottom-->
			<div class="container">
				<div class="row">
					<div class="col-sm-7">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						<div class="mainmenu pull-left">
							<ul class="nav navbar-nav collapse navbar-collapse">
								<li><a href="{{ URL::to('/trang-chu') }}" class="active">Home</a></li>
								<li class="has-submenu">
									<a href="#">Đồng Hồ <span class="arrow-down"></span></a>
									<ul class="sub-menu">
										@foreach($category as $cate)
											<li>
												<a href="{{ URL::to('/danh-muc-san-pham/' . $cate->category_slug) }}">
													{{ $cate->name }}
												</a>
											</li>
										@endforeach
									</ul>
								</li>

								<li class="dropdown brand-dropdown">
									<a href="#">
										Thương Hiệu <i class="fa fa-angle-down"></i>
									</a>

									<div class="brand-menu">
										<div class="brand-grid">
											@foreach($brand as $br)
												<a href="{{ URL::to('/thuong-hieu-san-pham/'.$br->brand_slug) }}" class="brand-item">
												<img src="{{ asset('storage/'.$br->image) }}" alt="{{ $br->name }}">
												</a>
											@endforeach
										</div>
									</div>
								</li>

								<li><a href="{{ route('sales.product') }}">Khuyến Mãi</a></li>
								<li><a href="{{ route('contact.us') }}">Liên Hệ</a></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-5">
    <form action="{{ route('search') }}" method="GET">
        <div class="search_box pull-right" style="position: relative; width: 250px;">

            <input type="text"
                   name="keywords"
                   value="{{ request('keywords') }}"
                   placeholder="Tìm kiếm sản phẩm"
                   style="width: 100%; padding: 8px 35px 8px 12px; border-radius: 5px; border: 1px solid #ccc;">

            <button type="submit"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
                           background: none; border: none; color: #555; cursor: pointer; font-size: 16px;">
                <i class="fa fa-search"></i>
            </button>

        </div>
    </form>
</div>

				</div>
			</div>
		</div><!--/header-bottom-->
	</header><!--/header-->

	<section id="slider"><!--slider-->
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div id="slider-carousel" class="carousel slide" data-ride="carousel">
						<ol class="carousel-indicators">
							<li data-target="#slider-carousel" data-slide-to="0" class="active"></li>
							<li data-target="#slider-carousel" data-slide-to="1"></li>
							<li data-target="#slider-carousel" data-slide-to="2"></li>
						</ol>

						<div class="carousel-inner">
							<div class="item active">
								<div class="col-sm-6">
									<h1><span>UnK</span>-STORE</h1>
									<h2>Từng Khoảnh Khắc - Một Bản Sắc</h2>
									<p>Thời gian của bạn, phong cách của chúng tôi – Khám phá bộ sưu tập đồng hồ đẳng cấp tại UnK STORE! </p>
									<button type="button" class="btn btn-default get">Get it now</button>									
								</div>
								<div class="col-sm-6">
									<img src="frontend/images/5.jfif" class="girl img-responsive" alt="" />
									<img src="images/home/pricing.png" class="pricing" alt="" />
								</div>
							</div>
							<div class="item">
								<div class="col-sm-6">
									<h1><span>UnK</span>-STORE</h1>
									<h2>Mỗi Giây Phút - Một Câu Chuyện</h2>
									<p>Thời gian của bạn, phong cách của chúng tôi – Khám phá bộ sưu tập đồng hồ đẳng cấp tại UnK STORE! </p>
									<button type="button" class="btn btn-default get">Get it now</button>
								</div>
								<div class="col-sm-6">
									<img src="images/home/girl2.jpg" class="girl img-responsive" alt="" />
									<img src="images/home/pricing.png" class="pricing" alt="" />
								</div>
							</div>

							<div class="item">
								<div class="col-sm-6">
									<h1><span>UnK</span>-STORE</h1>
									<h2>Từng Phút Giây Định Hình Đẳng Cấp</h2>
									<p>Thời gian của bạn, phong cách của chúng tôi – Khám phá bộ sưu tập đồng hồ đẳng cấp tại UnK STORE! </p>
									<button type="button" class="btn btn-default get">Get it now</button>
								</div>
								<div class="col-sm-6">
									<img src="images/home/girl3.jpg" class="girl img-responsive" alt="" />
									<img src="images/home/pricing.png" class="pricing" alt="" />
								</div>
							</div>

						</div>

						<a href="#slider-carousel" class="left control-carousel hidden-xs" data-slide="prev">
							<i class="fa fa-angle-left"></i>
						</a>
						<a href="#slider-carousel" class="right control-carousel hidden-xs" data-slide="next">
							<i class="fa fa-angle-right"></i>
						</a>
					</div>

				</div>
			</div>
		</div>
	</section><!--/slider-->

	<section>
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<div class="left-sidebar">
						<h2>Danh mục</h2>
						<div class="panel-group category-products" id="accordian"><!--category-productsr-->
							@foreach ($category as $key => $cate)


							<div class="panel panel-default">
								<div class="panel-heading">
									
									<h4 class="panel-title"><a href="{{URL::to('/danh-muc-san-pham/' .$cate->category_slug)}}">{{ $cate->name }}</a></h4>
								</div>

							</div>
							@endforeach
						</div><!--/category-products-->
						

						
						<h2>Thương Hiệu</h2>
						<div class="panel-group category-products" id="accordian"><!--brand-productsr-->
							@foreach ($brand as $key => $brand)


							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title"><a href="{{URL::to('/thuong-hieu-san-pham/' .$brand->brand_slug)}}">{{ $brand->name }}</a></h4>
								</div>

							</div>
							@endforeach
						</div><!--/brand-products-->
						

						<form action="{{ url()->current() }}" method="GET" id="price-filter-form">
						<div class="price-range">
							<h2>Tầm Giá</h2>
							<div class="text-center">
						<span id="price-range-text" style="color:#d70018;">
							@if(request()->has('min_price') || request()->has('max_price'))
								{{ number_format(request('min_price',0), 0, ',', '.') }} ₫
								–
								{{ number_format(request('max_price',100000000), 0, ',', '.') }} ₫
							@else
								Tất cả mức giá
							@endif
						</span>					 
									<input type="text"
										class="span2"
										data-slider-min="0"
										data-slider-max="100000000"
										data-slider-step="500000"
										data-slider-value="[{{ request('min_price', 0) }},{{ request('max_price', 100000000) }}]"
										id="sl2">


								<input type="hidden" name="min_price" id="min_price">
								<input type="hidden" name="max_price" id="max_price">

								<br>
								<b class="pull-left">0 Tr</b>
								<b class="pull-right">100 Tr</b>
							</div>
						</div>
					</form>

					@foreach(request()->except(['min_price','max_price','page']) as $key => $value)
						<input type="hidden" name="{{ $key }}" value="{{ $value }}">
					@endforeach
					
					</div>
				</div>

				<div class="col-sm-9 padding-right">
					@yield('content')

				</div>
			</div>
		</div>
	</section>

	<footer id="footer"><!--Footer-->
		<div class="footer-top">
			<div class="container">

				<div class="col-sm-3">
					<div class="address">
						
					</div>
				</div>
			</div>
		</div>


		<div class="footer-widget">
			<div class="container">
				<div class="row">
					<div class="col-sm-2">
						<div class="single-widget">
							<h2>VỀ CHÚNG TÔI</h2>
							<ul class="nav nav-pills nav-stacked">
								<li><a href="#">Giới thiệu về UnK STORE</a></li>
								<li><a href="#">Quy chế hoạt động</a></li>
								<li><a href="#">Chất lượng dịch vụ</a></li>
								<li><a href="#">Góp ý - Khiếu nại</a></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="single-widget">
							<h2>CHÍNH SÁCH CHUNG</h2>
							<ul class="nav nav-pills nav-stacked">
								<li><a href="#">Điều khoản thanh toán</a></li>
								<li><a href="#">Chính sách bảo hành</a></li>
								<li><a href="#">Chính sách đổi trả</a></li>
								<li><a href="#">Chính sách bảo mật</a></li>

							</ul>
						</div>
					</div>

					<div class="col-sm-2">
						<div class="single-widget">
							<h2>LIÊN HỆ HỖ TRỢ</h2>
							<ul class="nav nav-pills nav-stacked">
								<li><a href="#">Hotline 1: 0983 567 891</a></li>
								<li><a href="#">Hotline 2: 0983 567 892</a></li>
								<li><a href="#">Hotline 3: 0983 567 893</a></li>
								<li><a href="#">Email: unkstore@gmail.com</a></li>

							</ul>
						</div>
					</div>
					<div class="col-sm-3 col-sm-offset-1">
						<div class="single-widget">
							<h2>CẬP NHẬT THÔNG TIN</h2>
							<form action="#" class="searchform">
								<input type="text" placeholder="Nhập địa chỉ Email của bạn" />
								<button type="submit" class="btn btn-default"><i class="fa fa-arrow-circle-o-right"></i></button>
								<p>Nhận những cập nhật mới nhất từ trang của chúng tôi và luôn bắt kịp xu hướng!</p>
							</form>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<div class="container">
				<div class="row">
					<p class="pull-left">Copyright © 2025 UnK STORE Inc. All rights reserved.</p>
					<p class="pull-right">Designed by <span><a target="_blank">UnK STORE</a></span></p>
				</div>
			</div>
		</div>

	</footer><!--/Footer-->


	<script src="{{ asset('frontend/js/jquery.js')}}"></script>
<script src="{{ asset('frontend/js/bootstrap-slider.min.js')}}"></script>

<script>
$(document).ready(function () {

    var slider = new Slider("#sl2", {
        min: 0,
        max: 100000000,
        step: 500000,
        range: true,
        value: [
            {{ request('min_price', 0) }},
            {{ request('max_price', 100000000) }}
        ],

        tooltip: 'show',        // ✅ HIỆN TOOLTIP
        tooltip_split: true,    // ✅ HIỆN 2 GIÁ (min & max)

        formatter: function (value) {
            // format tooltip sang VNĐ
            return value.toLocaleString('vi-VN') + ' ₫';
        }
    });

    slider.on("slide", function (value) {
        // cập nhật text realtime khi rê
        $('#price-range-text').text(
            value[0].toLocaleString('vi-VN') + ' ₫ – ' +
            value[1].toLocaleString('vi-VN') + ' ₫'
        );
    });

    slider.on("slideStop", function (value) {
        $('#min_price').val(value[0]);
        $('#max_price').val(value[1]);
        $('#price-filter-form').submit();
    });

});
</script>



<script src="{{ asset('frontend/js/jquery.scrollUp.min.js')}}"></script>

<script src="{{ asset('frontend/js/jquery.prettyPhoto.js')}}"></script>
<script src="{{ asset('frontend/js/main.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('frontend/js/bootstrap-slider.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.js') }}"></script>
<script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/js/ai-chatbox.js') }}"></script>

@stack('js')
@include('pages.partials.ai_chatbox')
@include('pages.partials.compare_bar')

</body>

</html>