<?php if (!defined('THINK_PATH')) exit();?>
<div class="row">
    <div class="col-xs-12"><h4>收获地址</h4></div>
</div>
<div class="row address">
    <?php echo ($data['addressList']); ?>
</div>
<?php if(!empty($data['error']['addressId'])): ?><div class="row">
        <div class="col-xs-12 has-error">
            <span class="help-block"><?php echo ($data['error']['addressId']); ?></span>
        </div>
    </div><?php endif; ?>
<div class="row">
    <div class="col-xs-12">
        <a href="javascript:void(0)" class="btn-address enable" data-url="<?php echo U('Home/address/add');?>">
            <span class="glyphicon glyphicon-plus"></span> 新增地址
        </a>
    </div>
</div>
<div class="row">
    <div class="col-xs-12"><h4>配送方式</h4></div>
</div>
<div class="row shipping">
    <?php if(is_array($data['shippingList'])): $i = 0; $__LIST__ = $data['shippingList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shipping): $mod = ($i % 2 );++$i;?><div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
            <div class="shipping-content text-center
                  <?php if(!empty($_SESSION['checkout']['shippingCode'])): if(($shipping["code"]) == $_SESSION['checkout']['shippingCode']): ?>selected<?php endif; endif; ?>"
                  data="<?php echo ($shipping['code']); ?>">
                 <?php echo ($shipping['name']); ?>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    <input type="hidden" name="shipping_code" value="<?php if(!empty($_SESSION['checkout']['shippingCode'])): echo ($_SESSION['checkout']['shippingCode']); endif; ?>" autocomplete="off" />
    <?php if(!empty($data['error']['shippingCode'])): ?><div class="col-xs-12 has-error">
            <span class="help-block"><?php echo ($data['error']['shippingCode']); ?></span>
        </div><?php endif; ?>
</div>

<div class="row">
    <div class="col-xs-12"><h4>支付方式</h4></div>
</div>
<div class="row payment">
    <?php if(is_array($data['paymentList'])): $i = 0; $__LIST__ = $data['paymentList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$payment): $mod = ($i % 2 );++$i;?><div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
            <div class="payment-content text-center
                 <?php if(!empty($_SESSION['checkout']['shippingCode'])): if(($payment["code"]) == $_SESSION['checkout']['paymentCode']): ?>selected<?php endif; endif; ?>"
                 data="<?php echo ($payment['code']); ?>">
                 <?php echo ($payment['name']); ?>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    <input type="hidden" name="payment_code" value="<?php if(!empty($_SESSION['checkout']['paymentCode'])): echo ($_SESSION['checkout']['paymentCode']); endif; ?>" autocomplete="off" />
    <?php if(!empty($data['error']['paymentCode'])): ?><div class="col-xs-12 has-error">
            <span class="help-block"><?php echo ($data['error']['paymentCode']); ?></span>
        </div><?php endif; ?>
    <div class="clear"></div>
    <div class="form-group <?php if(!empty($data['error']['pay_integral'])): ?>has-error<?php endif; ?> " >
        <?php if(!empty($data['pay_integral'])): ?><div class="inner-payment col-xs-12">
            使用积分 <input type="text" placeholder="积分" name="pay_integral" id="pay_integral" value="<?php echo ($data['use_pay_integral']); ?>" autocomplete="off" />
            <p>您还有<span style="color:red;"><?php echo ($data['pay_integral']); ?></span>积分<?php if(($data['pay_integral']) > $data['canUseIntegral']): ?>,此订单最多可用<span style="color:red;"><?php echo ($data['canUseIntegral']); ?></span>积分.<?php endif; ?></p>
            <?php if(!empty($data['error']['pay_integral'])): ?><span class="help-block"><?php echo ($data['error']['pay_integral']); ?></span><?php endif; ?>
        </div><?php endif; ?>
    </div>
    <div class="form-group <?php if(!empty($data['error']['money'])): ?>has-error<?php endif; ?> " >
        <?php if(!empty($data['money'])): ?><div class="inner-payment col-xs-12">
            使用余额 <input type="text" placeholder="余额" name="money" id="money" value="<?php echo ($data['use_money']); ?>" autocomplete="off" />
            <p>您还有<span style="color:red;"><?php echo ($data['money']); ?></span>余额</p>
            <?php if(!empty($data['error']['money'])): ?><span class="help-block"><?php echo ($data['error']['money']); ?></span><?php endif; ?>
        </div><?php endif; ?>
    </div>

</div>

<div class="row">
    <div class="col-xs-12"><h4>开具发票</h4></div>
</div>
<div class="row invoice">
    <div class="col-lg-12">
        <div class="invoice_content">
            <div class="invoice_title">
                <label><input type="radio" name="invoice_status" value="1" <?php if(($data['invoice_status']) == "1"): ?>checked="true"<?php endif; ?> autocomplete="off" />需要发票</label>
                <label><input type="radio" name="invoice_status" value="0" <?php if(($data['invoice_status']) == "0"): ?>checked="true"<?php endif; ?> autocomplete="off" />不需要发票</label>
            </div>
            <div class="form-group invoice_body <?php if(!empty($data['error']['invoice_title'])): ?>has-error<?php endif; ?>" <?php if(($data['invoice_status']) == "0"): ?>style="display:none;"<?php endif; ?> >
                <div class="col-sm-10">

                    <select name="invoice_type" autocomplete="off">
                        <option value="1" <?php if(($data['invoice_type']) == "1"): ?>selected="true"<?php endif; ?>>个人</option>
                        <option value="2"  <?php if(($data['invoice_type']) == "2"): ?>selected="true"<?php endif; ?>>公司</option>
                    </select>
                    <input type="text" placeholder="抬头" name="invoice_title" id="invoice_title" value="<?php echo ($data['invoice_title']); ?>" autocomplete="off" />
                    <?php if(!empty($data['error']['invoice_title'])): ?><span class="help-block"><?php echo ($data['error']['invoice_title']); ?></span><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($data['couponList'])): ?><div class="row">
    <div class="col-xs-12"><h4>优惠券</h4></div>
</div>
<div class="row comment">
    <div class="form-group col-xs-12">
        <select name="coupon" autocomplete="off">
            <option value="">请选择优惠券</option>
            <?php if(is_array($data['couponList'])): $i = 0; $__LIST__ = $data['couponList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$coupon): $mod = ($i % 2 );++$i;?><option
                    <?php if(($coupon['id']) == $data['coupon']): ?>selected="true"<?php endif; ?>
                     value="<?php echo ($coupon['id']); ?>">
                    <?php echo ($coupon['name']); ?>
                </option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>
</div><?php endif; ?>
<div class="row">
    <div class="col-xs-12"><h4>备注</h4></div>
</div>
<div class="row comment">
   <div class="form-group col-xs-12">
       <input type="text" class="form-control" placeholder="备注" name="comment" id="comment" value="<?php echo ($data['comment']); ?>" />
   </div>
</div>


<div class="row">
    <div class="col-xs-12"><h4>商品清单</h4></div>
</div>
<div class="row goods">
    <?php if(is_array($data['goods'])): $i = 0; $__LIST__ = $data['goods'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><div class="col-sm-6 col-xs-12">
            <div class="product well check-cart">
                <div class=" col-sm-3">
                    <div class="image">
                        <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>">
                            <img src="<?php echo ($goods['thumb']); ?>" />
                        </a>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="caption">
                        <div class="name" <?php if(($goods['disabled']) == "1"): ?>style="text-decoration: line-through;"<?php endif; ?>>
                            <h3>
                                <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>" >
                                    <?php echo ($goods['name']); ?> <?php echo ($goods['attr_value']); ?>
                                </a>
                            </h3>
                        </div>
                        <div class="info">
                            <ul>
                                <li>货号: <?php echo ($goods['goods_sn']); if(!empty($goods['product_sn'])): ?>-<?php echo ($goods['product_sn']); endif; ?></li>
                            </ul>
                        </div>
                        <div>
                            <div class="price">
                                ￥<?php echo ($goods['price']); ?>X<?php echo ($goods['number']); ?>
                                <?php if(!empty($goods['or_price'])): ?><span>￥<?php echo ($goods['or_price']); ?>X<?php echo ($goods['number']); ?></span><?php endif; ?>
                            </div>
                            <div>
                                <?php if(($goods['is_on_sale']) == "0"): ?><span style="color:red;">已下架</span><?php endif; ?>
                                <?php if(($goods['has_number']) == "0"): ?><span style="color:red;">商品库存不足</span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>

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
<div class="row goods">
    <?php if(is_array($data['gift'])): $i = 0; $__LIST__ = $data['gift'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><div class="col-sm-6 col-xs-12">
            <div class="product well check-cart">
                <div class=" col-sm-3">
                    <div class="image">
                        <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>">
                            <img src="<?php echo ($goods['thumb']); ?>" />
                        </a>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="caption">
                        <div class="name" <?php if(($goods['disabled']) == "1"): ?>style="text-decoration: line-through;"<?php endif; ?>>
                        <h3>
                            <a href="<?php echo U('Home/goods/index',array('id'=>$goods['id']));?>" >
                                <?php echo ($goods['name']); ?> <?php echo ($goods['attr_value']); ?>
                            </a>
                        </h3>
                    </div>
                    <div class="info">
                        <ul>
                            <li>货号: <?php echo ($goods['goods_sn']); if(!empty($goods['product_sn'])): ?>-<?php echo ($goods['product_sn']); endif; ?></li>
                        </ul>
                    </div>
                    <div>
                        <div class="price">
                            ￥<?php echo ($goods['price']); ?>X<?php echo ($goods['number']); ?>
                            <?php if(!empty($goods['or_price'])): ?><span>￥<?php echo ($goods['or_price']); ?>X<?php echo ($goods['number']); ?></span><?php endif; ?>
                        </div>
                        <div>
                            <?php if(($goods['is_on_sale']) == "0"): ?><span style="color:red;">已下架</span><?php endif; ?>
                            <?php if(($goods['has_number']) == "0"): ?><span style="color:red;">商品库存不足</span><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
</div><?php endforeach; endif; else: echo "" ;endif; ?>
</div><?php endif; ?>



<?php if(!empty($data['error']['goods'])): ?><div class="col-xs-12 has-error">
        <span class="help-block"><?php echo ($data['error']['goods']); ?></span>
    </div><?php endif; ?>



<div class="row error-info" style="display:none;">
    <div class="col-xs-12 has-error">
        <span class="help-block"></span>
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
                <!--<a href="javascript:void(0)" class="btn <?php if(empty($data['error'])): ?>btn-checkout<?php else: ?>btn-discheckout<?php endif; ?> order">下单</a>-->
                <a href="javascript:void(0)" class="btn btn-checkout order">下单</a>
            </div>
        </div>
    </div>
</div>