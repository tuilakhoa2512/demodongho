@extends('pages.layout')
@section('content')
<section id="cart_items">
		<div class="container">
			<div class="breadcrumbs">
				<ol class="breadcrumb">
				  <li><a href="{{ URL::to('/') }}">Home</a></li>
				  <li class="active">Thanh toán giỏ hàng</li>
				</ol>
			</div><!--/breadcrums-->

			<div class="register-req">
				<p>Làm ơn đăng ký hoặc đăng nhập để thanh toán giỏ hàng và xem lại lịch sử mua hàng</p>
			</div><!--/register-req-->

			<div class="shopper-informations">
				<div class="row">

					<div class="col-sm-5 clearfix">
						<div class="bill-to">
							<p>Điền thông tin gửi hàng</p>
							<div class="form-one">
								<form>		
									<input type="text" placeholder="Email">
									
									<input type="text" placeholder="Họ và tên">
									<input type="text" placeholder="Địa chỉ">
									<input type="text" placeholder="Phone">
									<textarea name="message"  placeholder="Ghi chú đơn hàng của bạn" rows="16"></textarea>
								</form>
							</div>
							
						</div>
					</div>
					<div class="col-sm-4">
						<div class="order-message">
							<p>Ghi chú gửi hàng</p>
							<textarea name="message"  placeholder="Ghi chú đơn hàng của bạn" rows="16"></textarea>
							
						</div>	
					</div>					
				</div>
			</div>
			<div class="review-payment">
				<h2>Xem lại giỏ hàng</h2>
			</div>

			<div class="payment-options">
					<span>
						<label><input type="checkbox"> Direct Bank Transfer</label>
					</span>
					<span>
						<label><input type="checkbox"> Check Payment</label>
					</span>
					<span>
						<label><input type="checkbox"> Paypal</label>
					</span>
				</div>
		</div>
	</section> <!--/#cart_items-->
@endsection