@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Danh sách lô hàng
       @if($filterStatus == 1)
          <small>(Trạng Thái: Hiện)</small>
        @elseif($filterStatus === "0")
          <small>(Trạng Thái: Ẩn)</small>
        @endif
    </div>
   
    @if (session('success'))
        <script>
            Swal.fire({
                title: "Thành công!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK",
              
            });
        </script>
        @endif

    <!-- @if (session('success'))
      <div class="alert alert-success" style="margin: 15px;">
        {{ session('success') }}
      </div>
    @endif -->

    @if (session('error'))
      <div class="alert alert-danger" style="margin: 15px;">
        {{ session('error') }}
      </div>
    @endif

    <style>
        table td, table th {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>


    <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">

        <form method="GET" action="{{ route('admin.storages.index') }}" class="form-inline">

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

      <div class="col-sm-4">
      </div>

      <div class="col-sm-3">
        <div class="input-group">
          <input type="text" class="input-sm form-control" placeholder="Tìm theo mã lô / nhà cung cấp...">
          <span class="input-group-btn">
            <button class="btn btn-sm btn-default" type="button">Tìm</button>
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
            <th>Mã lô</th>
            <th>Nhà cung cấp</th>
            <th>Email NCC</th>
            <th>Ngày nhập</th>
            <th>Trạng thái</th>
            <th style="width:140px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($storages as $storage)
            <tr>
              <td>
                <label class="i-checks m-b-none">
                  <input type="checkbox" name="storage_ids[]" value="{{ $storage->id }}"><i></i>
                </label>
              </td>

              <td>{{ $storage->id }}</td>

              <td>{{ $storage->batch_code }}</td>

              <td>{{ $storage->supplier_name ?? '—' }}</td>

              <td>{{ $storage->supplier_email ?? '—' }}</td>

              <td>
                {{ $storage->import_date
                    ? \Carbon\Carbon::parse($storage->import_date)->format('d/m/Y')
                    : '—' }}
              </td>

              <td>
                @if($storage->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>

              <td>
                {{-- Sửa lô hàng --}}
                <a href="{{ route('admin.storages.edit', $storage->id) }}"
                   class="active styling-edit" title="Sửa lô hàng">
                  <i class="fa fa-pencil-square-o text-success text-active" style="font-size:18px;"></i>
                </a>

                {{-- Ẩn / hiện lô hàng (không xóa cứng nữa) --}}
                <form action="{{ route('admin.storages.toggle-status', $storage->id) }}"
                      method="POST"
                      style="display:inline-block; margin:0 4px;">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                          style="border:none; background:none; padding:0;"
                          title="{{ $storage->status ? 'Ẩn lô hàng' : 'Hiện lô hàng' }}">
                    @if($storage->status)
                      <i class="fa fa-eye-slash text-warning" style="font-size:18px;"></i>
                    @else
                      <i class="fa fa-eye text-info" style="font-size:18px;"></i>
                    @endif
                  </button>
                </form>

                {{-- Xem chi tiết kho theo lô --}}
                <a href="{{ route('admin.storage-details.by-storage', $storage->id) }}"
                   title="Xem sản phẩm trong lô này">
                  <i class="fa fa-archive text-primary" style="font-size:18px;"></i>
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
          <small class="text-muted inline m-t-sm m-b-sm">
            Hiển thị {{ $storages->firstItem() }} - {{ $storages->lastItem() }}
            / {{ $storages->total() }} lô hàng
          </small>
        </div>

        <div class="text-center">
          {{ $storages->links('vendor.pagination.number-only') }}
        </div>
      </div>
    </footer>
  </div>
</div>

@endsection
