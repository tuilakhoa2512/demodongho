@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Liệt Kê Thương Hiệu Sản Phẩm
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
      <form method="GET" action="{{ route('admin.allbrandproduct') }}" class="form-inline">
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
            <th>Tên thương hiệu</th>
            <th>Mô tả</th>
            <th>Slug</th>
            <th>Hình</th>
            <th>Hiển Thị</th>
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
            <td>{{ $brand_pro->brand_slug }}</td>
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
                <span class="text-ellipsis">
                @if ($brand_pro->status == 1)
                        <span class="label label-success">Hiện</span>
                @else
                        <span class="label label-danger">Ẩn</span>
                @endif
                </span>
              </td>
              <td>
               <a href="{{ URL::to('/edit-brand-product/'.$brand_pro->id) }}" class="active styling edit">
               <i class="fa fa-pencil-square-o text-success text-active"></i>
                </a>
                <!-- Ẩn / Hiện -->
                @if ($brand_pro->status == 1)
                    <!-- Đang hiện → cho phép chuyển sang Ẩn -->
                    <a href="{{ URL::to('/unactive-brand-product/'.$brand_pro->id) }}"
                      class="active styling edit"
                      style="font-size: 18px;">
                        <i class="fa fa-eye text-warning"></i>
                    </a>
                @else
                    <!-- Đang ẩn → cho phép chuyển sang Hiện -->
                    <a href="{{ URL::to('/active-brand-product/'.$brand_pro->id) }}"
                      class="active styling edit"
                      style="font-size: 18px;">
                        <i class="fa fa-eye-slash text-warning"></i>
                    </a>
                @endif
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
            Hiển thị {{ $all_brand_product->firstItem() }} - {{ $all_brand_product->lastItem() }}
            / {{ $all_brand_product->total() }} Thương Hiệu
          </small>
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $all_brand_product->links('vendor.pagination.number-only') }}
        </div>
      </div>
    </footer>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection