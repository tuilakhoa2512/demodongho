@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="color:#000; font-weight:600;">
      Sản phẩm trong lô: {{ $storage->batch_code }}
    </div>

    @if (session('success'))
      <div class="alert alert-success" style="margin: 15px;">
        {{ session('success') }}
      </div>
    @endif

    <div style="margin: 15px;">
      <a href="{{ route('admin.storage-details.create', $storage->id) }}" class="btn btn-primary btn-sm">
        + Thêm sản phẩm vào lô này
      </a>
      <a href="{{ route('admin.storages.index') }}" class="btn btn-default btn-sm">
        ← Quay lại danh sách lô
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng nhập</th>
            <th>Trạng thái kho</th>
            <th>Ghi chú</th>
            <th>Hiển thị</th>
            <th style="width:130px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($details as $detail)
            <tr>
              <td>{{ $detail->id }}</td>
              <td>{{ $detail->product_name }}</td>
              <td>{{ number_format($detail->import_quantity) }}</td>
              <td>
                @if($detail->stock_status === 'pending')
                  <span class="label label-warning">Chờ bán</span>
                @elseif($detail->stock_status === 'selling')
                  <span class="label label-success">Đang bán</span>
                @elseif($detail->stock_status === 'sold_out')
                  <span class="label label-default">Hết hàng</span>
                @elseif($detail->stock_status === 'stopped')
                  <span class="label label-danger">Ngừng bán</span>
                @else
                  <span class="label label-default">—</span>
                @endif
              </td>
              <td>{{ $detail->note }}</td>
              <td>
                @if($detail->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.storage-details.edit', $detail->id) }}"
                   class="btn btn-xs btn-warning">
                  Sửa
                </a>

                <form action="{{ route('admin.storage-details.toggle-status', $detail->id) }}"
                      method="POST"
                      style="display:inline-block;">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn btn-xs btn-secondary">
                    {{ $detail->status ? 'Ẩn' : 'Hiện' }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Chưa có sản phẩm nào trong lô này.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-12 text-right text-center-xs">
          {{ $details->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </footer>

  </div>
</div>

@endsection
