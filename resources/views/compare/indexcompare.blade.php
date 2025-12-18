@extends('pages.layout')
@section('content')

<h2 class="title text-center">So sánh sản phẩm</h2>

<div class="text-center" style="margin-top:20px;">
    <a href="{{ route('compare.clear') }}"
       class="btn btn-danger">
        Xoá tất cả sản phẩm so sánh
    </a>
</div>
<style>
.product-img-box {
    position: relative;
    width: 100%;
    height: 240px;
    overflow: hidden;
    padding-top: 6px;
}


.product-img-box .hover-img {
    opacity: 0;
}

.product-img-box:hover .main-img {
    opacity: 0;
}

.product-img-box:hover .hover-img {
    opacity: 1;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 280px; 
    background: rgba(0,0,0,0.0);
    transition: 0.4s ease-in-out;
}

.product-overlay .overlay-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
}

.product-overlay .overlay-content {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 15px;
    text-align: center;
    color: #fff;
}

.product-overlay:hover {
    background: rgba(0,0,0,0.3);
}
.recommended_items {
    position: relative;
    overflow: visible !important;
}

.recommended-item-control {
    position: absolute;
    top: 30%;
    transform: translateY(-50%);
    background: #d70018;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    z-index: 9999;
}

.left.recommended-item-control {
    left: -25px;
}

.right.recommended-item-control {
    right: -25px;
}

</style>
<div class="row" style="margin-top:30px;">

    {{-- ================= SLOT 1 ================= --}}
    <div class="col-sm-6">
        <div class="product-image-wrapper text-center">

            @if($sp1)
                <div class="single-products">
                    <div class="productinfo text-center">

                        {{-- ẢNH GIỐNG SHOW_BRAND – KHÔNG HOVER --}}
                        <div class="product-img-box" style="height:260px;">
                            <img src="{{ $sp1->main_image_url }}"
                                 alt="{{ $sp1->name }}"
                                 style="width:100%; height:260px; object-fit:cover; border-radius:8px;">
                        </div>

                        <h2 style="margin-top:12px;">
                            {{ number_format($sp1->price,0,',','.') }} VND
                        </h2>

                        <p>{{ $sp1->name }}</p>

                        <a href="{{ route('compare.remove','sp1') }}"
                           class="btn btn-warning btn-sm">
                            Xoá sản phẩm 1
                        </a>
                    </div>
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
    </div>

    {{-- ================= SLOT 2 ================= --}}
    <div class="col-sm-6">
        <div class="product-image-wrapper text-center">

            @if($sp2)
                <div class="single-products">
                    <div class="productinfo text-center">

                        {{-- ẢNH GIỐNG SHOW_BRAND – KHÔNG HOVER --}}
                        <div class="product-img-box" style="height:260px;">
                            <img src="{{ $sp2->main_image_url }}"
                                 alt="{{ $sp2->name }}"
                                 style="width:100%; height:260px; object-fit:cover; border-radius:8px;">
                        </div>

                        <h2 style="margin-top:12px;">
                            {{ number_format($sp2->price,0,',','.') }} VND
                        </h2>

                        <p>{{ $sp2->name }}</p>

                        <a href="{{ route('compare.remove','sp2') }}"
                           class="btn btn-warning btn-sm">
                            Xoá sản phẩm 2
                        </a>
                    </div>
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

@endsection
