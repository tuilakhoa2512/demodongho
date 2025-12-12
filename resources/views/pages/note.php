<?php
                            if($cate_pro->status==1){
                                ?>
                                <a href="{{ URL::to('/unactive-product-type/'.$cate_pro->id) }}">
                                    <span class="label label-success">Hiện</span>
                                </a>
                            <?php    
                            }else{
                                ?>
                                <a href="{{ URL::to('/active-product-type/'.$cate_pro->id) }}">
                                    <span class="label label-danger">Ẩn</span>
                                </a>
                                <?php   
                            }
                            ?>