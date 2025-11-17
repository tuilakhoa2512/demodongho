@extends('pages.admin_layout')
@section('admin_content')
<div class="row">
            <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Cập nhật thương hiệu sản phẩm
                        </header>
                        <?php
                            $message = Session::get('message');
                            if($message){
                                echo '<span class="text-alert">',$message.'</span>';
                                Session::put('message',null);
                            }
                            ?>
                        <div class="panel-body">
                            @foreach ($edit_brand_product as $key => $edit_value)

                            <div class="position-center">
                                <form role="form" action="{{ URL::to('/update-brand-product/'.$edit_value->id) }}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên loại sản phẩm</label>
                                    <input type="text" value="{{ $edit_value->name }}" name="brand_product_name" class="form-control" id="exampleInputEmail1" placeholder="Tên Loại sản phẩm">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Mô tả Loại sản phẩm</label>
                                    <textarea style="resize: none" rows="5" name="brand_product_desc" class="form-control" id="exampleInputPassword1" >{{ $edit_value->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputFile">Hình</label>
                                    <input type="file" name="brand_product_image" class="form-control-file" id="exampleInputFile">{{ $edit_value->image }}
                                </div>                           
                                <button type="submit" name="update_brand_product" class="btn btn-info">Cập nhật thương hiệu</button>
                            </form>
                            </div>
                                                        
                            @endforeach
                        </div>
                    </section>

            </div>
            
@endsection