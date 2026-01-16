@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Cập nhật thương hiệu sản phẩm
            </header>

            <div class="panel-body">
                @foreach ($edit_brand_product as $key => $edit_value)

                <div class="position-center">
                    <form role="form"
                          action="{{ URL::to('/update-brand-product/'.$edit_value->id) }}"
                          method="post"
                          enctype="multipart/form-data">

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Tên thương hiệu</label>
                            <input type="text"
                                   name="brand_product_name"
                                   value="{{ $edit_value->name }}"
                                   class="form-control"
                                   placeholder="Tên thương hiệu"
                                   maxlength="50">
                        </div>

                        <div class="form-group">
                            <label>Đường dẫn (slug)</label>
                            <input type="text"
                                name="brand_product_slug"
                                id="brand_slug"
                                value="{{ $edit_value->brand_slug }}"
                                class="form-control"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea rows="5"
                                      name="brand_product_desc"
                                      class="form-control"
                                      style="resize: none;">{{ $edit_value->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Hình hiện tại</label>
                            <br>

                            @if($edit_value->image)
                              
                                <img src="{{ asset('storage/' . $edit_value->image) }}"
                                     alt="{{ $edit_value->name }}"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                            @else
                                <p>Chưa có hình</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Chọn hình mới (Nếu muốn thay)</label>
                            <input type="file" name="brand_product_image" class="form-control-file">
                        </div>

                        <button type="submit" class="btn btn-info">
                            Cập nhật thương hiệu
                        </button>

                    </form>
                </div>

                @endforeach
            </div>
        </section>
    </div>
</div>

<script>
    function generateSlug(str) {
        return str
            .toLowerCase()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")   // bỏ dấu
            .replace(/đ/g, "d")                                // chuyển đ thành d
            .replace(/[^a-z0-9]+/g, '-')                       // thay ký tự đặc biệt bằng -
            .replace(/^-+|-+$/g, '');                          // bỏ - ở đầu và cuối
    }

    document.addEventListener("DOMContentLoaded", function () {
        const nameInput = document.querySelector('input[name="brand_product_name"]');
        const slugInput = document.getElementById("brand_slug");

        nameInput.addEventListener("keyup", function () {
            slugInput.value = generateSlug(this.value);
        });
    });
</script>

@endsection
