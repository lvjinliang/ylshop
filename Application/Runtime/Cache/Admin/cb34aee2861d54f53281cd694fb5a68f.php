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
                    <li><a href="<?php echo U('Home/Index/index');?>" target="_blank">商城首页</a></li>
                    <li><a href="<?php echo U('Admin/logout/index');?>" >退出</a></li>

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

</div>
<div id="main">
    <?php echo W('Common/leftMenu');?>
    <div id="main-content">
    <div class="main-title"><h2>站点统计</h2></div>
    <div class="main-header">
        <div class="row">
        <div class="col-sm-4">
            <ol class="breadcrumb">
                <?php if(is_array($breadcrumbs)): $i = 0; $__LIST__ = $breadcrumbs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$breadcrumb): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($breadcrumb['href']); ?>"><?php echo ($breadcrumb['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ol>
        </div>
        <div class="col-sm-8">
            <div class="btn-group pull-right form-botton" role="group">


                <a href="javascript:void(0)" onclick="download_excel('<?php echo ($downloadUrl); ?>');" class="btn btn-primary" role="button">
                    <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"> </span>导出
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
                    <label for="start_date" class="control-label">开始日期：</label>
                    <input type="text" class="form-control" id="start_date"
                           name="start_date"
                           value="<?php echo ($search['start_date']); ?>"
                           placeholder="开始日期" />
                </div>
                <div class="form-group">
                    <label for="end_date" class="control-label">结束日期：</label>
                    <input type="text" class="form-control" id="end_date"
                           name="end_date"
                           value="<?php echo ($search['end_date']); ?>"
                           placeholder="结束日期" />
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
                    <th>时间</th>
                    <th>平台</th>
                    <th>PV</th>
                    <th>UV</th>
                    <th>IPV</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($lists)): if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                    <td><?php echo ($list['date']); ?></td>
                    <td><?php echo ($list['type']); ?></td>
                    <td><?php echo ($list['pv']); ?></td>
                    <td><?php echo ($list['uv']); ?></td>
                    <td><?php echo ($list['ipv']); ?></td>

                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <?php else: ?>
                     <tr><td colspan="10" style="color:red; text-align: center;">暂时无数据</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo ($show); ?>
    </div>

</div>

<div id="dialog" title="">
    <p></p>
</div>
<script language="javascript" type="text/javascript">
    setDateRange($("#start_date"),$("#end_date"));
</script>

</div>
<div id="footer">
    <p>感谢使用雨良商务平台</p>
</div>



</body>

</html>