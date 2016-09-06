<?php if (!defined('THINK_PATH')) exit();?>
<?php if(!empty($images)): ?><div class="row">
    <div class="col-lg-12">
        <!-- Carousel -->
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->

            <ol class="carousel-indicators hidden-xs">
                <?php if(is_array($images)): $i = 0; $__LIST__ = $images;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$image): $mod = ($i % 2 );++$i;?><li data-target="#carousel-example-generic" data-slide-to="<?php echo ($i-1); ?>" class="active"></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <?php if(is_array($images)): $i = 0; $__LIST__ = $images;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$image): $mod = ($i % 2 );++$i;?><div class="item <?php if(($i) == "1"): ?>active<?php endif; ?>">
                    <a href="
                         <?php if(!empty($image['link'])): echo U($image['link']);?>
                         <?php else: ?>
                             javascript:void(0)<?php endif; ?>" >
                        <img src="<?php echo ($image['url']); ?>" alt="<?php echo ($image['title']); ?>" />
                    </a>

                    <div class="header-text hidden-xs">
                        <div class="col-md-12 text-center"></div>
                    </div>

                </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <!-- Controls -->
            <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
        </div>
        <!-- /carousel -->
    </div>
</div><?php endif; ?>