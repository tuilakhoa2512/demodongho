
<!DOCTYPE html>
<head>
<title>Dashboard UnK Store</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- bootstrap-css -->
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}" >
<!-- //bootstrap-css -->
<!-- Custom CSS -->
<link href="{{ asset('backend/css/style.css') }}" rel='stylesheet' type='text/css' />
<link href="{{ asset('backend/css/style-responsive.css') }}" rel="stylesheet"/>
<!-- font CSS -->
<link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<!-- font-awesome icons -->
<link rel="stylesheet" href="{{ asset('backend/css/font.css') }}" type="text/css"/>
<link href="{{ asset('backend/css/font-awesome.css') }}" rel="stylesheet"> 
<link rel="stylesheet" href="{{ asset('backend/css/morris.css') }}" type="text/css"/>
<!-- calendar -->
<link rel="stylesheet" href="{{ asset('backend/css/monthly.css') }}">
<!-- //calendar -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- //font-awesome icons -->
<script src="{{ asset('backend/js/jquery2.0.3.min.js') }}"></script>
<script src="{{ asset('backend/js/raphael-min.js') }}"></script>
<script src="{{ asset('backend/js/morris.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

body {
            background-image: url('https://blogger.googleusercontent.com/img/a/AVvXsEjc7XWcbeT6MGXJVKchMn5RTlbgLC7v052UpWG_Yms8cLYZeBYXETmrJCUjc3LbX631gDmYxxpytdqxRATP-6y9SJE8iZ9ctab92Ge2Ymv3UiFuo0j-4N5pi-mmWsAeBv_ovL4sW3TnDxokAMg92yNc0QQ-706_JGBiDt5kvNvx9ZOFf1Urar3xl4hDYX-y'); /* Thay đường dẫn này bằng ảnh nền thực tế */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            color: #000000;
        }
      
</style>
</head>
<body>
@php
    $role = (int) Session::get('admin_role_id');
@endphp

<section id="container">
<!--header start-->
<header class="header fixed-top clearfix">
<!--logo start-->
<div class="brand">
    <a href="{{ URL::to('trang-chu') }}" class="logo">
        UnK STORE
    </a>
    <div class="sidebar-toggle-box">
        <div class="fa fa-bars"></div>
    </div>
</div>
<!--logo end-->
<div class="nav notify-row" id="top_menu">
    <!--  notification start -->
    <ul class="nav top-menu">
        <!-- settings start -->
        
        <!-- settings end -->
        
        
    </ul>
    <!--  notification end -->
</div>
<div class="top-nav clearfix">
    <!--search & user info start-->
    <ul class="nav pull-right top-menu">
        <li>
            <input type="text" class="form-control search" placeholder=" Search">
        </li>
        <!-- user login dropdown start-->
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img alt="" src="{{asset('backend/images/kem.png')}}">
				<span class="username">{{ Session::get('admin_name') }}</span>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu extended logout">
                <li><a href="#"><i class=" fa fa-suitcase"></i>Hồ Sơ</a></li>
                <li><a href="#"><i class="fa fa-cog"></i> Cài đặt</a></li>
                <li><a href="{{ URL::to('/logout') }}"><i class="fa fa-key"></i> Đăng xuất</a></li>
            </ul>
        </li>
        <!-- user login dropdown end -->
       
    </ul>
    <!--search & user info end-->
</div>
</header>
<!--header end-->
<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class="active" href="{{ URL::to('/dashboard') }}">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                
                @if (in_array($role, [1,3,4]))
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-user"></i>
                        <span>Quản Lý Người Dùng</span>
                    </a>
                    <ul class="sub">
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.users.index') }}" style="width: 160px; text-align: left;">
                                Tài Khoản Khách Hàng
                            </a>
                        </li> 
                        @if (in_array($role, [1,3]))
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.staff.index') }}" style="width: 160px; text-align: left;">
                                Tài Khoản Nhân Sự
                            </a>
                        </li>
                        @endif
                        @if ($role === 1)
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.users.create') }}" style="width: 160px; text-align: left;">
                                Thêm người dùng
                            </a>
                        </li> 
                        @endif                                           
                    </ul>                  
                </li>
                @endif


                @if (in_array($role, [1,3,4]))
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-th"></i>
                        <span>Loại Sản Phẩm</span>
                    </a>
                    <ul class="sub">
                    <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/all-product-type') }}" style="width: 160px; text-align: left;">
                                Danh Sách Loại
                            </a>
                    </li> 
                    <li style="display: flex; justify-content: center;">
                        <a href="{{ URL::to('/add-product-type') }}" style="width: 160px; text-align: left;">
                            Thêm Loại
                        </a>
                    </li>                           
                                                          
                    </ul>
                </li>
                @endif


                @if (in_array($role, [1,3,4]))
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-tasks"></i>
                        <span>Thương Hiệu</span>
                    </a>
                    <ul class="sub">
                    <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/all-brand-product') }}" style="width: 160px; text-align: left;">
                                Danh Sách Thương Hiệu
                            </a>
                        </li> 
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/add-brand-product') }}" style="width: 160px; text-align: left;">
                                Thêm Thương Hiệu
                            </a>
                        </li>                                                                   
                    </ul>
                </li>
                @endif


                @if (in_array($role, [1,3,5]))
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-database"></i>
                        <span>Lô Hàng Nhập</span>
                    </a>
                    <ul class="sub">
                    <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/admin/storages') }}" style="width: 160px; text-align: left;">
                                Danh Sách Lô Hàng
                            </a>
                        </li> 
                        <li style="display: flex; justify-content: center;">
                            <a href="{{  URL::to('/admin/storages/create') }}" style="width: 160px; text-align: left;">
                                Thêm Lô Hàng Mới
                            </a>
                        </li>                                             
                    </ul>
                </li>
                @endif


                @if (in_array($role, [1,3,5]))
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-archive"></i>
                        <span>Kho Hàng</span>
                    </a>
                    <ul class="sub">
                    <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.storage-details.index') }}" style="width: 160px; text-align: left;">
                                Toàn Bộ Hàng Trong Kho
                            </a>
                        </li> 
                        <li style="display: flex; justify-content: center;">
                            <a href="{{  route('admin.storage-details.pending')  }}" style="width: 160px; text-align: left;">
                                Chưa Bán
                            </a>
                        </li>  
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.storage-details.selling') }}" style="width: 160px; text-align: left;">
                                Đang Bán
                            </a>
                        </li> 
                        <li style="display: flex; justify-content: center;">
                            <a href="{{  route('admin.storage-details.sold-out')  }}" style="width: 160px; text-align: left;">
                                Bán Hết
                            </a>
                        </li>
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.storage-details.stopped') }}" style="width: 160px; text-align: left;">
                                Ngừng Bán
                            </a>
                        </li> 
                                           
                    </ul>
                </li>
                @endif


                @if (in_array($role, [1,3,4]))
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-cubes"></i>
                        <span>Sản Phẩm</span>
                    </a>
                    <ul class="sub">
                    <li style="display: flex; justify-content: center;">
                            <a href="{{  URL::to('/admin/products')  }}" style="width: 160px; text-align: left;">
                                Danh Sách Sản Phẩm
                            </a>
                        </li>
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/admin/products/create') }}" style="width: 160px; text-align: left;">
                                Thêm Sản Phẩm
                            </a>
                        </li> 
                    </ul>
                </li>
                @endif


                @if (in_array($role, [1,3,4]))
                <li class="sub-menu">
                    <a href="javascript:;">	
                        <i class=" fa fa-shopping-cart"></i>
                        <span>Đơn Hàng</span>
                    </a>
                    <ul class="sub">
                    <li style="display: flex; justify-content: center;">
                            <a href="{{ (URL::to('/admin/orders'))  }}" style="width: 160px; text-align: left;">
                                Danh Sách Đơn Hàng
                            </a>
                        </li>
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/admin/orders?status=pending') }}" style="width: 160px; text-align: left;">
                                Đang Chờ Xử Lý
                            </a>
                        </li> 
                        <li style="display: flex; justify-content: center;">
                            <a href="{{  URL::to('/admin/orders?status=confirmed')  }}" style="width: 160px; text-align: left;">
                                Đang Đóng Gói
                            </a>
                        </li>
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/admin/orders?status=shipping') }}" style="width: 160px; text-align: left;">
                                Đang Vận Chuyển
                            </a>
                        </li> 
                        <li style="display: flex; justify-content: center;">
                            <a href="{{  URL::to('/admin/orders?status=success')  }}" style="width: 160px; text-align: left;">
                                Đơn Hàng Thành Công
                            </a>
                        </li>
                        <li style="display: flex; justify-content: center;">
                            <a href="{{ URL::to('/admin/orders?status=canceled') }}" style="width: 160px; text-align: left;">
                                Đơn Bị Huỷ
                            </a>
                        </li> 
        
                    </ul>
                </li>
                @endif

                <li class="sub-menu">
                    <a href="javascript:;">	
                        <i class="fa fa-ticket"></i>
                        <span>Ưu Đãi</span>
                    </a>
                    <ul class="sub">

                        <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.promotions.index') }}"
                            style="width: 160px; text-align: left;">
                                Danh Sách Ưu Đãi
                            </a>
                        </li>

                        <li style="display: flex; justify-content: center;">
                            <a href="{{ route('admin.promotions.create') }}"
                            style="width: 160px; text-align: left;">
                                Thêm Ưu Đãi
                            </a>
                        </li>

                    </ul>
                </li>



                @if (in_array($role, [1,3,4]))

                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class=" fa fa-comments"></i>
                        <span>Đánh Giá</span>
                    </a>
                    <ul class="sub">
                    <li style="display: flex; justify-content: center;">
                            <a href="{{  route('admin.reviewuser.index')  }}" style="width: 160px; text-align: left;">
                                Danh Sách Reviews
                            </a>
                    </li>         
                    </ul>
                </li>
                @endif

                
                
            </ul>            </div>
        <!-- sidebar menu end-->
    </div>
</aside>

<!--sidebar end-->
<!--main content start-->
<div id="main-content">
    <section class="wrapper">
        @include('pages.partials.alert')
        @yield('admin_content')
    </section>

 <!-- footer -->
		
<footer class="footer">
    <div class="wthree-copyright">
        <p>© 2025. All rights reserved | Design by UnK STORE</p>
    </div>
</footer>
  <!-- / footer -->
<!--main content end-->
</div>

<script src="{{ asset('backend/js/bootstrap.js')}}"></script>
<script src="{{ asset('backend/js/jquery.dcjqaccordion.2.7.js')}}"></script>
<script src="{{ asset('backend/js/scripts.js')}}"></script>
<script src="{{ asset('backend/js/jquery.slimscroll.js')}}"></script>
<script src="{{ asset('backend/js/jquery.nicescroll.js')}}"></script>
<script src="{{ asset('backend/js/jquery.scrollTo.js')}}"></script>

</body>
</html>
