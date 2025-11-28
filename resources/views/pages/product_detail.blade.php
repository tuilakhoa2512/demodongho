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

            <div class="pd-price">
                {{ number_format($product->price, 0, ',', '.') }} VND
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
    border-radius: 5px;
    font-size: 17px; 
    margin-bottom: 20px;
}

.pd-add-cart-btn:hover {
    background: #ffffffff;
    color: #ff0000ff;
    padding: 10px 20px;
    border: 2px solid #ff0000ff;
    border-radius: 5px;
    font-size: 17px;
    margin-bottom: 20px;
}
</style>

@endsection
