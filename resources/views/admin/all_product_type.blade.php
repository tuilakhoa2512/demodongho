@extends('pages.admin_layout')

@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
    <!-- <div class="panel-heading">
     
       @if($filterStatus == 1)
          <small>(Trạng Thái: Hiện)</small>
        @elseif($filterStatus === "0")
          <small>(Trạng Thái: Ẩn)</small>
        @endif
    </div> -->
   
        <div class="panel-heading">
            Liệt Kê Loại Sản Phẩm
        </div>
        @if (session('message'))
                            <script>
                                $(document).ready(function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: 'Xoá loại sản phẩm thành công!',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            </script>
                        @endif
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <select class="input-sm form-control w-sm inline v-middle">
                    
                    <!-- <option value="">Lọc trạng thái (Tất cả)</option>
            <option value="1" {{ isset($filterStatus) && $filterStatus == 1 ? 'selected' : '' }}>Hiện</option>
            <option value="0" {{ isset($filterStatus) && $filterStatus == 0 ? 'selected' : '' }}>Ẩn</option> -->4
                    <option value="0">Bulk action</option>
                    <option value="1">Delete selected</option>
                    <option value="2">Bulk edit</option>
                    <option value="3">Export</option>
                </select>
                <button type="submit" class="btn btn-sm btn-default" style="margin-left:5px;">Áp dụng</button>              
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
            @if (session('message'))
                    <script>
                        $(document).ready(function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: 'Kích hoạt loại sản phẩm thành công!',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                @endif
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
                            <?php
                            if($cate_pro->status==1){
                                ?>
                               <a href="{{ URL::to('/unactive-product-type/'.$cate_pro->id) }}"><span class="fa-thumb-styling fa fa-thumbs-up"></span></a>
                            <?php    
                            }else{
                                ?>
                                <a href="{{ URL::to('/active-product-type/'.$cate_pro->id) }}"><span class="fa-thumb-styling fa fa-thumbs-down"></span></a>
                                <?php   
                            }
                            ?>
                        </span></td>
                        <td>
                            <a href="{{ URL::to('/edit-product-type/'.$cate_pro->id) }}" class="active styling edit">
                                <i class="fa fa-pencil-square-o text-success text-active"></i>
                            </a>
                            <a href="#" class="active styling edit" onclick="confirmDelete('{{ URL::to('/delete-product-type/'.$cate_pro->id) }}')">
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
                    <small class="text-muted inline m-t-sm m-b-sm">showing 20-30 of 50 items</small>
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
                    text: 'Loại Sản phẩm sẽ bị xoá và không thể khôi phục!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Vâng, xoá',
                    cancelButtonText: 'Huỷ'
        }).then((result) => {
            if (result.isConfirmed) {
                // Chuyển hướng đến URL xóa
                window.location.href = url;
            }
        });
    }
</script>

@endsection