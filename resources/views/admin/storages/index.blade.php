@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Danh sách lô hàng
    </div>

    
    @if (session('success'))
      <div class="alert alert-success" style="margin: 15px;">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger" style="margin: 15px;">
        {{ session('error') }}
      </div>
    @endif

    <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">
        <select class="input-sm form-control w-sm inline v-middle">
          <option value="0">Thao tác</option>
          <option value="1">Xoá đã chọn</option>
          <option value="2">Xuất Excel</option>
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
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Nhà cung cấp</th>
            <th>Ngày nhập</th>
            <th>SL nhập</th>
            <th>Giá nhập (1 SP)</th>
            <th>Tổng tiền nhập</th>
            <th style="width:80px;">Thao tác</th>
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

              <td>{{ $storage->product_name }}</td>

              <td>{{ $storage->supplier_name ?? '—' }}</td>

              <td>
                {{ \Carbon\Carbon::parse($storage->import_date)->format('d/m/Y') }}
              </td>

              <td>{{ number_format($storage->import_quantity) }}</td>

              <td>{{ number_format($storage->unit_import_price, 0, ',', '.') }} đ</td>

              <td>{{ number_format($storage->total_import_price, 0, ',', '.') }} đ</td>

              <td>
               
                <a href="{{ URL::to('/admin/storages/' . $storage->id . '/edit') }}"
                   class="active styling-edit" title="Sửa lô hàng">
                  <i class="fa fa-pencil-square-o text-success text-active"></i>
                </a>

               
                <a href="javascript:void(0);"
                   class="active styling-delete btn-delete-storage"
                   data-id="{{ $storage->id }}"
                   title="Xóa lô hàng">
                  <i class="fa fa-times text-danger text"></i>
                </a>

              
                <form id="delete-storage-form-{{ $storage->id }}"
                      action="{{ URL::to('/admin/storages/'.$storage->id) }}"
                      method="POST"
                      style="display:none;">
                  @csrf
                  @method('DELETE')
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
            Hiển thị {{ $storages->firstItem() }} - {{ $storages->lastItem() }}
            / {{ $storages->total() }} lô hàng
          </small>
        </div>

        <div class="col-sm-7 text-right text-center-xs">
         
          {{ $storages->links('pagination::bootstrap-4') }}
        </div>

      </div>
    </footer>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
      const deleteButtons = document.querySelectorAll('.btn-delete-storage');

      deleteButtons.forEach(function (btn) {
          btn.addEventListener('click', function (e) {
              e.preventDefault();

              const id = this.getAttribute('data-id');
              const form = document.getElementById('delete-storage-form-' + id);

              if (!form) return;

              Swal.fire({
                  title: 'Xóa lô hàng?',
                  text: 'Bạn có chắc chắn muốn xóa lô hàng này không?',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Xóa',
                  cancelButtonText: 'Hủy'
              }).then((result) => {
                  if (result.isConfirmed) {
                      form.submit();
                  }
              });
          });
      });
  });
</script>

@endsection
