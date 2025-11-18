@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
            <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Thêm loại sản phẩm
                        </header>
                        <?php
                            $message = Session::get('message');
                            if($message){
                                echo '<span class="text-alert">'.$message.'</span>';
                                Session::put('message',null);
                            }
                            ?>
                        <div class="panel-body">

                            <div class="position-center">
                                <form role="form" action="{{ URL::to('/save-product-type') }}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên loại sản phẩm</label>
                                    <input type="text" name="product_type_name" class="form-control" id="exampleInputEmail1" placeholder="Tên Loại sản phẩm">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Mô tả Loại sản phẩm</label>
                                    <textarea style="resize: none" rows="5" name="product_type_desc" class="form-control" id="exampleInputPassword1" placeholder="Mô tả Loại sản phẩm"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputFile">Hình</label>
                                    <input type="file" name="product_type_image" class="form-control-file" id="exampleInputFile">
                                </div>
                                <!-- <div class="form-group">
                                <label for="exampleInputEmail1">Hiển Thị</label>
                                <select name="status" class="form-control input-lg m-bot15">
                                    <option value="0">Ẩn</option>
                                    <option value="1">Hiển Thị</option>
                                    
                                </select>                      
                                </div> -->
                                
                                <button type="submit" name="add_product_type" class="btn btn-info">Thêm loại</button>
                            </form>
                            </div>

                        </div>
                    </section>

            </div>
            
@endsection