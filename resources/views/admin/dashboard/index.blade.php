@extends('pages.admin_layout')

@section('admin_content')

<div class="row">

    <!-- ===== THỐNG KÊ ===== -->
    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-body text-center">
                <h4>Khách hàng</h4>
                <h2>{{ $totalCustomers }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-success">
            <div class="panel-body text-center">
                <h4>Đơn hàng</h4>
                <h2>{{ $totalOrders }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-warning">
            <div class="panel-body text-center">
                <h4>Sản phẩm</h4>
                <h2>{{ $totalProducts }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-danger">
            <div class="panel-body text-center">
                <h4>Đánh giá</h4>
                <h2>{{ $totalReviews }}</h2>
            </div>
        </div>
    </div>

</div>

<hr>

<!-- ===== BIỂU ĐỒ ===== -->
<div class="row">

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                Doanh thu theo tháng
            </div>
            <div class="panel-body">
                <div id="revenue-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                Số lượng đơn hàng
            </div>
            <div class="panel-body">
                <div id="order-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function () {

    new Morris.Line({
        element: 'revenue-chart',
        data: {!! json_encode($revenueChart) !!},
        xkey: 'month',
        ykeys: ['value'],
        labels: ['Doanh thu (VNĐ)'],
        parseTime: false,
        resize: true
    });

    new Morris.Bar({
        element: 'order-chart',
        data: {!! json_encode($orderChart) !!},
        xkey: 'month',
        ykeys: ['value'],
        labels: ['Số đơn hàng'],
        parseTime: false,
        resize: true
    });

});
</script>

@endsection
