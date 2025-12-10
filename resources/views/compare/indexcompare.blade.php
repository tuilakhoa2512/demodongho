@extends('pages.layout')
@section('content')

<h2 class="title text-center">So sánh sản phẩm</h2>

<div class="row" style="margin-top: 30px;">

    {{-- SLOT 1 --}}
    <div class="col-sm-6 text-center">
        @if($sp1)
            <img src="{{ $sp1->main_image_url }}" width="250">
            <h3>{{ $sp1->name }}</h3>
            <p>Giá: <b>{{ number_format($sp1->price) }} VND</b></p>
            <a href="{{ route('compare.remove', 'sp1') }}" class="btn btn-danger">Xoá SP1</a>
        @else
        <div style="border:2px dashed #ccc; padding:40px;">
    <p>Chưa chọn sản phẩm 1</p>
    <a href="{{ route('compare.select', ['slot' => 'sp1']) }}" class="btn btn-primary">
        Chọn sản phẩm 1
    </a>
</div>

        @endif
    </div>

    {{-- SLOT 2 --}}
    <div class="col-sm-6 text-center">
        @if($sp2)
            <img src="{{ $sp2->main_image_url }}" width="250">
            <h3>{{ $sp2->name }}</h3>
            <p>Giá: <b>{{ number_format($sp2->price) }} VND</b></p>
            <a href="{{ route('compare.remove', 'sp2') }}" class="btn btn-danger">Xoá SP2</a>
        @else
        <div style="border:2px dashed #ccc; padding:40px;">
    <p>Chưa chọn sản phẩm 2</p>
    <a href="{{ route('compare.select', ['slot' => 'sp2']) }}" class="btn btn-primary">
        Chọn sản phẩm 2
    </a>
</div>

        @endif
    </div>

</div>

{{-- Nếu có đủ 2 sản phẩm thì hiện bảng so sánh --}}
@if($sp1 && $sp2)
    <hr style="margin: 40px 0;">

    <table class="table table-bordered text-center" style="background:#fff;">
        <tr style="background:#f5f5f5;">
            <th width="25%" style="text-align: center;">Thuộc tính</th>
            <th width="37.5%"  style="text-align: center;">{{ $sp1->name }}</th>
            <th width="37.5%"  style="text-align: center;">{{ $sp2->name }}</th>
        </tr>

        <tr>
            <td>Giá</td>
            <td>{{ number_format($sp1->price) }} VND</td>
            <td>{{ number_format($sp2->price) }} VND</td>
        </tr>

        <tr>
            <td>Mô tả</td>
            <td>{{ $sp1->description }}</td>
            <td>{{ $sp2->description }}</td>
        </tr>

        <tr>
            <td>Chất liệu dây</td>
            <td>{{ $sp1->strap_material }}</td>
            <td>{{ $sp2->strap_material }}</td>
        </tr>

        <tr>
            <td>Size mặt đồng hồ</td>
            <td>{{ $sp1->dial_size }}</td>
            <td>{{ $sp2->dial_size }}</td>
        </tr>

        <tr>
            <td>Giới tính</td>
            <td>{{ $sp1->gender }}</td>
            <td>{{ $sp2->gender }}</td>
        </tr>

        <tr>
            <td>Thương hiệu</td>
            <td>{{ optional($sp1->brand)->name }}</td>
            <td>{{ optional($sp2->brand)->name }}</td>
        </tr>

        <tr>
            <td>Danh mục</td>
            <td>{{ optional($sp1->category)->name }}</td>
            <td>{{ optional($sp2->category)->name }}</td>
        </tr>

        <tr>
            <td>Số lượng còn</td>
            <td>{{ $sp1->quantity }}</td>
            <td>{{ $sp2->quantity }}</td>
        </tr>

    </table>
@endif


@endsection
