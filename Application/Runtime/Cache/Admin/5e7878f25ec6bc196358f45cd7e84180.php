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
        <h2>生成搜索词</h2>
    </div>
    <div class="main-header">
        <div class="row">
            <div class="col-sm-8">
                <ol class="breadcrumb">
                    <?php if(is_array($breadcrumbs)): $i = 0; $__LIST__ = $breadcrumbs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$breadcrumb): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($breadcrumb['href']); ?>"><?php echo ($breadcrumb['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ol>
            </div>
            <div class="col-sm-4">

            </div>
            <div class="clear"></div>
        </div>
    </div>


    <div class="admin_form">
        <div class="form-group">
            <div class="col-sm-12">
                <textarea id="content" name="content" class="form-control" rows="8"></textarea>
            </div>
        </div>
        <div class="clear"></div>
        <div style="margin: 10px;"></div>
        <div class="form-group">
            <div class="col-sm-12">
                <input type="submit" data="<?php echo U('search/ajax_create_keys');?>" value="生成" class="btn btn-primary create">
            </div>
        </div>
        <div class="clear"></div>
    </div>

</div>
<script>
    $(function(){
         $("body").on("click", "input.create", function (){
             var url = $(this).attr("data");
             createKeys(url,1);
         });

    });

    function createKeys(url, $key) {
        $.ajax({
            url : url+'/p/'+$key,
            type : 'GET',
            dateType : 'json',
            success : function (json){
                if (json.success==1) {
                    $("#content").append(json.msg);
                    createKeys(url,json.p);
                } else {
                    $("#content").append(json.msg);
                }

            }
        });
    }
</script>


</div>
<div id="footer">
    <p>感谢使用雨良商务平台</p>
</div>



</body>

</html>