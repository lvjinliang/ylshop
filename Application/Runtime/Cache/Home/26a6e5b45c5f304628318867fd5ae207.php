<?php if (!defined('THINK_PATH')) exit();?>
<?php if(!empty($images)): ?><div class="row">
        <div class="banner">
            <?php if(is_array($images)): $i = 0; $__LIST__ = array_slice($images,0,$col,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$image): $mod = ($i % 2 );++$i;?><div class="col-sm-<?php echo floor(12/$col);?>">
               <a href="
                <?php if(!empty($image['link'])): echo U($image['link']);?>
                <?php else: ?>
                    javascript:void(0)<?php endif; ?>" title="<?php echo ($image['title']); ?>" >
                <img src="<?php echo ($image['url']); ?>" alt="<?php echo ($image['title']); ?>" width="100%" />
                </a>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div><?php endif; ?>