<?php if (!defined('THINK_PATH')) exit();?>
<?php if(!empty($data)): if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><div class="product col-lg-<?php echo ($colLg); ?> col-md-<?php echo ($colMd); ?> col-sm-<?php echo ($colSm); ?> col-xs-<?php echo ($colXs); ?> ">
        <div class="image">
            <a href="<?php echo U('Home/goods/index', array('id'=>$goods['id']));?>" title="<?php echo ($goods['name']); ?>"><img src="<?php echo ($goods['thumb']); ?>" alt="<?php echo ($goods['name']); ?>"/></a>
        </div>
        <!--<div class="buttons">
            <a class="btn cart add-cart" href="javascript:void(0);"><span class="glyphicon glyphicon-shopping-cart"></span></a>
            <a class="btn wishlist" href="#"><span class="glyphicon glyphicon-heart"></span></a>
            <a class="btn compare" href="#"><span class="glyphicon glyphicon-transfer"></span></a>
        </div>-->
        <div class="caption">
            <div class="name">
                <h3>
                    <a href="<?php echo U('Home/goods/index', array('id'=>$goods['id']));?>" title="<?php echo ($goods['name']); ?>">
                        <?php echo ($goods['name']); ?>
                    </a>
                </h3>
            </div>

            <div class="price">
                <label>￥<?php echo ($goods['price']); ?></label>
                <?php if(!empty($goods['or_price'])): ?><span>￥<?php echo ($goods['or_price']); ?></span><?php endif; ?>
            </div>
            <!--<div class="rating"><span class="glyphicon glyphicon-star"></span><span
                    class="glyphicon glyphicon-star"></span><span
                    class="glyphicon glyphicon-star"></span><span
                    class="glyphicon glyphicon-star"></span><span
                    class="glyphicon glyphicon-star-empty"></span></div>-->
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
<?php else: ?>
    <div style="color:red;">暂无数据</div><?php endif; ?>