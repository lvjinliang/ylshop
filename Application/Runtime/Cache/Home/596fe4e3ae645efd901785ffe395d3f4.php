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
    <div id="page-content" class="goods-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumb">
                    <?php if(is_array($breadcrumbs)): $i = 0; $__LIST__ = $breadcrumbs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$breadcrumb): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($breadcrumb['href']); ?>"><?php echo ($breadcrumb['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
        </div>
        <div class="row">
            <div id="main-content" class="col-md-8">
                <div class="product">
                    <div class="col-sm-6">
                        <div class="image swiper-container">
                            <div class="swiper-wrapper">
                                <?php if(is_array($data['goodsInfo']['gallerys'])): $i = 0; $__LIST__ = $data['goodsInfo']['gallerys'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gallery): $mod = ($i % 2 );++$i;?><div class="swiper-slide"><img class="img-responsive" src="<?php echo ($gallery['url']); ?>"></div><?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                            <!-- Add Pagination -->
                            <div class="swiper-pagination"></div>
                            <!-- Add Arrows -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="caption">
                            <div class="name"><h3><?php echo ($data['goodsInfo']['name']); ?></h3></div>
                            <div class="info">
                                <ul>
                                    <li>品牌: <?php echo ($data['goodsInfo']['brand_name']); ?></li>
                                    <li>货号: <?php echo ($data['goodsInfo']['goods_sn']); if(!empty($data['goodsInfo']['product_sn'])): ?>-<span class="product_sn"><?php echo ($data['goodsInfo']['product_sn']); ?></span><?php endif; ?></li>
                                    <li>库存:
                                        <span class="number">
                                            <?php echo ($data['goodsInfo']['number']); ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="price">
                                <label>￥<?php echo ($data['goodsInfo']['price']); ?></label>
                                <input type="hidden" name="price" value="<?php echo ($data['goodsInfo']['old_price']); ?>" />
                                <?php if(!empty($data['goodsInfo']['or_price'])): ?><span>￥<?php echo ($data['goodsInfo']['or_price']); ?></span>
                                    <input type="hidden" name="or_price" value="<?php echo ($data['goodsInfo']['old_or_price']); ?>" /><?php endif; ?>
                            </div>
                            <div class="attr">
                                <?php if(!empty($data['goodsInfo']['attrSelect'])): ?><ul>
                                    <?php if(is_array($data['goodsInfo']['attrSelect'])): $i = 0; $__LIST__ = $data['goodsInfo']['attrSelect'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attrSelect): $mod = ($i % 2 );++$i;?><li><?php echo ($key); ?>:
                                        <?php if(is_array($attrSelect)): $i = 0; $__LIST__ = $attrSelect;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i;?><span class="goods-attrs <?php if(($i) == "1"): ?>goods-attrs-on<?php endif; ?>" attr-id="<?php echo ($attr['attr_id']); ?>"><?php echo ($attr['attr_value']); ?></span><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul><?php endif; ?>
                                <input type="hidden" name="id" value="<?php echo ($data['goodsInfo']['id']); ?>" />
                                <input type="hidden" name="change_goods_attr_url" value="<?php echo ($changeGoodsAttrUrl); ?>" />

                            </div>
                            <!--<div class="rating">
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star-empty"></span>
                            </div>-->
                            <div class="well">
                                <label>数量: </label>
                                <input class="form-inline number" type="text" value="1">
                                <a href="javascript:void(0)"
                                   class="btn <?php if(($data['goodsInfo']['number']) > "0"): ?>btn-enbuy<?php else: ?>btn-disbuy<?php endif; ?> add-cart"
                                    data-product_sn="<?php echo ($data['goodsInfo']['product_sn']); ?>"

                                    data-goods_id="<?php echo ($data['goodsInfo']['id']); ?>"
                                    data-add_cart_url="<?php echo U('Home/cart/add_cart');?>"
                                >加入购物车</a>
                            </div>
                            <!--<div class="share well">
                                <strong style="margin-right: 13px;">Share :</strong>
                                <a href="#" class="share-btn" target="_blank">
                                    <i class="fa fa-twitter"></i>
                                </a>
                                <a href="#" class="share-btn" target="_blank">
                                    <i class="fa fa-facebook"></i>
                                </a>
                                <a href="#" class="share-btn" target="_blank">
                                    <i class="fa fa-linkedin"></i>
                                </a>
                            </div>-->
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="product-desc">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#description" role="tab" data-toggle="tab">商品描述</a>
                        </li>
                        <li role="presentation">
                            <a href="#attr" role="tab" data-toggle="tab">商品属性</a>
                        </li>
                        <li role="presentation" >
                            <a href="#review" role="tab" data-toggle="tab">评论</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="description" class="tab-pane fade in active">
                            <?php echo htmlspecialchars_decode($data['goodsInfo']['content']);?>
                        </div>
                        <div id="attr" class="tab-pane fade table-responsive">
                            <?php if(!empty($data['goodsInfo']['attrOnly'])): ?><table class="table table-hover table-bordered ">
                                <tbody>
                                    <?php if(is_array($data['goodsInfo']['attrOnly'])): $i = 0; $__LIST__ = $data['goodsInfo']['attrOnly'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attrOnly): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($key); ?></td>
                                        <td><?php echo ($attrOnly['attr_value']); ?></td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table><?php endif; ?>
                        </div>
                        <div id="review" class="tab-pane fade">
                            <div class="review-text">
                                <?php echo ($data['review']); ?>
                            </div>

                        </div>
                    </div>
                </div>
                <!--<div class="product-related">
                    <div class="heading"><h2>相关商品</h2></div>
                    <div class="products">

                    </div>
                </div>-->
            </div>
            <div id="sidebar" class="col-md-4">
                <div class="widget wid-categories">
                    <div class="heading"><h4>分类</h4></div>
                    <div class="content">
                        <ul>
                            <?php if(is_array($data['leftCategory'])): $i = 0; $__LIST__ = $data['leftCategory'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$leftCategory): $mod = ($i % 2 );++$i;?><li <?php if(($leftCategory['id']) == $data['goodsInfo']['category_id']): ?>class="active"<?php endif; ?>>
                                <a href="<?php echo U('Home/category/index',array('id'=>$leftCategory['id']));?>">
                                    <?php echo ($leftCategory['name']); ?>
                                </a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                </div>
                <?php echo W('Home/Goods/get_last_view');?>
            </div>
        </div>
    </div>

    <div id="dialog" title="">
        <p></p>
    </div>
</div>
<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        paginationClickable: true,
        spaceBetween: 30,
        centeredSlides: true,
        autoplay: false,
        autoplayDisableOnInteraction: false
    });
</script>

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