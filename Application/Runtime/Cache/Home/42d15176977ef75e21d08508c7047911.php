<?php if (!defined('THINK_PATH')) exit();?>
<?php if(!empty($data['goods'])): if(is_array($data['goods'])): $i = 0; $__LIST__ = $data['goods'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><div class="row">
            <div class="col-lg-12">
                <div class="product well check-cart">
                    <div class="col-sm-2">
                        <div class="image">
                            <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>">
                                <img src="<?php echo ($goods['thumb']); ?>" />
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-10">
                        <div class="caption">
                            <div class="name" <?php if(($goods['disabled']) == "1"): ?>style="text-decoration: line-through;"<?php endif; ?>>
                                <h3>
                                    <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>">
                                        <?php echo ($goods['name']); ?> <?php echo ($goods['attr_value']); ?>
                                    </a>
                                </h3>
                            </div>
                            <div class="info row">
                                <ul class="col-sm-6">
                                    <li>货号: <?php echo ($goods['goods_sn']); if(!empty($goods['product_sn'])): ?>-<?php echo ($goods['product_sn']); endif; ?></li>
                                </ul>
                                <div class="price col-sm-6">
                                    ￥<?php echo ($goods['price']*$goods['number']); ?>
                                    <?php if(!empty($goods['or_price'])): ?><span>￥<?php echo ($goods['or_price']*$goods['number']); ?></span><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <label>
                                    <input
                                    class="form-inline select"

                                    <?php if(($goods['checked']) == "1"): ?>checked="true"<?php endif; ?>
                                    type="checkbox"
                                    data-product_sn="<?php echo ($goods['product_sn']); ?>"
                                    data-goods_id="<?php echo ($goods['id']); ?>"
                                    data-select_cart_url="<?php echo U('Home/cart/select_cart');?>"
                                    name="cart_select[]"
                                    value="1" />
                                    <?php if(($goods['is_on_sale']) == "0"): ?><span style="color:red;">已下架</span><?php endif; ?>
                                </label>
                                <span>
                                    <label>数量: </label>
                                    <input class="form-inline number" type="text" value="<?php echo ($goods['number']); ?>" />
                                    <?php if(($goods['has_number']) == "0"): ?><span style="color:red;">商品库存不足</span><?php endif; ?>
                                    <a href="javascript:void(0)" class="btn btn-enbuy update-cart"
                                       data-product_sn="<?php echo ($goods['product_sn']); ?>"
                                       data-goods_id="<?php echo ($goods['id']); ?>"
                                       data-update_cart_url="<?php echo U('Home/cart/update_cart');?>">
                                        修改
                                    </a>
                                </span>
                                <a href="javascript:void(0)"
                                   class="btn btn-default remove-cart enable-remove"
                                   data-product_sn="<?php echo ($goods['product_sn']); ?>"
                                   data-goods_id="<?php echo ($goods['id']); ?>"
                                   data-remove_cart_url="<?php echo U('Home/cart/remove_cart');?>">
                                    删除
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>

    <?php if(!empty($data['usePromotions'])): ?><div class="row">
            <div class="col-xs-12"><h4>促销活动</h4></div>
        </div>
        <div class="row">
            <?php if(is_array($data['usePromotions'])): $i = 0; $__LIST__ = $data['usePromotions'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$usePromotion): $mod = ($i % 2 );++$i;?><div class="col-xs-12">
               <p>
                   <span>活动名：<?php echo ($usePromotion['name']); ?></span>
                   <?php if(($usePromotion['type']) == "5"): ?><span>送赠品</span>
                   <?php else: ?>
                       <span>优惠：<?php echo ($usePromotion['price']); ?></span><?php endif; ?>
               </p>
               </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div><?php endif; ?>
    <?php if(!empty($data['gift'])): ?><div class="row">
            <div class="col-xs-12"><h4>赠品</h4></div>
        </div>
        <?php if(is_array($data['gift'])): $i = 0; $__LIST__ = $data['gift'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><div class="row">
                <div class="col-lg-12">
                    <div class="product well check-cart">
                        <div class="col-sm-2">
                            <div class="image">
                                <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>">
                                    <img src="<?php echo ($goods['thumb']); ?>" />
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-10">
                            <div class="caption">
                                <div class="name" <?php if(($goods['disabled']) == "1"): ?>style="text-decoration: line-through;"<?php endif; ?>>
                                <h3>
                                    <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>">
                                        <?php echo ($goods['name']); ?> <?php echo ($goods['attr_value']); ?>
                                    </a>
                                </h3>
                            </div>
                            <div class="info row">
                                <ul class="col-sm-6">
                                    <li>货号: <?php echo ($goods['goods_sn']); if(!empty($goods['product_sn'])): ?>-<?php echo ($goods['product_sn']); endif; ?></li>
                                </ul>
                                <div class="price col-sm-6">
                                    ￥<?php echo ($goods['price']*$goods['number']); ?>
                                    <?php if(!empty($goods['or_price'])): ?><span>￥<?php echo ($goods['or_price']*$goods['number']); ?></span><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <label>

                                    <?php if(($goods['is_on_sale']) == "0"): ?><span style="color:red;">已下架</span><?php endif; ?>
                                </label>
                                    <span>
                                        <label>数量: </label> <?php echo ($goods['number']); ?>

                                        <?php if(($goods['has_number']) == "0"): ?><span style="color:red;">商品库存不足</span><?php endif; ?>
                                    </span>

                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    <div class="row">
        <?php if(!empty($data['error']['goods'])): ?><div class="col-xs-12 has-error">
                <span class="help-block"><?php echo ($data['error']['goods']); ?></span>
            </div><?php endif; ?>
        <div class="col-sm-12">
            <div class="pull-right"><a href="<?php echo U('Home/index/index');?>" class="btn btn-1">继续购物</a></div>
        </div>
    </div>
    <div class="row">
        <div class="pricedetails col-sm-12">
            <div class="pull-right">
                <table>
                    <h6>小计</h6>
                    <?php if(is_array($data['totals'])): $i = 0; $__LIST__ = $data['totals'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$total): $mod = ($i % 2 );++$i; if(($i) != $data['totalRow']): ?><tr>
                                <td><?php echo ($total['name']); ?></td>
                                <td><?php echo ($total['price']); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr style="border-top: 1px solid #333">
                                <td><h5><?php echo ($total['name']); ?></h5></td>
                                <td><?php echo ($total['price']); ?></td>
                            </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </table>
                <div class="pull-right">
                    <a href="javascript:void(0)" class="btn <?php if(empty($data['error'])): ?>btn-checkout<?php else: ?>btn-discheckout<?php endif; ?> checkout">结算</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="text-center">购物车内暂时没有商品,<a href="<?php echo U('Home/index/index');?>" >继续购物</a>。</div>
        </div>
    </div><?php endif; ?>