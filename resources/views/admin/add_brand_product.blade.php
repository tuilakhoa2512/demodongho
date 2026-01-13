@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Thêm thương hiệu sản phẩm
            </header>

            <div class="panel-body">
            
                <div class="position-center">
                    
                    <form action="{{ URL::to('/save-brand-product') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Tên Thương Hiệu</label>
                            <input type="text" name="brand_product_name" class="form-control"maxlength="50">
                        </div>

                        <div class="form-group">
                            <label>Mô Tả</label>
                            <textarea name="brand_product_desc" rows="5" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Đường dẫn (brand slug)</label>
                            <input type="text" name="brand_product_slug" class="form-control" placeholder="Tự tạo nếu để trống">
                        </div>


                        <div class="form-group">
                            <label for="BrandProductDesc">Hiển Thị</label>
                            <select name="brand_product_status" class="form-control input-sm m-bot15">

                            <option value="1">Hiển Thị</option>
                            <option value="0">Ẩn</option>

                            </select>

                        </div>

                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" name="brand_product_image" class="form-control">
                        </div>

                        <button type="submit" name="add_brand_product" class="btn btn-info">Thêm Thương Hiệu</button>

                    </form>

                </div>
            </div>

        </section>
    </div>
</div>

<script>
document.querySelector("input[name='brand_product_name']").addEventListener("keyup", function() {
    let name = this.value;
    
    // tạo slug
    let slug = name.toLowerCase() //chuyển từ chũ cái thành chữ thường
    // replace xử lý các ký tự có dấu Việt Nam và thay thế chúng bằng ký tự không dấu tương ứng.
        .replace(/á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/g, "a")
        .replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/g, "e")
        .replace(/í|ì|ỉ|ĩ|ị/g, "i")
        .replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/g, "o")
        .replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/g, "u")
        .replace(/ý|ỳ|ỷ|ỹ|ỵ/g, "y")
        .replace(/đ/g, "d")
        .replace(/[^a-z0-9\-]/g, "-") //thay thế tất cả ký tự không phải chữ cái (a-z), chữ số (0-9) và dấu gạch ngang bằng một dấu gạch ngang.
        .replace(/-+/g, "-") //thay thế nhiều dấu gạch ngang liên tiếp thành một dấu gạch ngang.
        .replace(/^-+|-+$/g, ""); // xóa dấu gạch ngang không cần thiết ở đầu và cuối chuỗi.

    document.getElementById("brand_slug").value = slug;
});
</script>

@endsection
