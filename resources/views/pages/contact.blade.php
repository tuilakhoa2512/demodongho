@extends('pages.layout')
@section('content')

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

<div class="contact-wrapper">

    <h2 class="contact-title">Liên hệ với chúng tôi</h2>

    <div class="contact-info">
        <p><strong>Địa chỉ:</strong> 8 Cao Lỗ</p>
        <p><strong>Số điện thoại:</strong> 0123 456 789</p>
        <p><strong>Email:</strong> unkstore@gmail.com</p>
    </div>

    <hr>

    <div class="contact-form">
        <form action="">
            <label><strong>Tên của bạn</strong></label>
            <input type="text" placeholder="Nhập tên của bạn">

            <label><strong>Email</strong></label>
            <input type="email" placeholder="Nhập email">

            <label><strong>Nội dung cần liên hệ</strong></label>
            <textarea placeholder="Bạn muốn gửi gì đến UnK STORE?"></textarea>

            <button class="btn-send">Gửi</button>
        </form>
    </div>

</div>

@endsection
