@extends('pages.layout')

@section('content')


<div class="product-detail-wrapper">
    <h2 class="title text-center">
        Chi Tiết Sản Phẩm
    </h2>
    <div class="row">

@if(session('success'))
    <div id="cart-alert" 
         style="background:#e60012; color:#fff; padding:12px 16px; border-radius:8px; 
                margin-bottom:16px; font-weight:500; font-size:15px; text-align:center;">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('cart-alert');
            if (alertBox) {
                alertBox.style.opacity = '0';
                alertBox.style.transform = 'translateY(-10px)';
                alertBox.style.transition = '0.5s';
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 1800);
    </script>
@endif

        <div class="col-sm-5">

            @php
                use Illuminate\Support\Facades\Storage;

                $imgs = [];

                if ($product->productImage) {
                    foreach (['image_1','image_2','image_3','image_4'] as $col) {
                        if ($product->productImage->{$col}) {
                            $imgs[] = Storage::url($product->productImage->{$col});
                        }
                    }
                }

                $main = $imgs[0] ?? asset('frontend/images/noimage.jpg');
            @endphp

            <div class="pd-main-image-box">
                <img id="pd-main-image" src="{{ $main }}" alt="{{ $product->name }}">
            </div>

            <div class="pd-thumbs">
                @foreach($imgs as $index => $img)
                    <div class="pd-thumb-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ $img }}" data-large="{{ $img }}">
                    </div>
                @endforeach
            </div>

        </div>

     
        <div class="col-sm-7">

            <h1 class="pd-info-title">{{ $product->name }}</h1>

            @php
                $salePrice = $product->discounted_price;   // null nếu không có ưu đãi
                $basePrice = (float) $product->price;
                $hasDiscount = !is_null($salePrice) && $salePrice < $basePrice;
            @endphp

            <div class="pd-price" style="display:flex; align-items:baseline; gap:12px;">
                <span>
                    @if($hasDiscount)
                        {{ number_format($salePrice, 0, ',', '.') }} VND
                    @else
                        {{ number_format($basePrice, 0, ',', '.') }} VND
                    @endif
                </span>

                @if($hasDiscount)
                    <span style="color:#999; font-size:16px; text-decoration:line-through;">
                        {{ number_format($basePrice, 0, ',', '.') }} VND
                    </span>
                @endif
            </div>



            <div class="pd-info-list">
                <p><span class="label">Thương hiệu:</span> {{ optional($product->brand)->name }}</p>
                <p><span class="label">Loại:</span> {{ optional($product->category)->name }}</p>
                <p><span class="label">Chất liệu dây:</span> {{ $product->strap_material }}</p>

                <p>
                    <span class="label">Kích thước mặt:</span>
                    {{ $product->dial_size ? $product->dial_size . ' mm' : '—' }}
                </p>

                @php
                    $genderText = [
                        'male' => 'Nam',
                        'female' => 'Nữ',
                        'unisex' => 'Unisex',
                    ][$product->gender] ?? '—';
                @endphp

                <p><span class="label">Giới tính:</span> {{ $genderText }}</p>

                <p class="label" style="margin-top: 10px;">Mô tả:</p>
                <div class="pd-description">
                    {{ $product->description }}
                </div>

            </div>

            <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline-block;">
                @csrf
                <input type="hidden" name="quantity" value="1">

                <button type="submit" class="pd-add-cart-btn">
                    <i class="fa fa-shopping-cart"></i> Thêm Vào Giỏ
                </button>
            </form>
            </div>
            </div>
            <div class="row">
        <div class="col-sm-12">

            {{-- ================== REVIEW ================== --}}
            <div class="product-reviews" style="margin-top:30px;">
                <h3>Đánh giá sản phẩm</h3>

                <p>Điểm trung bình: {{ $averageRating ?? 0 }}/5</p>

                @if(!empty($reviews) && $reviews->count() > 0)
                    @foreach($reviews as $review)
                        <div style="border-bottom:1px solid #eee; padding:8px 0;">
                        <strong>{{ $review->user->fullname ?? 'Khách' }}</strong>
                            - Rating: {{ $review->rating }}/5
                            <p>{{ $review->comment }}</p>
                            <small>{{ $review->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @endforeach
                @else
                    <p>Chưa có đánh giá nào.</p>
                @endif
            </div>

            {{-- ⭐ CHỌN SAO --}}
            <div class="star-rating" style="font-size:28px; cursor:pointer; margin-bottom:15px;">
                @for($i = 1; $i <= 5; $i++)
                    <i class="star" data-value="{{ $i }}">★</i>
                @endfor
                <span id="ratingText" style="margin-left:10px; font-weight:600;"></span>
            </div>

{{-- 📝 FORM GỬI ĐÁNH GIÁ (TABLE) --}}
<div id="reviewForm" style="display:none; margin-bottom:30px;">

    <h4>Viết đánh giá của bạn</h4>

    <form action="{{ route('reviews.store', $product->id) }}" method="POST">
        @csrf

        {{-- product_id --}}
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        {{-- rating --}}
        <input type="hidden" name="rating" id="ratingValue">

        <table class="table table-bordered review-table">
            <tbody>
                <tr>
                    <th style="width:180px;">Số sao</th>
                    <td>
                        <span id="ratingTextTable" style="font-weight:600;color:#e60012;"></span>
                    </td>
                </tr>

                <tr>
                    <th>Nội dung đánh giá *</th>
                    <td>
                        <textarea name="comment"
                                  rows="4"
                                  required
                                  class="form-control"
                                  placeholder="Nhập đánh giá của bạn"></textarea>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="text-right">
                        <button type="submit" class="btn btn-danger">
                            Gửi đánh giá
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

</div>



        </div>

    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const mainImg = document.getElementById('pd-main-image');
    const thumbs  = document.querySelectorAll('.pd-thumb-item img');

    thumbs.forEach(t => {
        t.addEventListener('click', () => {
            mainImg.src = t.dataset.large;

            document.querySelectorAll('.pd-thumb-item')
                .forEach(x => x.classList.remove('active'));

            t.parentElement.classList.add('active');
        });
    });
});
</script>

<style>

.pd-main-image-box {
    width: 100%;
    max-width: 480px;
    aspect-ratio: 1/1;
    border: 3px solid #e60012;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
}

#pd-main-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pd-thumbs {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.pd-thumb-item {
    width: 90px;
    height: 90px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: 0.2s;
}

.pd-thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pd-thumb-item.active,
.pd-thumb-item:hover {
    border-color: #e60012;
}

.pd-info-title {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 10px;
}

.pd-price {
    font-size: 26px;
    color: #e60012;
    font-weight: 700;
    margin-bottom: 20px;
}

.pd-info-list p {
    font-size: 15px;
    margin: 4px 0;
}

.pd-info-list .label {
    font-weight: 600;
    color: black;
    font-size: 15px;
}

.pd-description {
    text-align: justify;
    margin-top: 6px;
    line-height: 1.55;
    margin-bottom: 20px;
}

.pd-add-cart-btn {
    background: #e60012;
    color: #fff;
    padding: 10px 20px;
    border: 2px solid #ff0000ff;
    font-size: 17px; 
    border-radius: 3px;
    margin-bottom: 20px;
}

.pd-add-cart-btn:hover {
    background: #ffffffff;
    color: #ff0000ff;
    padding: 10px 20px;
    border: 2px solid #ff0000ff;
    border-radius: 3px;
    font-size: 17px;
    margin-bottom: 20px;
}
</style>
<script>
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function () {
        const value = this.dataset.value;

        document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
        for (let i = 0; i < value; i++) {
            document.querySelectorAll('.star')[i].classList.add('active');
        }

        document.getElementById('ratingValue').value = value;
        document.getElementById('ratingText').innerText =
            ['Rất tệ','Tệ','Tạm ổn','Tốt','Rất tốt'][value - 1];

        document.getElementById('reviewForm').style.display = 'block';
    });
});
</script>
<style>
.product-review-section{margin-top:50px}
.review-wrapper{padding:30px;background:#fff;border-top:3px solid #e60012}
.single-review{border-bottom:1px solid #eee;padding:10px 0}
.review-rating{color:#e60012;font-weight:600;margin-left:8px}
.star-rating{font-size:28px;cursor:pointer;margin:15px 0}
.star.active{color:#e60012}
</style>
@endsection
