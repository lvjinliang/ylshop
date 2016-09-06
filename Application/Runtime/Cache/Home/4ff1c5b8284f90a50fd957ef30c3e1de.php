<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh">
<head>
    <title><?php echo ($seoTitle); ?></title>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="<?php echo ($seoDescription); ?>">
    <meta name="keywords" content="<?php echo ($seoKeywords); ?>">
    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/Public/Home/css/jquery-ui.min.css" type="text/css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/Public/Home/css/style.css">
    <!-- Custom Fonts -->
    <link rel="stylesheet" href="/Public/Home/font-awesome/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/Public/Home/fonts/font-slider.css" type="text/css">
    <?php if(is_array($styles)): $i = 0; $__LIST__ = $styles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$style): $mod = ($i % 2 );++$i;?><link rel="stylesheet" href="<?php echo ($style['href']); ?>" type="text/css"><?php endforeach; endif; else: echo "" ;endif; ?>
    <!-- jQuery and Modernizr-->
    <script src="/Public/Home/js/jquery-1.9.1.min.js"></script>
    <!-- Core JavaScript Files -->
    <script src="/Public/Home/js/bootstrap.min.js"></script>
    <script src="/Public/Home/js/jquery-ui.min.js"></script>
    <?php if(($showMenu) == "1"): ?><script src="/Public/Home/js/stickUp.min.js"></script><?php endif; ?>
    <script src="/Public/Home/js/jquery.fly.min.js"></script>
    <?php if(is_array($scripts)): $i = 0; $__LIST__ = $scripts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$script): $mod = ($i % 2 );++$i;?><script type="text/javascript" language="javascript" src="<?php echo ($script['src']); ?>" ></script><?php endforeach; endif; else: echo "" ;endif; ?>
    <script src="/Public/Home/js/main.js"></script>
    <!--[if lt IE 9]>
    <script src="/Public/Home/js/html5shiv.js"></script>
    <script src="/Public/Home/js/respond.min.js"></script>
    <![endif]-->
    <!--[if lte IE 9]>
    <script src="/Public/Home/js/requestAnimationFrame.js"></script>
    <![endif]-->

</head>
<body>
<!--Top-->
    <nav id="top">
        <div class="container">
            <div class="row">
                <div>
                    <ul class="top-link">
                        <?php if(!empty($_SESSION['account']['id'])): ?><li><a href="<?php echo U('Account/index/index');?>"><span class="glyphicon glyphicon-user"></span> 用户中心</a></li>
                        <li><a href="<?php echo U('Home/login/logout');?>"><span class="glyphicon glyphicon glyphicon-log-out"></span> 退出</a></li>
                        <?php else: ?>
                        <li><a href="<?php echo U('Home/login/index');?>"><span class="glyphicon glyphicon glyphicon-log-in"></span> 登录</a></li>
                        <li><a href="<?php echo U('Home/register/index');?>"><span class="glyphicon glyphicon glyphicon-registration-mark"></span> 注册</a></li><?php endif; ?>
                        <li><a href="#"><span class="glyphicon glyphicon-envelope"></span> 联系我们</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!--Header-->
    <header class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div id="logo"><a href="/"><img src="/Public/Home/images/logo.png"/></a></div>
            </div>
            <div class="col-xs-9 col-sm-6">
                <form class="form-search">
                    <input type="text" class="search-input input-medium search-query" value="<?php echo ($searchKey); ?>">
                    <button type="button" class="search-btn btn" data="<?php echo U('search/index');?>"><span class="glyphicon glyphicon-search"></span></button>
                </form>
            </div>
            <div class="col-xs-3 col-sm-2">
                <?php if(($showMenu) == "1"): ?><div id="cart">
                    <a class="btn btn-1 cart" href="<?php echo U('Home/cart/index');?>">
                        <span class="glyphicon glyphicon-shopping-cart"></span>
                        <span class="cart-number"><?php echo W('Home/Common/init_cart');?><span>
                    </a>
                </div><?php endif; ?>
            </div>
        </div>
    </header>
    <!--Navigation-->
    <?php if(($showMenu) == "1"): echo W('Home/Common/Menu'); endif; ?>
    <div id="page-content" class="home-page">
    <div class="container">
        <?php echo W('Home/Common/main_ad', array('main-banner'));?>
        <?php echo W('Home/Common/col_banner', array('sub-banner','3'));?>
        <div class="row">
            <div class="col-lg-12">
                <div class="heading"><h2>新品</h2></div>
                <div class="products">
                    <?php echo W('Home/Goods/getGoodsByPositionId', array(12,4,3,3,6,12));?>
                </div>
            </div>
        </div>
        <?php echo W('Home/Common/col_banner', array('two-banner','2'));?>
        <div class="row">
            <div class="col-lg-12">
                <div class="heading"><h2>热门</h2></div>
                <div class="products">
                    <?php echo W('Home/Goods/getGoodsByPositionId', array(15,4,3,3,6,12));?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="heading"><h2>精品</h2></div>
                <div class="products">
                    <?php echo W('Home/Goods/getGoodsByPositionId', array(13,4,3,3,6,12));?>
                </div>
            </div>
        </div>
    </div>
</div>

    <footer>
        <?php if(($showMenu) == "1"): ?><div class="container">
            <div class="wrap-footer">
                <div class="row">
                    <div class="col-md-4 col-footer footer-1">
                        <img src="/Public/Home/images/logofooter.png"/>

                        <p>我们愿与更多中小企业一起努力，一起成功!</p>
                    </div>
                    <div class="col-md-4 col-footer footer-2">
                        <div class="heading"><h4>关于我们</h4></div>
                        <ul>
                            <li><a href="#">关于我们</a></li>
                            <li><a href="#">使用说明</a></li>
                            <li><a href="#">注删协议</a></li>
                        </ul>
                    </div>

                    <div class="col-md-4 col-footer footer-4">
                        <div class="heading"><h4>联系我们</h4></div>
                        <ul>
                            <li><span class="glyphicon glyphicon-home"></span>上海市徐汇区宝石园20栋18楼</li>
                            <li><span class="glyphicon glyphicon-earphone"></span>186****8692</li>
                            <li><span class="glyphicon glyphicon-envelope"></span>411481190@qq.com</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><?php endif; ?>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        Copyright &copy; 2015.Company name All rights reserved.
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            <ul>
                                <li></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <?php echo W('Home/Common/bi');?>
</body>

</html>