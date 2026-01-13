@extends('pages.admin_layout')

@section('admin_content')

<div class="container-fluid" px-4>

    {{-- ===== THỐNG KÊ ===== --}}
    <div class="row">

        <div class="col-md-3">
            <div class="panel panel-primary text-center" style="padding:35px;background-color:#ffeb85">
                <div class="panel-body">
                    <h3 style="font-size:30px; font-weight:700;">{{ $totalCustomers }}</h3>
                    <p style="font-size:20px; font-weight:600;">Khách Hàng</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
        <div class="panel panel-primary text-center" style="padding:35px;background-color:#ffa385">
                <div class="panel-body">
                    <h3 style="font-size:30px; font-weight:700;">{{ $totalOrders }}</h3>
                    <p style="font-size:20px; font-weight:600;">Đơn Hàng</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
        <div class="panel panel-primary text-center" style="padding:35px;background-color:#bcff85">
                <div class="panel-body">
                    <h3 style="font-size:30px; font-weight:700;">{{ $totalProducts }}</h3>
                    <p style="font-size:20px; font-weight:600;">Sản Phẩm Đang Bán</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
        <div class="panel panel-primary text-center" style="padding:35px;background-color:#eec7fc">
                <div class="panel-body">
                    <h3 style="font-size:30px; font-weight:700;">{{ $totalReviews }}</h3>
                    <p style="font-size:20px; font-weight:600;">Đánh Giá</p>
                </div>
            </div>
        </div>

    </div>

   
    {{-- ===== BIỂU ĐỒ ===== --}}
    <div class="row">
    <!-- Doanh thu -->
    <div class="col-md-6">
        <div class="dashboard-card">
            <h4 class="chart-title">Doanh Thu Theo Tháng</h4>
            <div id="revenue-chart" style="height: 300px;"></div>
        </div>
    </div>

    <!-- Số đơn hàng -->
    <div class="col-md-6">
        <div class="dashboard-card">
            <h4 class="chart-title">Số Đơn Hàng Theo Tháng</h4>
            <div id="order-chart" style="height: 300px;"></div>
        </div>
    </div>
</div>
<style>
.dashboard-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.chart-title {
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}
#revenue-chart,
#order-chart {
    height: 350px;
}

</style>



</div>

{{-- MORRIS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

<script>
$(document).ready(function () {

    new Morris.Line({
        element: 'revenue-chart',
        data: @json($revenueData),
        xkey: 'month',
        ykeys: ['total'],
        labels: ['Doanh thu'],
        parseTime: false,
        resize: true,
        lineColors: ['#1abc9c']
    });

    new Morris.Bar({
        element: 'order-chart',
        data: @json($orderData),
        xkey: 'month',
        ykeys: ['total'],
        labels: ['Đơn hàng'],
        resize: true,
        barColors: ['#3498db']
    });

});
</script>


@endsection
