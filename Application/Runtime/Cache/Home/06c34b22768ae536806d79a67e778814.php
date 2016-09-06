<?php if (!defined('THINK_PATH')) exit();?>
<?php if(!empty($data)): ?><div class="widget wid-product">
        <div class="heading"><h4>最近浏览</h4></div>
        <div class="content">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><div class="product">
                    <a href="<?php echo U('Home/goods/index', array('id'=>$goods['id']));?>">
                        <img src="<?php echo ($goods['thumb']); ?>" />
                    </a>
                    <div class="wrapper">
                        <h5>
                            <a href="<?php echo U('Home/goods/index', array('id'=>$goods['id']));?>">
                                <?php echo ($goods['name']); ?>
                            </a>
                        </h5>
                        <div class="price">
                            <label>￥<?php echo ($goods['price']); ?></label>
                            <?php if(!empty($goods['or_price'])): ?><span>￥<?php echo ($goods['or_price']); ?></span><?php endif; ?>
                        </div>
                    </div>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div><?php endif; ?>