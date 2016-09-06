<?php if (!defined('THINK_PATH')) exit();?>
<?php if(!empty($data)): if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$address): $mod = ($i % 2 );++$i;?><div class="col-md-3 col-sm-4">
        <div class="addr-content <?php if(($address['id']) == $_SESSION['checkout']['addressId']): ?>selected<?php endif; ?>" >
            <div class="addr-hd">
                <span class="prov"><?php echo ($address['province_name']); ?></span>
                <span class="city"><?php echo ($address['city_name']); ?></span>
                <span class="dist"><?php echo ($address['district_name']); ?></span>
                <label><input type="radio" <?php if(($address['id']) == $_SESSION['checkout']['addressId']): ?>checked="true"<?php endif; ?> name="address_id" value="<?php echo ($address['id']); ?>" /></label>
            </div>
            <div class="addr-bd" >
                <p class="telephone">
                    <span><?php echo ($address['telephone']); ?></span>
                    <span class="name"><?php echo ($address['name']); ?>（收）</span>
                </p>
                <p class="street">
                    <span class="j_4tip"></span>
                    <span ><?php echo ($address['address']); ?></span>
                </p>
            </div>
            <div class="addr-toolbar">
                <a class="btn-update-address enable" data-url="<?php echo U('Home/address/update',array('id'=>$address['id']));?>" href="javascript:void(0);" title="修改地址">修改</a>
                <a class="btn-delete-address enable" data-url="<?php echo U('Home/address/delete',array('id'=>$address['id']));?>" data-getAddress-url="<?php echo U('Home/checkout/ajax_get_addressList');?>" href="javascript:void(0);" title="删除地址">删除</a>
                <?php if(($address['id']) == $address['address_id']): ?><a class="btn-set-default-address disable"  href="javascript:void(0);" style="color:red;">默认地址</a>
                <?php else: ?>
                    <a class="btn-set-default-address enable" data-url="<?php echo U('Home/address/set_default_address',array('id'=>$address['id']));?>" data-getAddress-url="<?php echo U('Home/checkout/ajax_get_addressList');?>" href="javascript:void(0);" title="设成默认地址">设成默认地址</a><?php endif; ?>
            </div>
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
<?php else: ?>
    <div class="text-center">您还没有地址，请先添加!</div><?php endif; ?>