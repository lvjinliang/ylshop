<?php if (!defined('THINK_PATH')) exit();?>
<div id="sidebar">
    <div class="sidebar_flag">
        <button class="show_sidebar btn">
            <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
        </button>
        <span class="left_username">
            欢迎您：<span><?php echo ($username); ?></span>
        </span>
    </div>
    <ul class="menu menu_float_right">

        <?php if(is_array($menus)): $i = 0; $__LIST__ = $menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php if(($menu['isActive']) == "1"): ?>active<?php else: ?>off<?php endif; ?>">
                <a href="<?php echo ($menu['name']); ?>" title="<?php echo ($menu['title']); ?>">
                    <div class="menu_icon pull-left"><span class="glyphicon glyphicon-hand-right" aria-hidden="true"></span></div>
                    <span class="text"><?php echo ($menu['title']); ?></span>
                </a>
                <?php if(!empty($menu['children'])): ?><ul class="submenu ">
                    <?php if(is_array($menu['children'])): $i = 0; $__LIST__ = $menu['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$submenu): $mod = ($i % 2 );++$i;?><li  class="<?php if(($submenu['isActive']) == "1"): ?>active<?php else: ?>off<?php endif; ?>">
                        <a href="<?php echo ($submenu['name']); ?>"><?php echo ($submenu['title']); ?></a>
                        <?php if(!empty($submenu['children'])): ?><ul class="ssmenu">
                        <?php if(is_array($submenu['children'])): $i = 0; $__LIST__ = $submenu['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ssmenu): $mod = ($i % 2 );++$i;?><li class="<?php if(($ssmenu['isActive']) == "1"): ?>active<?php else: ?>off<?php endif; ?>">
                                <a href="<?php echo ($ssmenu['name']); ?>"><?php echo ($ssmenu['title']); ?></a>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul><?php endif; ?>
                <div class="clear"></div>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>

    </ul>
    <div class="clear"></div>
</div>