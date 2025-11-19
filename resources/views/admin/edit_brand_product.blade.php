@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Cập nhật thương hiệu sản phẩm
            </header>

          
            @if (Session::has('message'))
                <span class="text-alert">{{ Session::get('message') }}</span>
                {{ Session::put('message', null) }}
            @endif

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
                                   placeholder="Tên thương hiệu">
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

@endsection
