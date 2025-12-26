@extends('pages.admin_layout')
@section('admin_content')

@php
    $storageDetail = optional($product->storageDetail);
    $storage       = optional($storageDetail->storage);
    $images        = $product->productImage;

    // ====== ƯU ĐÃI ======
    $hasDiscount = !empty($product->discount_id) && !is_null($product->discount_rate);
    $discountRate = $hasDiscount ? (int)$product->discount_rate : null;

    $discountPrice = null;
    if ($hasDiscount && $discountRate > 0) {
        $discountPrice = (float)$product->price * (100 - $discountRate) / 100;
    }
@endphp

<style>
    .product-section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .product-image-large {
        width: 100%;
        height: 260px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
    .info-row {
        display: flex;
        border-bottom: 1px solid #eee;
        padding: 6px 0;
    }
    .info-label {
        font-weight: 600;
        width: 180px;
    }
    .badge-status {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
</style>

<div class="row">
    <div class="col-lg-12">

        <section class="panel">
            <header class="panel-heading" style="color:#000; font-weight:600;">
                Chi tiết sản phẩm: {{ $product->name }}
            </header>

            <div class="panel-body">

                {{-- ================== ẢNH ================== --}}
                <h4 class="product-section-title">Ảnh sản phẩm</h4>

                <div class="row" style="margin-bottom:20px;">
                    @foreach ([1,2,3,4] as $i)
                        @php $field = "image_{$i}"; @endphp
                        <div class="col-md-3 col-sm-6" style="margin-bottom:15px; text-align:center;">

                            @if($images && $images->$field)
                                <img src="{{ asset('storage/' . $images->$field) }}"
                                     class="product-image-large">
                            @else
                                <div style="
                                    width:100%;
                                    height:260px;
                                    border:1px dashed #ccc;
                                    border-radius:6px;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#aaa;">
                                    Không có ảnh
                                </div>
                            @endif

                            <div style="margin-top:5px; font-size:13px;">
                                Ảnh {{ $i }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ================== THÔNG TIN + MÔ TẢ ================== --}}
                <h4 class="product-section-title">Thông tin sản phẩm</h4>

                <div class="row">

                    {{-- CỘT TRÁI --}}
                    <div class="col-md-6">
                        <div class="well" style="padding:12px 15px;">

                            <div class="info-row">
                                <div class="info-label">Tên sản phẩm</div>
                                <div>{{ $product->name }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Giá bán</div>
                                <div>{{ number_format($product->price, 0, ',', '.') }} đ</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Ưu đãi</div>
                                <div>
                                    @if($product->discount_label)
                                        <span class="label label-info">{{ $product->discount_label }}</span>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Giá sau ưu đãi</div>
                                <div>
                                    @if(!is_null($product->discounted_price))
                                        {{ number_format($product->discounted_price, 0, ',', '.') }} đ
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>


                            <div class="info-row">
                                <div class="info-label">Số lượng tồn kho</div>
                                <div>{{ $product->quantity }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Trạng thái bán</div>
                                <div>
                                    @if ($product->stock_status === 'selling')
                                        <span class="badge-status label label-success">Đang bán</span>
                                    @elseif ($product->stock_status === 'sold_out')
                                        <span class="badge-status label label-default">Hết hàng</span>
                                    @else
                                        <span class="badge-status label label-danger">Ngừng bán</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Hiển thị</div>
                                <div>
                                    @if($product->status)
                                        <span class="badge-status label label-success">Hiện</span>
                                    @else
                                        <span class="badge-status label label-default">Ẩn</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Loại đồng hồ</div>
                                <div>{{ optional($product->category)->name ?? '—' }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Thương hiệu</div>
                                <div>{{ optional($product->brand)->name ?? '—' }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Giới tính</div>
                                <div>
                                    @if($product->gender === 'male') Nam
                                    @elseif($product->gender === 'female') Nữ
                                    @elseif($product->gender === 'unisex') Unisex
                                    @else —
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Kích thước mặt</div>
                                <div>{{ $product->dial_size ? $product->dial_size . ' mm' : '—' }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Chất liệu dây</div>
                                <div>{{ $product->strap_material ?? '—' }}</div>
                            </div>

                        </div>
                    </div>

                    {{-- CỘT PHẢI: MÔ TẢ --}}
                    <div class="col-md-6">
                        <h6 class="product-section-title">Mô tả sản phẩm</h6>
                        <div class="well" style="height:100%; background:#f9f9f9; padding:12px 15px;">
                            {!! nl2br(e($product->description ?: 'Chưa có mô tả.')) !!}
                        </div>
                    </div>

                </div>

                {{-- ================== KHO ================== --}}
                <h4 class="product-section-title">Thông tin Lô / Kho</h4>

                <div class="well" style="padding:12px 15px;">

                    <div class="info-row">
                        <div class="info-label">Mã lô</div>
                        <div>{{ $storage->batch_code ?? '—' }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Tên trong kho</div>
                        <div>{{ $storageDetail->product_name ?? '—' }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Số lượng nhập</div>
                        <div>{{ $storageDetail->import_quantity }}</div>
                    </div>

                </div>

                {{-- ================== REVIEW ================== --}}
                <h4 class="product-section-title" style="margin-top:20px;">Đánh giá sản phẩm</h4>

                <div class="well" style="padding:12px 15px;">
                    <p><strong>Điểm trung bình:</strong> {{ $averageRating }}/5</p>

                    @if($reviews->isEmpty())
                        <p>Chưa có đánh giá nào.</p>
                    @else
                        @foreach($reviews as $review)
                            <div style="border-bottom:1px solid #eee; padding:8px 0;">
                                <strong>{{ $review->user ? $review->user->name : $review->guest_name }}</strong>
                                - <span>{{ $review->rating }}/5</span>
                                <p>{{ $review->comment }}</p>
                                <small>{{ $review->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        @endforeach
                    @endif
                </div>


                {{-- ================== NÚT ================== --}}
                <div style="margin-top:15px;">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                        <i class="fa fa-pencil"></i> Sửa sản phẩm
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>

            </div>
        </section>
    </div>
</div>

@endsection
