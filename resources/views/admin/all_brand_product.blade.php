@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Liệt Kê Thương Hiệu Sản Phẩm
    </div>

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
      <div class="col-sm-4"></div>
      <div class="col-sm-3">
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
            <th>Tên thương hiệu</th>
            <th>Mô tả</th>
            <th>Hình</th>
            <th>Thao tác</th>
            <th style="width:30px;"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($all_brand_product as $key => $brand_pro)
          <tr>
            <td>
              <label class="i-checks m-b-none">
                <input type="checkbox" name="post[]"><i></i>
              </label>
            </td>

            <td>{{ $brand_pro->id }}</td>
            <td>{{ $brand_pro->name }}</td>
            <td>{{ $brand_pro->description }}</td>

            <td>
              @if ($brand_pro->image)
                <img src="{{ asset('storage/' . $brand_pro->image) }}"
                     alt="{{ $brand_pro->name }}"
                     style="width: 50px; height: 50px; object-fit: cover;">
              @else
                <span>Chưa có ảnh</span>
              @endif
            </td>

            <td>
                            <a href="{{ URL::to('/edit-brand-product/'.$brand_pro->id) }}" class="active styling edit">
                                <i class="fa fa-pencil-square-o text-success text-active"></i>
                            </a>
                            <a href="#" class="active styling edit" onclick="confirmDelete('{{ URL::to('/delete-brand-product/'.$brand_pro->id) }}')">
                                <i class="fa fa-times text-danger text"></i>
                            </a>
                        </td>
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
            showing 20-30 of 50 items
          </small>
        </div>
        <div class="col-sm-7 text-right text-center-xs">
          <ul class="pagination pagination-sm m-t-none m-b-none">
            <li><a href=""><i class="fa fa-chevron-left"></i></a></li>
            <li><a href="">1</a></li>
            <li><a href="">2</a></li>
            <li><a href="">3</a></li>
            <li><a href="">4</a></li>
            <li><a href=""><i class="fa fa-chevron-right"></i></a></li>
          </ul>
        </div>
      </div>
    </footer>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Bạn chắc chắn?',
            text: "Thương hiệu phẩm sẽ bị xóa và không thể khôi phục!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Vâng, xóa!',
            cancelButtonText: 'Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Chuyển hướng đến URL xóa
                window.location.href = url;
            }
        });
    }
</script>

@endsection