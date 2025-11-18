@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Liệt kê sản phẩm
    </div>

    <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">
        <select class="input-sm form-control w-sm inline v-middle">
          <option value="0">Thao tác</option>
          <option value="1">Xoá đã chọn</option>
          <option value="2">Ẩn đã chọn</option>
          <option value="3">Hiển thị đã chọn</option>
        </select>
        <button class="btn btn-sm btn-default">Áp dụng</button>
      </div>

      <div class="col-sm-4">
      </div>

      <div class="col-sm-3">
        <div class="input-group">
          <input type="text" class="input-sm form-control" placeholder="Tìm theo tên sản phẩm...">
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
            <th>Tên sản phẩm</th>
            <th>Lô hàng</th>
            <th>Giới tính</th>
            <th>Kích thước mặt</th>
            <th>Chất liệu dây</th>
            <th>Giá bán</th>
            <th>Trạng thái</th>
            <th style="width:80px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $product)
            <tr>
              <td>
                <label class="i-checks m-b-none">
                  <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"><i></i>
                </label>
              </td>

              <td>{{ $product->name }}</td>

              <td>
                @if($product->storage)
                  Lô #{{ $product->storage->id }} - {{ $product->storage->product_name }}
                @else
                  —
                @endif
              </td>

              <td>{{ $product->gender ?? '—' }}</td>
              <td>{{ $product->dial_size ?? '—' }}</td>
              <td>{{ $product->strap_material ?? '—' }}</td>

              <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>

              <td>
                @if($product->status == 1)
                  <span class="text-success">Hiển thị</span>
                @else
                  <span class="text-danger">Ẩn</span>
                @endif
              </td>

              <td>
                <a href="#" class="active" ui-toggle-class="">
                  <i class="fa fa-pencil-square-o text-success text-active"></i>
                  <i class="fa fa-times text-danger text"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">

        <div class="col-sm-5 text-center">
          @if ($products->total() > 0)
            <small class="text-muted inline m-t-sm m-b-sm">
              Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }}
              / {{ $products->total() }} sản phẩm
            </small>
          @else
            <small class="text-muted inline m-t-sm m-b-sm">
              Chưa có sản phẩm nào.
            </small>
          @endif
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $products->links('pagination::bootstrap-4') }}
        </div>

      </div>
    </footer>
  </div>
</div>

@endsection
