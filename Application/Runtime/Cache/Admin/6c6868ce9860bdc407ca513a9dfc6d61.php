<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title><?php echo ($seoTitle); ?></title>
    <meta name="viewport" content="width=device-width,
                                     initial-scale=1.0, 
                                     maximum-scale=1.0, 
                                     user-scalable=no">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo ($seoDescription); ?>">
    <meta name="keywords" content="<?php echo ($seoKeywords); ?>">
    <link href="/Public/Admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/jquery-ui.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/main.css" rel="stylesheet">
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="/Public/Admin/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="/Public/Admin/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/Public/Admin/js/html5shiv.min.js"></script>
    <script src="/Public/Admin/js/respond.min.js"></script>
    <![endif]-->
    <script src="/Public/Admin/js/jquery-1.9.1.min.js"></script>
    <script src="/Public/Admin/js/bootstrap.min.js"></script>
    <script src="/Public/Admin/js/jquery-ui.min.js"></script>
    <script src="/Public/Admin/js/jquery.ui.datepicker-zh-CN.js"></script>


    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="/Public/Ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/Public/Ueditor/ueditor.all.js"></script>

    <script src="/Public/Admin/js/main.js"></script>
</head>
<body>
<div id="header">
    <nav class="navbar navbar-inverse ">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo U('admin/index/index');?>">雨良商务系统</a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">

                    <li class="active"><a href="<?php echo U('admin/personal/index');?>">个人信息</a></li>
                    <li class="active"><a href="<?php echo U('admin/order/index');?>">订单列表</a></li>
                    <!--<li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">One more separated link</a></li>
                        </ul>
                    </li>-->
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo U('Home/index/index');?>" target="_blank">商城首页</a></li>
                    <li><a href="<?php echo U('Admin/logout/index');?>" >退出</a></li>

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

</div>
<div id="main">
    <?php echo W('Common/leftMenu');?>
    
<div id="main-content">
    <div class="main-title">
        <h2><?php if(empty($data['id'])): ?>添加<?php else: ?>编辑<?php endif; ?>商品</h2>
    </div>
    <div class="main-header">
        <div class="row">
            <div class="col-sm-8">
                <ol class="breadcrumb">
                    <?php if(is_array($breadcrumbs)): $i = 0; $__LIST__ = $breadcrumbs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$breadcrumb): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($breadcrumb['href']); ?>"><?php echo ($breadcrumb['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ol>
            </div>
            <div class="col-sm-4">
                <div class="btn-group pull-right form-botton" role="group">
                    <a href="<?php echo ($redirect); ?>" class="btn btn-primary" role="button">
                        返回
                    </a>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <?php if(!empty($errorInfo)): ?><div id="erroInfo" class="alert  alert-danger">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>失败！</strong><?php echo ($errorInfo); ?>
        </div><?php endif; ?>

    <div class="admin_form">
        <form class="form-horizontal" action="" id="theForm" method="post">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#base" role="tab" data-toggle="tab">基本信息</a>
                </li>
                <li role="presentation">
                    <a href="#description" role="tab" data-toggle="tab">商品详情</a>
                </li>
                <li role="presentation">
                    <a href="#attribute" role="tab" data-toggle="tab">商品属性</a>
                </li>
                <li role="presentation">
                    <a href="#other" role="tab" data-toggle="tab">其它信息</a>
                </li>
                <li role="presentation">
                    <a href="#gallery" role="tab" data-toggle="tab">商品相册</a>
                </li>

            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="base">
                    <div class="form-group <?php if(!empty($error['name'])): ?>has-error<?php endif; ?>">
                        <label for="name" class="col-sm-2 control-label">商品名：</label>
                        <div class="col-sm-10">
                            <input type="text" id="name" class="form-control" name="name" placeholder="商品名" value="<?php echo ($data['name']); ?>" />
                            <?php if(!empty($error['name'])): ?><span class="help-block"><?php echo ($error['name']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['goods_sn'])): ?>has-error<?php endif; ?>">
                        <label for="goods_sn" class="col-sm-2 control-label">商品货号：</label>
                        <div class="col-sm-10">
                            <input type="text" id="goods_sn" class="form-control" name="goods_sn" placeholder="商品货号" value="<?php echo ($data['goods_sn']); ?>" />
                            <?php if(!empty($error['goods_sn'])): ?><span class="help-block"><?php echo ($error['goods_sn']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['category_id'])): ?>has-error<?php endif; ?>">
                        <label for="pid" class="col-sm-2 control-label">商品分类：</label>
                        <div class="col-sm-10">
                            <select id="category" class="form-control" name="category">
                                <option value="">--请选择分类--</option>
                                <?php if(is_array($data['categorys'])): $i = 0; $__LIST__ = $data['categorys'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$category): $mod = ($i % 2 );++$i;?><option value="<?php echo ($category['id']); ?>" data-lev="<?php echo ($category['lev']); ?>">
                                    <?php echo str_repeat('&nbsp;',($category['lev']-1)*4); echo ($category['name']); ?>
                                    </option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <?php if(!empty($error['category_id'])): ?><span class="help-block"><?php echo ($error['category_id']); ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2  col-sm-10">
                        <div id="product-category"
                             class="well well-sm"
                             style="height: 100px; overflow: auto;">
                             <?php if(is_array($data['category_id'])): $i = 0; $__LIST__ = $data['category_id'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$category_id): $mod = ($i % 2 );++$i;?><div id="product-category-<?php echo ($category_id); ?>">
                                     <span class="glyphicon glyphicon-minus" style="cursor: pointer;" aria-hidden="true"></span><?php echo ($data['category_name'][$key]); ?>
                                     <input type="hidden" name="category_id[]" value="<?php echo ($category_id); ?>"/>
                                     <input type="hidden" name="category_name[]" value="<?php echo ($data['category_name'][$key]); ?>"/>
                                     &nbsp;&nbsp;&nbsp;&nbsp;
                                     <input name="is_primary" <?php if(($data['is_primary']) == $category_id): ?>checked="true"<?php endif; ?> type="radio" value="<?php echo ($category_id); ?>" title="是否为主分类"/>
                                 </div><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['brand_id'])): ?>has-error<?php endif; ?>">
                        <label for="brand_id" class="col-sm-2 control-label">商品品牌：</label>
                        <div class="col-sm-10">
                            <select id="brand_id" class="form-control" name="brand_id">
                                <option value="0">--请选择品牌--</option>
                                <?php if(is_array($data['brands'])): $i = 0; $__LIST__ = $data['brands'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand): $mod = ($i % 2 );++$i;?><option value="<?php echo ($brand['id']); ?>" <?php if(($brand['id']) == $data['brand_id']): ?>selected="selected"<?php endif; ?>><?php echo ($brand['name']); ?>
                                    </option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <?php if(!empty($error['brand_id'])): ?><span class="help-block"><?php echo ($error['brand_id']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['suppliers_id'])): ?>has-error<?php endif; ?>">
                        <label for="suppliers_id" class="col-sm-2 control-label">供应商：</label>
                        <div class="col-sm-10">
                            <select id="suppliers_id" class="form-control" name="suppliers_id">
                                <option value="0">--请选择供应商--</option>
                                <?php if(is_array($data['suppliers'])): $i = 0; $__LIST__ = $data['suppliers'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$suppliers): $mod = ($i % 2 );++$i;?><option value="<?php echo ($suppliers['id']); ?>" <?php if(($suppliers['id']) == $data['suppliers_id']): ?>selected="selected"<?php endif; ?>><?php echo ($suppliers['name']); ?>
                                    </option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <?php if(!empty($error['brand_id'])): ?><span class="help-block"><?php echo ($error['suppliers_id']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['price'])): ?>has-error<?php endif; ?>">
                        <label for="price" class="col-sm-2 control-label">商品价格：</label>
                        <div class="col-sm-10">
                            <input type="text" id="price" class="form-control" name="price" placeholder="商品价格" value="<?php echo ($data['price']); ?>" />
                            <?php if(!empty($error['price'])): ?><span class="help-block"><?php echo ($error['price']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['give_integral'])): ?>has-error<?php endif; ?>">
                        <label for="give_integral" class="col-sm-2 control-label">赠送积分数：</label>
                        <div class="col-sm-10">
                            <input type="text" id="give_integral" class="form-control" name="give_integral" placeholder="赠送积分数" value="<?php echo ($data['give_integral']); ?>" />
                            <?php if(!empty($error['give_integral'])): ?><span class="help-block"><?php echo ($error['give_integral']); ?></span>
                            <?php else: ?>
                                <span class="help-block">购买该商品时赠送消费积分数,-1表示按商品价格赠送</span><?php endif; ?>

                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['integral'])): ?>has-error<?php endif; ?>">
                        <label for="integral" class="col-sm-2 control-label">积分购买金额：</label>
                        <div class="col-sm-10">
                            <input type="text" id="integral" class="form-control" name="integral" placeholder="积分购买金额" value="<?php echo ($data['integral']); ?>" />

                            <?php if(!empty($error['integral'])): ?><span class="help-block"><?php echo ($error['integral']); ?></span>
                                <?php else: ?>
                                <span class="help-block">购买该商品时最多可以使用多少积分,-1为不限</span><?php endif; ?>
                        </div>
                    </div>


                    <div class="form-group <?php if(!empty($error['promote_price'])): ?>has-error<?php endif; ?>">
                        <label for="promote_price" class="col-sm-2 control-label">促销价格：</label>
                        <div class="col-sm-10">
                            <input type="text" id="promote_price" class="form-control" name="promote_price" placeholder="促销价格" value="<?php echo ($data['promote_price']); ?>" />
                            <?php if(!empty($error['promote_price'])): ?><span class="help-block"><?php echo ($error['promote_price']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="promote_start_date" class="col-sm-2 control-label">促销开始时间：</label>
                        <div class="col-sm-10">
                            <input type="text" id="promote_start_date" class="form-control" name="promote_start_date" placeholder="促销开始时间" value="<?php echo ($data['promote_start_date']); ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="promote_start_date" class="col-sm-2 control-label">促销结束时间：</label>
                        <div class="col-sm-10">
                            <input type="text" id="promote_end_date" class="form-control" name="promote_end_date" placeholder="促销结束时间" value="<?php echo ($data['promote_end_date']); ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="thumb" class="col-sm-2 control-label">商品缩略图：</label>
                        <div class="col-sm-10">
                            <!-- 加载编辑器的容器 -->
                            <span class="uploadImg  thumb">
                       <textarea style="display:none;" data="thumb" id="thumb"></textarea>
                       <input type="hidden" name="thumb" value="<?php echo ($data['thumb']); ?>" />
                       <img class="thumb" src="<?php echo ($data['thumb']); ?>" width="100px" height="100px"  />
                       <a href="javascript:void(0);" class="btn btn-primary add" role="button">
                       <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加
                       </a>
                       <a href="javascript:void(0);"  class="btn btn-primary remove" role="button">
                       <span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>删除
                       </a>
                       </span>
                       <script type="text/javascript">
                        var upload_img_thumb = UE.getEditor("thumb",
                                                 { isShow:false,
                                                   serverUrl :"<?php echo U("Admin/public/ueeditor");?>",
                                                 });
                        upload_img_thumb.ready(function (){
                            upload_img_thumb.addListener("beforeInsertImage", function (t,arg){
                                 callBeforeInsertImage("thumb", arg[0]);
                            });
                        });
                       </script>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="form-group <?php if(!empty($error['sort'])): ?>has-error<?php endif; ?>">
                        <label for="sort" class="col-sm-2 control-label">排序：</label>
                        <div class="col-sm-10">
                            <input type="text" id="sort" class="form-control" name="sort"  value="<?php echo ($data['sort']); ?>"  placeholder="排序">
                            <?php if(!empty($error['sort'])): ?><span class="help-block"><?php echo ($error['sort']); ?></span><?php endif; ?>
                        </div>
                    </div>

                </div>
                <div class="tab-pane" id="description">
                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">商品详情：</label>
                        <div class="col-sm-10">
                            <!-- 加载编辑器的容器 -->
                            <textarea id="content" name="content" ><?php echo ($data['content']); ?></textarea>
                    <script type="text/javascript">
                        UE.getEditor("content",
                          { initialFrameWidth: null,
                            serverUrl :"<?php echo U("Admin/public/ueeditor");?>",
                            initialFrameHeight:400});
                    </script>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div class="tab-pane" id="attribute">
                    <div class="form-group goodsAttr <?php if(!empty($error['goods_type_id'])): ?>has-error<?php endif; ?>">
                        <label for="goods_type_id" class="col-sm-2 control-label">商品类型：</label>
                        <div class="col-sm-10">
                            <select id="goods_type_id" class="form-control" name="goods_type_id">
                                <option value="">--请选择商品类型--</option>
                                <?php if(is_array($data['goodsTypes'])): $i = 0; $__LIST__ = $data['goodsTypes'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goodsType): $mod = ($i % 2 );++$i;?><option value="<?php echo ($goodsType['id']); ?>" <?php if(($goodsType['id']) == $data['goods_type_id']): ?>selected="selected"<?php endif; ?>><?php echo ($goodsType['name']); ?>
                                    </option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>

                            <?php if(!empty($error['goods_type_id'])): ?><span class="help-block"><?php echo ($error['goods_type_id']); ?></span>
                            <?php else: ?>
                                <span class="help-block">请选择商品的所属类型，进而完善此商品的属性</span><?php endif; ?>
                        </div>
                    </div>
                    <?php echo ($data['attribute_html']); ?>
                </div>

                <div class="tab-pane" id="other">

                    <div class="form-group <?php if(!empty($error['weight'])): ?>has-error<?php endif; ?>">
                        <label for="weight" class="col-sm-2 control-label">重量：</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" id="weight" class="form-control" name="weight" placeholder="重量" value="<?php echo ($data['weight']); ?>" />
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <select class="form-control" name="weight_unit">
                                            <option value="千克" <?php if(($data['weight_unit']) == "千克"): endif; ?>>千克</option>
                                            <option value="克" <?php if(($data['weight_unit']) == "克"): endif; ?>>克</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($error['weight'])): ?><span class="help-block"><?php echo ($error['weight']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['number'])): ?>has-error<?php endif; ?>">
                        <label for="number" class="col-sm-2 control-label">库存数：</label>
                        <div class="col-sm-10">
                            <input type="text" id="number" class="form-control" name="number" placeholder="库存数" value="<?php echo ($data['number']); ?>" />
                            <?php if(!empty($error['number'])): ?><span class="help-block"><?php echo ($error['number']); ?></span>
                            <?php else: ?>
                                <span class="help-block">商品存在货品时，库存数值取决于货品数量</span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?php if(!empty($error['warn_number'])): ?>has-error<?php endif; ?>">
                        <label for="warn_number" class="col-sm-2 control-label">库存警告数：</label>
                        <div class="col-sm-10">
                            <input type="text" id="warn_number" class="form-control" name="warn_number" placeholder="库存警告数" value="<?php echo ($data['warn_number']); ?>" />
                            <?php if(!empty($error['warn_number'])): ?><span class="help-block"><?php echo ($error['warn_number']); ?></span>
                            <?php else: ?>
                                <span class="help-block"> 商品存在货品时，库存数值取决于货品数量</span><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="position" class="col-sm-2 control-label">推荐位：</label>
                        <div class="col-sm-10">
                            <?php if(is_array($data['positions'])): $i = 0; $__LIST__ = $data['positions'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($i % 2 );++$i;?><label class="postion">
                                    <input  type="checkbox"
                                    <?php if(in_array($position['id'], $data['position'])): ?>checked = "true"<?php endif; ?>
                                    value="<?php echo ($position['id']); ?>"
                                    name="position[]"/>
                                    <?php echo ($position['name']); ?>
                                </label><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="is_on_sale" class="col-sm-2 control-label">是否上架：</label>
                        <div class="col-sm-10">
                            <select id="is_on_sale" class="form-control" name="is_on_sale" >
                                <option value="1" <?php if(($data['is_on_sale']) == "1"): ?>selected="selected"<?php endif; ?>>上架</option>
                                <option value="0" <?php if(($data['is_on_sale']) == "0"): ?>selected="selected"<?php endif; ?>>下架</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="is_alone_sale" class="col-sm-2 control-label">是否普通销售商品：</label>
                        <div class="col-sm-10">
                            <select id="is_alone_sale" class="form-control" name="is_alone_sale" >
                                <option value="1" <?php if(($data['is_alone_sale']) == "1"): ?>selected="selected"<?php endif; ?>>普通销售商品</option>
                                <option value="0" <?php if(($data['is_alone_sale']) == "0"): ?>selected="selected"<?php endif; ?>>配件或赠品</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keywords" class="col-sm-2 control-label">关键字：</label>
                        <div class="col-sm-10">
                            <input type="text" id="keywords" class="form-control" name="keywords"  value="<?php echo ($data['keywords']); ?>"  placeholder="关键字">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">描述：</label>
                        <div class="col-sm-10">
                            <textarea id="description" name="description" class="form-control" rows="3"><?php echo ($data['description']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="seller_note" class="col-sm-2 control-label">商家备注：</label>
                        <div class="col-sm-10">
                            <textarea id="seller_note" name="seller_note" class="form-control" rows="3"><?php echo ($data['seller_note']); ?></textarea>
                        </div>
                    </div>

                </div>

                <div class="tab-pane" id="gallery">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover gallery_table">
                            <thead>
                            <tr>
                                <td class="text-left">图片</td>
                                <td class="text-right">标题</td>
                                <td class="text-right">排序</td>
                                <td>
                                    <textarea style="display:none;" id="images"></textarea>
                                    <script type="text/javascript">
                                        var images = UE.getEditor("images",
                                                { isShow:false,
                                                  serverUrl :"<?php echo U('Admin/public/ueeditor');?>"});
                                        images.ready(function (){
                                            images.addListener("beforeInsertImage", function (t,arg){
                                                console.log(arg);
                                                callBeforeInsertMultImage("images", arg);
                                            });
                                        });
                                    </script>
                                    <a class="btn btn-primary" onclick="addMultImage('images');">+</a>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                               <?php if(is_array($data['image'])): $i = 0; $__LIST__ = $data['image'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$image): $mod = ($i % 2 );++$i;?><tr class="imageRow">
                                       <td class="text-left">
                                           <img height="100" width="100" src="<?php echo ($image); ?>" data-sort="<?php echo ($key); ?>">
                                           <input type="hidden" value="<?php echo ($image); ?>" name="image[<?php echo ($key); ?>]" />
                                       </td>
                                       <td class="text-right">
                                           <input type="text" value="<?php echo ($data['image_title'][$key]); ?>" name="image_title[<?php echo ($key); ?>]" />
                                       </td>
                                       <td class="text-right">
                                           <input type="text" value="<?php echo ($data['image_sort'][$key]); ?>" name="image_sort[<?php echo ($key); ?>]" />
                                       </td>
                                       <td>
                                           <a class="btn btn-danger" onclick="revomeImage(this);">-</a>
                                       </td>
                                   </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <?php if(!empty($data['id'])): ?><input type="hidden" name="id" value="<?php echo ($data['id']); ?>" /><?php endif; ?>
            <?php if(!empty($redirect)): ?><input type="hidden" name="redirect" value="<?php echo ($redirect); ?>" /><?php endif; ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="提交" class="btn btn-primary">
                </div>
            </div>
        </form>
        <div class="clear"></div>
    </div>

</div>
<script>
    $(function(){
        //商品分类
        $("select[name='category']").change(function(){
            var category_showname = "";
            var category_id = $("select[name='category']").val();
            if(category_id==""){
                return false;
            }
            var selectOption = $("select[name='category']").find("option[value='"+category_id+"']");
            var category_name = new Array();
            category_name.unshift(selectOption.html().replace(/&nbsp;/g,""));
            var lev = selectOption.attr("data-lev");
            if(lev > 1) {
                for(i=lev-1; i >=1; i--){
                    category_name.unshift(selectOption.prevAll("[data-lev='"+i+"']").first().html().replace(/&nbsp;/g,""));
                }
            }
            for(i=0; i<category_name.length; i++) {
                if(i<category_name.length-1){
                    category_showname = category_showname + category_name[i] + " > ";
                } else {
                    category_showname = category_showname + category_name[i];
                }
            }
            $("#product-category #product-category-"+category_id).remove();
            $('#product-category').append('<div id="product-category-' + category_id + '"><span class="glyphicon glyphicon-minus" style="cursor: pointer;" aria-hidden="true"></span> ' + category_showname+ '<input type="hidden" name="category_id[]" value="' + category_id + '" /><input type="hidden" name="category_name[]" value="'+category_showname+'"/>&nbsp;&nbsp;&nbsp;&nbsp;<input name="is_primary" type="radio" value="'+ category_id + '" title="是否为主分类"/></div>');

        });

        $("body").on("click","#product-category .glyphicon-minus", function(){
            $(this).parent().remove();
        });
        //日期
        setDateRange($("#promote_start_date"),$("#promote_end_date"));

        $("select[name='goods_type_id']").val("<?php echo ($data['goods_type_id']); ?>");
        $("select[name='goods_type_id']").change(function(){
            $(this).parents(".goodsAttr").nextAll().remove();
            if($(this).val() == ''){

            } else {
                var goodsId = $("input[name='id']").size()>0?$("input[name='id']").val():'';
                var _this = $(this);
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('Goods/ajax_get_attr');?>",
                    data: 'goodsId='+goodsId+'&goodsTypeId='+$(this).val(),
                    dataType: 'html',
                    success: function (html) {
                        _this.parents(".goodsAttr").after(html);
                    }
                });
            }
        });

    });
</script>


</div>
<div id="footer">
    <p>感谢使用雨良商务平台</p>
</div>



</body>

</html>