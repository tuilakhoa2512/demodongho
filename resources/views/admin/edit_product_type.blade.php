@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Cập nhật loại sản phẩm
            </header>
            @if (session('message'))
                            <script>
                                $(document).ready(function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: 'Cập nhật loại sản phẩm thành công!',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            </script>
                        @endif
          
            @if (Session::has('message'))
                <span class="text-alert">{{ Session::get('message') }}</span>
                {{ Session::put('message', null) }}
            @endif

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
                                   placeholder="Tên loại">
                        </div>

                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea rows="5"
                                      name="product_type_desc"
                                      class="form-control"
                                      style="resize: none;">{{ $edit_value->description }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-info">
                            Cập nhật loại sản phẩm
                        </button>

                    </form>
                </div>

                @endforeach
            </div>
        </section>
    </div>
</div>

@endsection
