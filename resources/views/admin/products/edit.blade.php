@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Cập nhật sản phẩm
            </header>

            <div class="panel-body">
                <div class="position-center">

                    <form role="form"
                          action="{{ URL::to('/admin/products/'.$product->id.'/update') }}"
                          method="post"
                          enctype="multipart/form-data">
                        @csrf

                    
                        <div class="form-group">
                            <label>Tên sản phẩm</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ $product->name }}">
                        </div>

                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea name="description"
                                      rows="5"
                                      class="form-control"
                                      style="resize:none;">{{ $product->description }}</textarea>
                        </div>

                 
                        <div class="form-group">
                            <label>Thương hiệu</label>
                            <select name="brand_id" class="form-control">
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    
                        <div class="form-group">
                            <label>Loại sản phẩm</label>
                            <select name="category_id" class="form-control">
                                @foreach($categories as $cate)
                                    <option value="{{ $cate->id }}"
                                        {{ $product->category_id == $cate->id ? 'selected' : '' }}>
                                        {{ $cate->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                       
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="gender" class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male"   {{ $product->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ $product->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="unisex" {{ $product->gender == 'unisex' ? 'selected' : '' }}>Trung tính</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kích thước mặt (mm)</label>
                            <input type="number"
                                   step="0.01"
                                   name="dial_size"
                                   class="form-control"
                                   value="{{ $product->dial_size }}">
                        </div>

                      
                        <div class="form-group">
                            <label>Chất liệu dây</label>
                            <input type="text"
                                   name="strap_material"
                                   class="form-control"
                                   value="{{ $product->strap_material }}">
                        </div>

                      
                        <div class="form-group">
                            <label>Giá bán</label>
                            <input type="number"
                                   name="price"
                                   class="form-control"
                                   value="{{ $product->price }}">
                        </div>

                        <hr>

                      
                        @php
                            $images = $product->productImage;
                        @endphp

                        <div class="row">
                         
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ảnh 1 (đại diện)</label><br>
                                    @if($images && $images->image_1)
                                        <img src="{{ asset('storage/' . $images->image_1) }}"
                                             alt="Ảnh 1"
                                             style="width:100%; max-width:120px; height:120px; object-fit:cover; border-radius:6px; margin-bottom:5px;">
                                    @else
                                        <p>Chưa có ảnh</p>
                                    @endif
                                    <input type="file" name="image_1" class="form-control">
                                </div>
                            </div>

                         
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ảnh 2</label><br>
                                    @if($images && $images->image_2)
                                        <img src="{{ asset('storage/' . $images->image_2) }}"
                                             alt="Ảnh 2"
                                             style="width:100%; max-width:120px; height:120px; object-fit:cover; border-radius:6px; margin-bottom:5px;">
                                    @else
                                        <p>Chưa có ảnh</p>
                                    @endif
                                    <input type="file" name="image_2" class="form-control">
                                </div>
                            </div>

                         
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ảnh 3</label><br>
                                    @if($images && $images->image_3)
                                        <img src="{{ asset('storage/' . $images->image_3) }}"
                                             alt="Ảnh 3"
                                             style="width:100%; max-width:120px; height:120px; object-fit:cover; border-radius:6px; margin-bottom:5px;">
                                    @else
                                        <p>Chưa có ảnh</p>
                                    @endif
                                    <input type="file" name="image_3" class="form-control">
                                </div>
                            </div>

                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ảnh 4</label><br>
                                    @if($images && $images->image_4)
                                        <img src="{{ asset('storage/' . $images->image_4) }}"
                                             alt="Ảnh 4"
                                             style="width:100%; max-width:120px; height:120px; object-fit:cover; border-radius:6px; margin-bottom:5px;">
                                    @else
                                        <p>Chưa có ảnh</p>
                                    @endif
                                    <input type="file" name="image_4" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info">Cập nhật sản phẩm</button>

                    </form>

                </div>
            </div>
        </section>
    </div>
</div>

@endsection
