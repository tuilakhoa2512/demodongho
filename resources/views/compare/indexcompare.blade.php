@extends('pages.layout')
@section('content')

<h2 class="title text-center">So sánh sản phẩm</h2>

<div class="text-center" style="margin-top:20px;">
    <a href="{{ route('compare.clear') }}" class="btn btn-danger">
        Xoá tất cả sản phẩm so sánh
    </a>
</div>

<div class="compare-page">
    <div class="row" style="margin-top:30px;">

        {{--SP1 --}}
        <div class="col-sm-6">
            @if($sp1)
                @include('pages.partials.product_card', [
                    'product'     => $sp1,
                    'hideActions' => true
                ])

                <div class="text-center" style="margin-top:10px;">
                    <a href="{{ route('compare.remove','sp1') }}"
                       class="btn btn-warning btn-sm">
                        Xoá sản phẩm 1
                    </a>
                </div>
            @else
                <div style="border:2px dashed #ccc; padding:50px; text-align:center;">
                    <p>Chưa chọn sản phẩm 1</p>
                    <a href="{{ route('compare.select','sp1') }}"
                       class="btn btn-primary">
                        Chọn sản phẩm 1
                    </a>
                </div>
            @endif
        </div>

        {{-- ================= SLOT 2 ================= --}}
        <div class="col-sm-6">
            @if($sp2)
                @include('pages.partials.product_card', [
                    'product'     => $sp2,
                    'hideActions' => true
                ])

                <div class="text-center" style="margin-top:10px;">
                    <a href="{{ route('compare.remove','sp2') }}"
                       class="btn btn-warning btn-sm">
                        Xoá sản phẩm 2
                    </a>
                </div>
            @else
                <div style="border:2px dashed #ccc; padding:50px; text-align:center;">
                    <p>Chưa chọn sản phẩm 2</p>
                    <a href="{{ route('compare.select','sp2') }}"
                       class="btn btn-primary">
                        Chọn sản phẩm 2
                    </a>
                </div>
            @endif
        </div>

    </div>

    {{-- ================= BẢNG SO SÁNH ================= --}}
    @if($sp1 && $sp2)
    <hr style="margin:40px 0;">

    <table class="table table-bordered text-center" style="background:#fff;">
        <tr style="background:#f5f5f5;">
            <th width="25%">Thuộc tính</th>
            <th width="37.5%">{{ $sp1->name }}</th>
            <th width="37.5%">{{ $sp2->name }}</th>
        </tr>

        <tr>
            <td>Loại đồng hồ</td>
            <td>{{ optional($sp1->category)->name }}</td>
            <td>{{ optional($sp2->category)->name }}</td>
        </tr>

        <tr>
            <td>Thương hiệu</td>
            <td>{{ optional($sp1->brand)->name }}</td>
            <td>{{ optional($sp2->brand)->name }}</td>
        </tr>

        <tr>
            <td>Chất liệu dây</td>
            <td>{{ $sp1->strap_material }}</td>
            <td>{{ $sp2->strap_material }}</td>
        </tr>

        <tr>
            <td>Size mặt</td>
            <td>{{ $sp1->dial_size }}</td>
            <td>{{ $sp2->dial_size }}</td>
        </tr>

        <tr>
            <td>Giới tính</td>
            <td>{{ $sp1->gender }}</td>
            <td>{{ $sp2->gender }}</td>
        </tr>

        <tr>
            <td>Giá</td>
            <td>{{ number_format($sp1->price,0,',','.') }} VND</td>
            <td>{{ number_format($sp2->price,0,',','.') }} VND</td>
        </tr>

        <tr>
            <td>Mô tả</td>
            <td>{{ $sp1->description }}</td>
            <td>{{ $sp2->description }}</td>
        </tr>
    </table>
    @endif
</div>
<style>
.compare-page .col-sm-4 {
    width: 100% !important;
    float: none !important;
}
.btn-danger{
    background: #d70018 !important;
}
.btn-sm{
    background: #d70018 !important;
}
.compare-page .product-overlay,
.compare-page .choose {
    display: none !important;
}
</style>
@endsection
