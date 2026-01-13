@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Cập nhật loại sản phẩm
            </header>

            <div class="panel-body">
                @foreach ($edit_product_type as $key => $edit_value)

                <div class="position-center">
                    <form role="form"
                          action="{{ URL::to('/update-product-type/'.$edit_value->id) }}"
                          method="post"
                          enctype="multipart/form-data">

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Tên Loại sản phẩm</label>
                            <input type="text"
                                   name="product_type_name"
                                   value="{{ $edit_value->name }}"
                                   class="form-control"
                                   placeholder="Tên loại"
                                   maxlength="50">
                        </div>

                        <div class="form-group">
                            <label>Đường dẫn (slug)</label>
                            <input type="text"
                                name="category_slug"
                                id="category_slug"
                                value="{{ $edit_value->category_slug }}"
                                class="form-control"
                                readonly>
                        </div>


                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea rows="5"
                                      name="product_type_desc"
                                      class="form-control"
                                      style="resize: none;">{{ $edit_value->description }}</textarea>
                        </div>

                        <button type="submit" name="update_product_type" class="btn btn-info">
                            Cập nhật loại sản phẩm
                        </button>

                    </form>
                </div>

                @endforeach
            </div>
        </section>
    </div>
</div>

{{-- JS tạo slug--}}
<script>
    function generateSlug(str) {
        str = str.toLowerCase();

        str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        str = str.replace(/đ/g, "d");

        str = str.replace(/[^a-z0-9]+/g, '-');
        str = str.replace(/^-+|-+$/g, '');

        return str;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const nameInput = document.querySelector('input[name="category_name"]');
        const slugInput = document.getElementById("category_slug");

        nameInput.addEventListener("keyup", function() {
            slugInput.value = generateSlug(this.value);
        });
    });
</script>

@endsection
