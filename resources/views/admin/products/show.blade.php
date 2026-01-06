@extends('pages.admin_layout')
@section('admin_content')

@php
    $storageDetail = optional($product->storageDetail);
    $storage       = optional($storageDetail->storage);
    $images        = $product->productImage;

    // ✅ NEW: ƯU ĐÃI (PromotionService gắn runtime từ controller)
    $hasPromo   = !empty($product->promo_has_sale);
    $promoName  = $product->promo_name ?? null;
    $promoLabel = $product->promo_label ?? null;

    // final_price luôn fallback về price để view không bị null
    $finalPrice = isset($product->final_price) ? (float)$product->final_price : (float)$product->price;

    // Nếu muốn “chắc chắn” là có giảm thật mới hiện giá sau ưu đãi:
    $showFinalPrice = $hasPromo && ($finalPrice < (float)$product->price);
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

                            {{-- ✅ NEW: Ưu đãi (promotion) --}}
                            <div class="info-row">
                                <div class="info-label">Ưu đãi</div>
                                <div>
                                    @if($hasPromo)
                                        <span class="label label-info">
                                            {{ $promoName ?? 'Ưu đãi' }}
                                            @if(!empty($promoLabel))
                                                ({{ $promoLabel }})
                                            @endif
                                        </span>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>

                            {{-- ✅ NEW: Giá sau ưu đãi (đồng bộ với index) --}}
                            <div class="info-row">
                                <div class="info-label">Giá sau ưu đãi</div>
                                <div>
                                    @if($showFinalPrice)
                                        <strong style="color:#e60012;">
                                            {{ number_format($finalPrice, 0, ',', '.') }} đ
                                        </strong>
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

                <div class="well" style="">
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


                {{-- ================== FORM ĐÁNH GIÁ HOẶC THÔNG BÁO ================== --}}
                @php
                    $canReview = false;
                    $notPurchased = false;

                    if(session()->has('id')) {
                        $userId = session('id');

                        $lastOrderDetail = \App\Models\OrderDetail::where('product_id', $product->id)
                            ->whereHas('order', fn($q) => $q->where('user_id', $userId)->where('status','success'))
                            ->orderByDesc('id')->first();

                        if(!$lastOrderDetail) {
                            $notPurchased = true;
                        } else {
                            $completedAt = $lastOrderDetail->order->updated_at;
                            if($completedAt && $completedAt->diffInMinutes(now()) >= 2) {
                                $canReview = true;
                            }
                        }
                    } else {
                        $notPurchased = true;
                    }
                @endphp

                @if($canReview)
                    <div class="well mt-3">
                        <h5>Viết đánh giá</h5>
                        <form action="{{ route('reviews.store', $product->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Số sao:</label>
                                <select name="rating" class="form-control" required>
                                    @for($i=1;$i<=5;$i++)
                                        <option value="{{ $i }}">{{ $i }} ★</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nhận xét:</label>
                                <textarea name="comment" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Gửi đánh giá</button>
                        </form>
                    </div>
                @elseif($notPurchased)
                    <div class="alert alert-info mt-3">
                        Bạn cần mua sản phẩm và đơn phải hoàn thành mới có thể đánh giá.
                    </div>
                @else
                    <div class="alert alert-warning mt-3">
                        Bạn cần đợi 2 phút sau khi đơn hoàn thành để có thể đánh giá.
                    </div>
                @endif




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
