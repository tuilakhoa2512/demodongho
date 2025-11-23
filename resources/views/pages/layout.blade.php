<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>Home | UnK-Store</title>
	<link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/prettyPhoto.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/price-range.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/animate.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/main.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/responsive.css') }}" rel="stylesheet">

	<link rel="shortcut icon" href="{{ asset('frontend/images/ico/favicon.ico')}}">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="images/ico/apple-touch-icon-57-precomposed.png">
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
								<li><a href="#"><i class="fa fa-twitter"></i></a></li>
								<li><a href="#"><i class="fa fa-linkedin"></i></a></li>
								<li><a href="#"><i class="fa fa-dribbble"></i></a></li>
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
								<li><a href="{{ URL::to('/checkout') }}"><i class="fa fa-credit-card"></i>Thanh toán</a></li>
								
								<?php
								 }else{
									?>
									<li><a href="{{ URL::to('/login-checkout') }}"><i class="fa fa-credit-card"></i> Thanh Toán</a></li>
									<?php
								 }
								?>
								
								<li><a href="{{ URL::to('/show-cart') }}"><i class="fa fa-shopping-cart"></i>Giỏ Hàng</a></li>
								<?php
									$id = Session::get('id');
									$fullname = Session::get('fullname'); // Lấy tên người dùng
									$image = Session::get('images');
									?>

									@if($id != null)
										<li class="dropdown user-menu">
											<a href="#" class="dropdown-toggle">
												<i class="fa fa-user"></i> {{ $fullname }}
											</a>

											<ul class="dropdown-menu user-dropdown">
												<li><a href="{{ URL::to('/profile') }}"><i class="fa fa-info-circle"></i> Thông tin cá nhân</a></li>
												<li><a href="{{ URL::to('/my-orders') }}"><i class="fa fa-list-alt"></i> Đơn hàng</a></li>
												<li><a href="{{ URL::to('/logout-checkout') }}"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
											</ul>
										</li>
									@else
										<li><a href="{{ URL::to('/login-checkout') }}"><i class="fa fa-lock"></i> Đăng Nhập</a></li>
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
								<li class="dropdown"><a href="#">Shop<i class="fa fa-angle-down"></i></a>
									<ul role="menu" class="sub-menu">
										<li><a href="shop.html">Sản Phẩm</a></li>
										<li><a href="product-details.html">Product Details</a></li>
										<li><a href="checkout.html">Checkout</a></li>
										<li><a href="cart.html">Đơn Hàng</a></li>
										<li><a href="login.html">Login</a></li>
									</ul>
								</li>
								<li class="dropdown"><a href="#">Blog<i class="fa fa-angle-down"></i></a>
									<ul role="menu" class="sub-menu">
										<li><a href="blog.html">Blog List</a></li>
										<li><a href="blog-single.html">Blog Single</a></li>
									</ul>
								</li>
								<li><a href="404.html">Sales</a></li>
								<li><a href="contact-us.html">Contact</a></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-5">
						<form  action="{{ URL::to('/tim-kiem') }}" method="POST">
						{{ csrf_field() }}
						<div class="search_box pull-right">
							<input type="text" name="keywords_submit" placeholder="Tìm Kiếm sản phẩm" />
							<input type="submit" style="margin-top:0;color:#666" name="search_items" class="btn btn-primary btn-sm" value="Tìm Kiếm" >
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
									<h2>Free E-Commerce Template</h2>
									<p>Thời gian của bạn, phong cách của chúng tôi – Khám phá bộ sưu tập đồng hồ đẳng cấp tại [UnK STORE]! </p>
									<button type="button" class="btn btn-default get">Get it now</button>
								</div>
								<div class="col-sm-6">
									<img src="images/home/girl1.jpg" class="girl img-responsive" alt="" />
									<img src="images/home/pricing.png" class="pricing" alt="" />
								</div>
							</div>
							<div class="item">
								<div class="col-sm-6">
									<h1><span>UnK</span>-STORE</h1>
									<h2>100% Responsive Design</h2>
									<p>Thời gian của bạn, phong cách của chúng tôi – Khám phá bộ sưu tập đồng hồ đẳng cấp tại [UnK STORE]! </p>
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
									<h2>Free Ecommerce Template</h2>
									<p>Thời gian của bạn, phong cách của chúng tôi – Khám phá bộ sưu tập đồng hồ đẳng cấp tại [UnK STORE]! </p>
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
						<h2>Category</h2>
						<div class="panel-group category-products" id="accordian"><!--category-productsr-->
							@foreach ($category as $key => $cate)


							<div class="panel panel-default">
								<div class="panel-heading">
									<!-- Cơ, Pin, Thể Thao, Thông minh -->
									<h4 class="panel-title"><a href="{{URL::to('/danh-muc-san-pham/' .$cate->id)}}">{{ $cate->name }}</a></h4>
								</div>

							</div>
							@endforeach
						</div><!--/category-products-->

						<div class="brands_products"><!--brands_products-->
							<h2>Brands</h2>
							<div class="brands-name">
								<ul class="nav nav-pills nav-stacked">
									@foreach ($brand as $key => $brand)
									 <li><a href="{{URL::to('/thuong-hieu-san-pham/' .$brand->id)}}">
									  <span class="pull-right">(50)</span>{{ $brand->name }}</a></li>
									<!-- <li><a href="#"> <span class="pull-right">(56)</span>Cartier</a></li>
									<li><a href="#"> <span class="pull-right">(27)</span>Patek Philippe</a></li>
									<li><a href="#"> <span class="pull-right">(32)</span>Breitling</a></li>
									<li><a href="#"> <span class="pull-right">(5)</span>Puma</a></li>
									<li><a href="#"> <span class="pull-right">(9)</span>Casio</a></li>
									<li><a href="#"> <span class="pull-right">(4)</span>Adidas</a></li>
									<li><a href="#"> <span class="pull-right">(5)</span>BaBy-G</a></li>
									<li><a href="#"> <span class="pull-right">(6)</span>G-Shock</a></li>
									<li><a href="#"> <span class="pull-right">(10)</span>	</a></li>  -->
									@endforeach
								</ul>
							</div>
						</div><!--/brands_products-->

						<div class="price-range"><!--price-range-->
							<h2>Price Range</h2>
							<div class="well text-center">
								<input type="text" class="span2" value="" data-slider-min="0" data-slider-max="600" data-slider-step="5" data-slider-value="[250,450]" id="sl2"><br />
								<b class="pull-left">$ 0</b> <b class="pull-right">$ 600</b>
							</div>
						</div><!--/price-range-->

						<div class="shipping text-center"><!--shipping-->
							<img src="images/home/shipping.jpg" alt="" />
						</div><!--/shipping-->

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
						<img src="images/home/map.png" alt="" />
						<p>505 S Atlantic Ave Virginia Beach, VA(Virginia)</p>
					</div>
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
								<input type="text" placeholder="Your email address" />
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
	<script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
	<script src="{{ asset('frontend/js/jquery.scrollUp.min.js')}}"></script>
	<script src="{{ asset('frontend/js/price-range.js')}}"></script>
	<script src="{{ asset('frontend/js/jquery.prettyPhoto.js')}}"></script>
	<script src="{{ asset('frontend/js/main.js')}}"></script>
	
</body>

</html>