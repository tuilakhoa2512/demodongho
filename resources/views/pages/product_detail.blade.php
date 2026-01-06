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
                $basePrice  = (float) $product->price;
                $finalPrice = (float) ($product->final_price ?? 0);
                $hasDiscount = $finalPrice > 0 && $finalPrice < $basePrice;
                @endphp

                <div class="pd-price">
                @if($hasDiscount)
                    <span>{{ number_format($finalPrice, 0, ',', '.') }} VND</span>
                    <span style="color:#999;font-size:16px;text-decoration:line-through;">
                        {{ number_format($basePrice, 0, ',', '.') }} VND
                    </span>
                @else
                    <span>{{ number_format($basePrice, 0, ',', '.') }} VND</span>
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
    @php
            $hasReviewed = false;
            if (session('id') && $reviews) {
                $hasReviewed = $reviews->where('user_id', session('id'))->isNotEmpty();
            }
        @endphp

        <div class="product-reviews" style="margin-top:40px;">
            <h2 class="title text-center">Đánh giá sản phẩm</h2>

            <p style="font-size:20px;color:#D70018;font-weight:700;">
                Điểm Trung Bình: {{ $averageRating ?? 0 }}/5★
            </p>

            {{-- ===== FORM ===== --}}
            @if($hasReviewed)
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Bạn đã đánh giá sản phẩm này rồi.
                </div>
            @else
                <div class="product-review-section">

                    <div class="star-rating">
                        @for($i=1;$i<=5;$i++)
                            <i class="star" data-value="{{ $i }}">★</i>
                        @endfor
                        <span id="ratingText"></span>
                    </div>

                    <div id="reviewForm" style="display:none;">
                        <form action="{{ route('reviews.store',$product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="rating" id="ratingValue">

                            <textarea name="comment" rows="4" class="form-control"
                                    placeholder="Nhập đánh giá của bạn" required></textarea>

                            <button type="submit" class="btn btn-danger" style="margin-top:10px;">
                                Gửi đánh giá
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        

        @if($reviews && $reviews->count() > 0)
            @foreach($reviews as $review)
                <div class="single-review">

                    <strong>{{ $review->user->fullname ?? 'Khách' }}</strong>

                    {{-- ⭐ SAO --}}
                    <div class="review-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                        @endfor
                    </div>

                    <p>{{ $review->comment }}</p>
                    <small>{{ $review->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</small>
                </div>
            @endforeach
        @else
            <p>Chưa có đánh giá nào.</p>
        @endif
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
