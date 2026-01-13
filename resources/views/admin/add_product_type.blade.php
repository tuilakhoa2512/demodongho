@extends('pages.admin_layout')

@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Thêm loại sản phẩm
            </header>
            
            <div class="panel-body">
                @if (session('message'))
                    <script>
                        $(document).ready(function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: '{{ session('message') }}',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif

                @if ($errors->any())
                    <script>
                        $(document).ready(function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                html: `{!! implode('<br>', $errors->all()) !!}`,
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif

                <div class="position-center">
                    <form role="form" action="{{ URL::to('/save-product-type') }}" method="post">
                        {{ csrf_field() }}
                        
                        <div class="form-group">
                            <label for="productTypeName">Tên loại sản phẩm</label>
                            <input type="text" name="product_type_name" class="form-control" id="productTypeName" placeholder="Tên loại sản phẩm" maxlength="50">
                        </div>

                        <div class="form-group">
                            <label>Đường dẫn (slug)</label>
                            <input type="text" name="category_slug" id="category_slug" class="form-control" readonly>
                        </div>
                       
                        <div class="form-group">
                            <label for="productTypeDesc">Mô tả Loại sản phẩm</label>
                            <textarea style="resize: vertical; min-height: 150px;" name="product_type_desc" class="form-control" id="productTypeDesc" placeholder="Mô tả Loại sản phẩm"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="productTypeDesc">Trạng Thái</label>
                            <select name="product_type_status" class="form-control input-sm m-bot15">                   
                                <option value="1">Hiển Thị</option>
                                <option value="0">Ẩn</option>                                    
                            </select>
                        </div>
                        
                        <button type="submit" name="add_product_type" class="btn btn-info">Thêm loại</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let nameInput = document.querySelector('input[name="product_type_name"]');
    let slugInput = document.getElementById('category_slug');

    nameInput.addEventListener('keyup', function () {
        slugInput.value = convertToSlug(nameInput.value);
    });

    function convertToSlug(str) {
        str = str.toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "") //loại bỏ dấu
        .replace(/đ/g, "d")
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
        return str;
    }
});
</script>

@endsection