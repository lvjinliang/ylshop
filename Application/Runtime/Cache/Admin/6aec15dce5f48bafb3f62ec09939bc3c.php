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
        <h2><?php if(empty($data['id'])): ?>添加<?php else: ?>编辑<?php endif; ?>支付方式</h2>
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
            <div class="form-group <?php if(!empty($error['name'])): ?>has-error<?php endif; ?>">
                <label for="name" class="col-sm-2 control-label">名称：</label>
                <div class="col-sm-10">
                    <input type="text" id="name" class="form-control" name="name" placeholder="名称" value="<?php echo ($data['name']); ?>" />
                    <?php if(!empty($error['name'])): ?><span class="help-block"><?php echo ($error['name']); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-group <?php if(!empty($error['code'])): ?>has-error<?php endif; ?>">
                <label for="code" class="col-sm-2 control-label">code：</label>
                <div class="col-sm-10">
                    <input type="text" id="code" class="form-control" name="code" placeholder="code" value="<?php echo ($data['code']); ?>" />
                    <?php if(!empty($error['code'])): ?><span class="help-block"><?php echo ($error['code']); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">email：</label>
                <div class="col-sm-10">
                    <input type="text" id="email" class="form-control" name="email" placeholder="email" value="<?php echo ($data['email']); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="key" class="col-sm-2 control-label">key：</label>
                <div class="col-sm-10">
                    <input type="text" id="key" class="form-control" name="key" placeholder="key" value="<?php echo ($data['key']); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="partner" class="col-sm-2 control-label">partner：</label>
                <div class="col-sm-10">
                    <input type="text" id="partner" class="form-control" name="partner" placeholder="partner" value="<?php echo ($data['partner']); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="logo" class="col-sm-2 control-label">logo</label>
                <div class="col-sm-10">
                    <!-- 加载编辑器的容器 -->
                    <span class="uploadImg  logo">
                       <textarea style="display:none;" data="logo" id="logo"></textarea>
                       <input type="hidden" name="logo" value="<?php echo ($data['logo']); ?>" />
                       <img class="logo" src="<?php echo ($data['logo']); ?>" width="100px" height="100px"  />
                       <a href="javascript:void(0);" class="btn btn-primary add" role="button">
                       <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加
                       </a>
                       <a href="javascript:void(0);"  class="btn btn-primary remove" role="button">
                       <span class="glyphicon glyphicon-trash" aria-hidden="true"> </span>删除
                       </a>
                       </span>
                       <script type="text/javascript">
                        var upload_img_logo = UE.getEditor("logo",
                                                 { isShow:false,
                                                   serverUrl :"<?php echo U("Admin/public/ueeditor");?>",
                                                 });
                        upload_img_logo.ready(function (){
                            upload_img_logo.addListener("beforeInsertImage", function (t,arg){
                                 callBeforeInsertImage("logo", arg[0]);
                            });
                        });
                       </script>
                </div>
                <div class="clear"></div>
            </div>

            <div class="form-group">
                <label for="description" class="col-sm-2 control-label">描述：</label>
                <div class="col-sm-10">
                    <textarea id="description" name="description" class="form-control" rows="3"><?php echo ($data['description']); ?></textarea>
                </div>
            </div>


            <div class="form-group <?php if(!empty($error['sort'])): ?>has-error<?php endif; ?>">
                <label for="sort" class="col-sm-2 control-label">排序：</label>
                <div class="col-sm-10">
                    <input type="text" id="sort" class="form-control" name="sort"  value="<?php echo ($data['sort']); ?>"  placeholder="排序">
                    <?php if(!empty($error['sort'])): ?><span class="help-block"><?php echo ($error['sort']); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="col-sm-2 control-label">状态：</label>
                <div class="col-sm-10">
                    <select id="status" class="form-control" name="status" >
                        <option value="1" <?php if(($data['status']) == "1"): ?>selected="selected"<?php endif; ?>>开启</option>
                        <option value="0" <?php if(($data['status']) == "0"): ?>selected="selected"<?php endif; ?>>关闭</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="is_online" class="col-sm-2 control-label">在线支付：</label>
                <div class="col-sm-10">
                    <select id="is_online" class="form-control" name="is_online" >
                        <option value="1" <?php if(($data['is_online']) == "1"): ?>selected="selected"<?php endif; ?>>是</option>
                        <option value="0" <?php if(($data['is_online']) == "0"): ?>selected="selected"<?php endif; ?>>否</option>
                    </select>
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


</div>
<div id="footer">
    <p>感谢使用雨良商务平台</p>
</div>



</body>

</html>