<?php if (!defined('THINK_PATH')) exit();?>
<?php if(!empty($data['reviewList'])): ?><div class="orders table-responsive">
        <table class="table table-bordered ">
            <?php if(is_array($data['reviewList'])): $i = 0; $__LIST__ = $data['reviewList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$review): $mod = ($i % 2 );++$i;?><tr>
                    <td colspan="6" class="order-title">
                        <span class="pull-left">下单时间：<?php echo date('Y-m-d',$review['order_date']);?></span>
                        <span class="pull-right">订单号：<?php echo ($review['order_no']); ?></span>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a href="<?php echo U('Home/goods/index',array('id'=>$review['goods_id']));?>">
                            <?php echo ($review['name']); echo ($review['goods_attr']); ?>
                        </a><br/>
                        <a href="<?php echo U('Home/goods/index',array('id'=>$review['goods_id']));?>">
                            <img class="order-img" src="<?php echo ($review['thumb']); ?>" />
                        </a>
                    </td>
                    <td class="product">
                        <div class="price">
                            <?php echo ($review['price']); ?>
                            <?php if(($review['or_price']) != "0"): ?><span><?php echo ($review['or_price']); ?></span><?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo ($review['number']); ?></td>
                    <td>
                        <p>
                            评论：<?php echo ($review['content']); ?> <br/>
                            评论者：<?php echo ($review['account_name']); ?> <br/>
                            评论时间：<?php echo date('Y-m-d', $review['date_added']);?>
                        </p>
                        <?php if(!empty($review['reply_content'])): ?><p>回复：<?php echo ($review['reply_content']); ?></p><?php endif; ?>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
    </div>
    <div class="row text-center">
        <?php echo ($data['show']); ?>
    </div>
<?php else: ?>
    <div class="text-center">暂无评价</div><?php endif; ?>