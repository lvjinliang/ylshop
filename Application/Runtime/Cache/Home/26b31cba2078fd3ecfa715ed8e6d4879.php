<?php if (!defined('THINK_PATH')) exit();?>
<nav id="menu" class="navbar">
    <div class="container">
        <div class="navbar-header">
            <span id="heading" class="visible-xs">分类</span>
            <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse"
                    data-target=".navbar-ex1-collapse">
                <i class="fa fa-bars"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav main-menu">
                <li><a href="/">首页</a></li>
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                        <a href="<?php echo U('Home/category/index',array('id'=>$menu['id']));?>"><?php echo ($menu['name']); ?></a>
                        <?php if(!empty($menu['children'])): ?><ul class="sub-menu">
                                <?php if(is_array($menu['children'])): $i = 0; $__LIST__ = $menu['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$submenu): $mod = ($i % 2 );++$i;?><li >
                                        <a href="<?php echo U('Home/category/index',array('id'=>$submenu['id']));?>"><?php echo ($submenu['name']); ?></a>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul><?php endif; ?>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>


            </ul>
        </div>
    </div>
</nav>