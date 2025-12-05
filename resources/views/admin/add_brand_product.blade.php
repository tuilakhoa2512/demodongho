@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Thêm thương hiệu sản phẩm
            </header>

            <div class="panel-body">
            @if (session('success'))
                    <script>
                        $(document).ready(function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Thêm brand sản phẩm thành công!',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif
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
                            <label for="productTypeDesc">Hiển Thị</label>
                            <select name="brand_product_status" class="form-control input-sm m-bot15">
                                <option value="0">Hiển Thị</option>
                                <option value="1">Ẩn</option>
                                
                               
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

@endsection
