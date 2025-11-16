@extends('pages.admin_layout')

@section('admin_content')
    <div class="row">
        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Danh sách lô hàng</h2>

                <a href="{{ route('admin.storages.create') }}" class="btn btn-primary">
                    + Thêm lô hàng mới
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($storages->isEmpty())
                <p>Chưa có lô hàng nào.</p>
            @else
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm (kho)</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>SL nhập</th>
                        <th>SL đang bán</th>
                        <th>SL đã bán</th>
                        <th>Giá nhập / 1sp</th>
                        <th>Tổng giá nhập</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($storages as $storage)
                        @php
                            $product = $storage->product;
                            $sellingQuantity = $product ? $product->quantity : 0;
                            $soldQuantity = $product
                                ? max(0, $storage->import_quantity - $product->quantity)
                                : 0;
                        @endphp

                        <tr>
                            <td>{{ $storage->id }}</td>
                            <td>{{ $storage->product_name }}</td>
                            <td>{{ $storage->supplier_name }}</td>
                            <td>{{ $storage->import_date }}</td>
                            <td>{{ $storage->import_quantity }}</td>
                            <td>{{ $sellingQuantity }}</td>
                            <td>{{ $soldQuantity }}</td>
                            <td>{{ number_format($storage->unit_import_price) }} đ</td>
                            <td>{{ number_format($storage->total_import_price) }} đ</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{ $storages->links() }}
            @endif
        </div>
    </div>
@endsection
