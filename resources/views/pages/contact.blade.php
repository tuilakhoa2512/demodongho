@extends('pages.layout')
@section('content')
<div class="contact-wrapper">
    <h2 class="contact-title">Liên hệ với chúng tôi</h2>
    <div class="contact-info">
        <p><strong>Địa chỉ:</strong> 123 Dương Bá Trạc - Phường 4 - Quận 8 - TP. Hồ Chí Minh</p>
        <p><strong>Số điện thoại:</strong> 0983 567 891</p>
        <p><strong>Email:</strong> unkstore@gmail.com</p>
    </div>
    <hr>
    <div class="contact-form">
        <div class="contact-form">

            {{-- Thông báo gửi thành công --}}
            @if(session('success'))
                <p style="color: green; margin-bottom: 15px;">
                    {{ session('success') }}
                </p>
            @endif

            {{-- Hiển thị lỗi validate --}}
            @if($errors->any())
                <ul style="color:red; margin-bottom:15px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form action="{{ route('contact.send') }}" method="POST">
                @csrf

                <label><strong>Tên của bạn</strong></label>
                <input type="text"
                    name="name"
                    placeholder="Nhập tên của bạn"
                    required>

                <label><strong>Email</strong></label>
                <input type="email"
                    name="email"
                    placeholder="Nhập email"
                    required>

                <label><strong>Nội dung cần liên hệ</strong></label>
                <textarea name="message"
                        placeholder="Bạn muốn gửi gì đến UnK STORE?"
                        required></textarea>
                <button type="submit" class="btn-send">Gửi</button>
            </form>
        </div>
    </div>
</div>
<style>
    /* Ẩn sidebar chỉ ở trang Contact */
    .left-sidebar {
        display: none !important;
    }
    .col-sm-9.padding-right {
        width: 100% !important;
    }

    /* FORM STYLE */
    .contact-wrapper {
        max-width: 800px;
        margin: 40px auto;
        background: #fff;
        border: 1px solid #ddd;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    .contact-info p {
        font-size: 16px;
        margin-bottom: 8px;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 10px 12px;
        margin-bottom: 15px;
        font-size: 15px;
        font-family: Roboto, sans-serif;
    }

    .contact-form textarea {
        height: 150px;
        resize: vertical;
    }

    .btn-send {
        width: 100%;
        background: #d70018;
        color: #fff;
        padding: 12px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-send:hover {
        background: #b00012;
    }

    h2.contact-title {
        text-align: center;
        margin-bottom: 25px;
        font-weight: 600;
    }
</style>
@endsection
