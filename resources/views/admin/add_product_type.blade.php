@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
            <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Thêm loại sản phẩm
                        </header>
                        <div class="panel-body">
                            <div class="position-center">
                                <form role="form">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên loại sản phẩm</label>
                                    <input type="email" name="product_type_name" class="form-control" id="exampleInputEmail1" placeholder="Tên Loại sản phẩm">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Mô tả Loại sản phẩm</label>
                                    <textarea style="resize: none" rows="5" name="product_type_desc" class="form-control" id="exampleInputPassword1" placeholder="Mô tả Loại sản phẩm"></textarea>
                                </div>
                                <div class="form-group">
                                <label for="exampleInputEmail1">Hiển Thị</label>
                                <select class="form-control input-lg m-bot15">
                                    <option>Ẩn</option>
                                    <option>Hiển Thị</option>
                                    
                                </select>                      
                                </div>
                                
                                <button type="submit" name="add_product_type" class="btn btn-info">Thêm</button>
                            </form>
                            </div>

                        </div>
                    </section>

            </div>
            
@endsection