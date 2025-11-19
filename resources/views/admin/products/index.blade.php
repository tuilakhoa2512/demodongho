@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="font-size: 18px; font-weight: 600;">
      Danh Sách Sản Phẩm
    </div>

      @if (session('success'))
        <script>
            Swal.fire({
                title: "Thành công!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK",
                timer: 2000
            });
        </script>
        @endif

    <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">
        <select class="input-sm form-control w-sm inline v-middle">
          <option value="0">Bulk action</option>
          <option value="1">Delete selected</option>
          <option value="2">Bulk edit</option>
          <option value="3">Export</option>
        </select>
        <button class="btn btn-sm btn-default">Apply</button>
      </div>

      <div class="col-sm-3"></div>

      <div class="col-sm-4">
        <div class="input-group">
          <input type="text" class="input-sm form-control" placeholder="Search">
          <span class="input-group-btn">
            <button class="btn btn-sm btn-default" type="button">Go!</button>
          </span>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped b-t b-light">

        <thead>
          <tr>
            <th style="width:20px;">
              <label class="i-checks m-b-none">
                <input type="checkbox"><i></i>
              </label>
            </th>
            <th>ID</th>
            <th>Hình</th>
            <th>Tên sản phẩm</th>
            <th>Lô hàng</th>
            <th>Loại</th>
            <th>Thương hiệu</th>
            <th>Giới tính</th>
            <th>Kích thước</th>
            <th>Chất liệu dây</th>
            <th>Giá bán</th>
            <th style="width:80px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>

          @foreach($products as $product)
          <tr>

            <td><label class="i-checks m-b-none"><input type="checkbox" name="post[]"><i></i></label></td>

            <td>{{ $product->id }}</td>

            <td>
              @if ($product->productImage && $product->productImage->image_1)
                  <img src="{{ asset('storage/' . $product->productImage->image_1) }}"
                      alt="{{ $product->name }}"
                      style="width: 60px; height: 60px; object-fit: cover;">
              @else
                  <span>Không có ảnh</span>
              @endif
          </td>

            <td>{{ $product->name }}</td>

            <td>Lô #{{ $product->storage->id }}</td>

            <td>{{ $product->category->name }}</td>

            <td>{{ $product->brand->name }}</td>

            <td>{{ $product->gender }}</td>

            <td>
                {{ $product->dial_size !== null ? $product->dial_size . ' mm' : '-' }}
            </td>



            <td>{{ $product->strap_material }}</td>

            <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>

           <td>
                <a href="#" class="active">
                    <i class="fa fa-pencil-square-o text-success text-active"></i>
                </a>

                <form action="{{ route('admin.products.destroy', $product->id) }}"
                    method="POST"
                    style="display:inline-block"
                    class="form-delete-product">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn-delete-product" style="border:none; background:none; padding:0; margin-left:5px;">
                        <i class="fa fa-times text-danger"></i>
                    </button>
                </form>


            </td>

          </tr>
          @endforeach

        </tbody>

      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">

        <div class="col-sm-5 text-center">
          <small class="text-muted inline m-t-sm m-b-sm">
            Showing {{ count($products) }} items
          </small>
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $products->links('pagination::bootstrap-4') }}
        </div>

      </div>
    </footer>

  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.btn-delete-product');

        deleteButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const form = this.closest('form');

                Swal.fire({
                    title: 'Bạn chắc chắn?',
                    text: 'Sản phẩm sẽ bị xoá và không thể khôi phục!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Vâng, xoá',
                    cancelButtonText: 'Huỷ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();   // gửi request DELETE /admin/products/{id}
                    }
                });
            });
        });
    });
</script>


@endsection
