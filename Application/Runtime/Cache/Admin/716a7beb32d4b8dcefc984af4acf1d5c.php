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
    <div class="main-title"><h2>商品列表</h2></div>
    <div class="main-header">
        <div class="row">
        <div class="col-sm-4">
            <ol class="breadcrumb">
                <?php if(is_array($breadcrumbs)): $i = 0; $__LIST__ = $breadcrumbs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$breadcrumb): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($breadcrumb['href']); ?>"><?php echo ($breadcrumb['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ol>
        </div>
        <div class="col-sm-8">
            <div class="btn-group pull-right form-botton" role="group">
                <a href="<?php echo U('goods/add');?>" class="btn btn-primary" role="button">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加
                </a>

                <a href="javascript:void(0)" onclick="deleteSelect('<?php echo U('goods/delete');?>','id','确认将所选项移入回收站')" class="btn btn-primary" role="button">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>回收站
                </a>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    </div>

    <?php if(!empty($error)): ?><div id="error" class="alert  alert-danger">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>失败！</strong><?php echo ($error); ?>
        </div><?php endif; ?>
    <?php if(!empty($success)): ?><div id="success" class="alert  alert-success">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>成功！</strong><?php echo ($success); ?>
        </div><?php endif; ?>

    <div class="admin_list" >
        <div class="search-bar">
            <form class="form-inline" role="form" method="get" action="<?php echo U(ACTION_NAME);?>">
                <div class="form-group">
                    <label for="name" class="control-label">商品名：</label>
                    <input type="text" class="form-control" id="name"
                           name="name" value="<?php echo ($search['name']); ?>" placeholder="商品名" />
                </div>
                <div class="form-group">
                    <label for="category_id" class="control-label">主分类：</label>
                    <select id="category_id" class="form-control" name="category_id">
                        <option value="">--请选择分类--</option>
                        <?php if(is_array($data['categorys'])): $i = 0; $__LIST__ = $data['categorys'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$category): $mod = ($i % 2 );++$i;?><option <?php if($category['id'] == $search['category_id']): ?>selected="true"<?php endif; ?> value="<?php echo ($category['id']); ?>">
                                <?php echo str_repeat('&nbsp;',($category['lev']-1)*4); echo ($category['name']); ?>
                            </option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brand_id" class="control-label">品牌：</label>
                    <select id="brand_id" class="form-control" name="brand_id">
                        <option value="0">--请选择商品品牌--</option>
                        <?php if(is_array($data['brands'])): $i = 0; $__LIST__ = $data['brands'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand): $mod = ($i % 2 );++$i;?><option value="<?php echo ($brand['id']); ?>" <?php if(($search['brand_id']) == $brand['id']): ?>selected="selected"<?php endif; ?>><?php echo ($brand['name']); ?>
                            </option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="position_id" class="control-label">推荐位：</label>
                    <select id="position_id" class="form-control" name="position_id">
                        <option value="0">--请选择推荐位--</option>
                        <?php if(is_array($data['positions'])): $i = 0; $__LIST__ = $data['positions'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$position): $mod = ($i % 2 );++$i;?><option value="<?php echo ($position['id']); ?>" <?php if(($search['position_id']) == $position['id']): ?>selected="selected"<?php endif; ?>><?php echo ($position['name']); ?>
                            </option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>



                <div class="form-group">
                    <button type="submit" class="btn btn-primary">筛选</button>

                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th><input type="checkbox" name="checkall" value="1"> 全选</th>
                    <th>ID</th>
                    <th>商品名</th>
                    <th>货号</th>
                    <th>主分类</th>
                    <th>品牌名</th>
                    <th>价格</th>
                    <th>上架</th>
                    <th>推荐位</th>
                    <th>排序</th>
                    <th>库存</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($lists)): if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                    <td><input name="check" type="checkbox" value="<?php echo ($list['id']); ?>"></td>
                    <td><?php echo ($list['id']); ?></td>
                    <td><?php echo ($list['name']); ?></td>
                    <td><?php echo ($list['goods_sn']); ?></td>
                    <td><?php echo ($list['category_name']); ?></td>
                    <td><?php echo ($list['brand_name']); ?></td>
                    <td><?php echo ($list['price']); ?></td>
                    <td><?php echo isOnSaleToText($list['is_on_sale']);?></td>
                    <td><?php echo ($list['position_name']); ?></td>
                    <td><?php echo ($list['sort']); ?></td>
                    <td><?php echo ($list['number']); ?></td>
                    <td>
                        <a href="<?php echo U('goods/update', array('id'=>$list['id']));?>" class="btn btn-primary" role="button" title="编辑">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>
                        </a>
                        <a href="javascript:void(0);" onclick="del('<?php echo U('goods/delete');?>','<?php echo ($list['id']); ?>','id', '确认将此项移入回收站')"  class="btn btn-primary" role="button" title="回收站">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>
                        </a>
                        <?php if(($list['isAttrPrice']) == "true"): ?><a href="<?php echo U('goods/product_list', array('id'=>$list['id']));?>" class="btn btn-primary" role="button" title="编辑">
                                货品列表
                            </a><?php endif; ?>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <?php else: ?>
                     <tr><td colspan="12" style="color:red; text-align: center;">暂时无数据</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo ($show); ?>
    </div>

</div>

<div id="dialog" title="">
    <p></p>
</div>

</div>
<div id="footer">
    <p>感谢使用雨良商务平台</p>
</div>



</body>

</html>