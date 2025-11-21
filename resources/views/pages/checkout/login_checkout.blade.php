@extends('pages.layout')
@section('content')
<section id="form"><!--form-->
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-sm-offset-1">
					<div class="login-form"><!--login form-->
						<h2>Đăng nhập tài khoản</h2>
						@if ($errors->any())
							<div class="alert alert-danger">
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
						<form action="{{ URL::to('/login-user') }}" method="POST">
                        {{ csrf_field() }}
							<input type="text" name="email" placeholder="Tài khoản (Email)" />
							<input type="password" name="password" placeholder="Password" />
							<span>
								<input type="checkbox" class="checkbox"> 
								Ghi nhớ đăng nhập
							</span>
							<span>
                                <a href="{{ url('/quen-mat-khau') }}">Quên mật khẩu</a>
                            </span>
							<button type="submit" class="btn btn-default">Đăng nhập</button>
						</form>
					</div><!--/login form-->
				</div>
				<div class="col-sm-1">
					<h2 class="or">Hoặc</h2>
				</div>
				<div class="col-sm-4">
					<div class="signup-form"><!--sign up form-->
						<h2>Đăng ký</h2>
						<form action="{{ URL::to('/add-user') }}" method= "POST">
                        {{ csrf_field() }}                          
							<input type="text" name="fullname" placeholder="Họ và tên"/>
							<input type="email" name="email" placeholder="Địa chỉ email"/>
							<input type="password" name="password" placeholder="Mật Khẩu"/>
                            <input type="text" name="phone" placeholder="Điện thoại"/>

							<button type="submit" class="btn btn-default">Đăng ký</button>
						</form>
					</div><!--/sign up form-->
				</div>
			</div>
		</div>
	</section><!--/form-->
@endsection