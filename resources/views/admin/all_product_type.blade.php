@extends('pages.admin_layout')

@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            Liệt Kê Loại Sản Phẩm
            @if($filterStatus == 1)
                <small>(Trạng Thái: Hiện)</small>
            @elseif($filterStatus === "0")
                <small>(Trạng Thái: Ẩn)</small>
            @endif  
        </div>
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
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
            <form method="GET" action="{{ route('admin.allproducttype') }}" class="form-inline">
          <select name="status" class="input-sm form-control w-sm inline v-middle">
            <option value="">Lọc trạng thái (Tất cả)</option>
            <option value="1" {{ isset($filterStatus) && $filterStatus == 1 ? 'selected' : '' }}>Hiện</option>
            <option value="0" {{ isset($filterStatus) && $filterStatus == 0 ? 'selected' : '' }}>Ẩn</option>
          </select>

          <button type="submit" class="btn btn-sm btn-default" style="margin-left:5px;">
            Áp dụng
          </button>

        </form>             
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
                        <th>Tên loại</th>
                        <th>Mô tả</th>
                        <th>Slug</th>
                        <th>Hiển Thị</th>
                        <th>Thao tác</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($all_product_type as $key => $cate_pro)
                    <tr>
                        <td><label class="i-checks m-b-none"><input type="checkbox" name="post[]"><i></i></label></td>
                        <td>{{ $cate_pro->id }}</td>
                        <td>{{ $cate_pro->name }}</td>
                        <td>{{ $cate_pro->description }}</td>
                        <td>{{ $cate_pro->category_slug }}</td>
                        <!-- <td>{{ $cate_pro->status }}</td> -->
                        <td><span class="text-ellipsis">
                        @if ($cate_pro->status == 1)
                            <span class="label label-success">Hiện</span>
                        @else
                            <span class="label label-danger">Ẩn</span>
                        @endif
                        </span></td>
                        <td>
                            <a href="{{ URL::to('/edit-product-type/'.$cate_pro->id) }}" class="active styling edit">
                                <i class="fa fa-pencil-square-o text-success text-active"></i>
                            </a>
                            <!-- Thao tác Ẩn Hiện -->
                            @if ($cate_pro->status == 1)
                                <!-- Đang hiện → cho phép ẩn -->
                                <a href="{{ URL::to('/unactive-product-type/'.$cate_pro->id) }}" class="active styling edit" style="font-size: 18px;">
                                    <i class="fa fa-eye text-warning"></i>
                                </a>
                            @else
                                <!-- Đang ẩn → cho phép hiện -->
                                <a href="{{ URL::to('/active-product-type/'.$cate_pro->id) }}" class="active styling edit" style="font-size: 18px;">
                                    <i class="fa fa-eye-slash text-warning"></i>
                                </a>
                            @endif

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
            Hiển thị {{ $all_product_type->firstItem() }} - {{ $all_product_type->lastItem() }}
            / {{ $all_product_type->total() }} Danh Mục
          </small>
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $all_product_type->links('vendor.pagination.number-only') }}
        </div>
      </div>
</footer>
    </div>
</div>

@endsection