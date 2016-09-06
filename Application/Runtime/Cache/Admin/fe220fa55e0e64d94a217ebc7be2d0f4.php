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

    <div class="main-title"><h2>控制面板</h2></div>


    <div class="admin_list" >
        <div> 选择平台：
        <select id="select_type" autocomplete="off">
            <option value="WEB">WEB</option>
            <option value="WAP">WAP</option>
            <option value="APP">APP</option>
        </select>
        </div>
        <div id="visit-count" style="height:400px;">
        </div>

        <div class="table-responsive">
            <h4>订单统计</h4>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>日期</th>
                    <th>总订单数</th>
                    <th>总订单额</th>
                    <th>支付单数</th>
                    <th>支付额</th>
                    <th>取消单数</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($data['order'])): if(is_array($data['order'])): $i = 0; $__LIST__ = $data['order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($list['setting_date']); ?></td>
                            <td><?php echo ($list['all_rows']); ?></td>
                            <td><?php echo ($list['all_total']); ?></td>
                            <td><?php echo ($list['pay_rows']); ?></td>
                            <td><?php echo ($list['pay_total']); ?></td>
                            <td><?php echo ($list['back_rows']); ?></td>

                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php else: ?>
                    <tr><td colspan="10" style="color:red; text-align: center;">暂时无数据</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <h4>用户统计</h4>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>日期</th>
                    <th>注册数</th>
                    <th>激活数</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($data['account'])): if(is_array($data['account'])): $i = 0; $__LIST__ = $data['account'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($list['setting_date']); ?></td>
                            <td><?php echo ($list['all_rows']); ?></td>
                            <td><?php echo ($list['all_rows']); ?></td>

                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php else: ?>
                    <tr><td colspan="10" style="color:red; text-align: center;">暂时无数据</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>
<script src="/Public/Admin/js/echarts.min.js"></script>
<script type="application/javascript">
    var visitChart = echarts.init(document.getElementById('visit-count'));
    visitChart.showLoading();
    $.get("<?php echo U('index/ajax_get_count_visit');?>").done(function (data) {
        set_visit_count(data);
    });

    $("#select_type").change(function () {
        var type = $(this).val();
        $.get("<?php echo U('index/ajax_get_count_visit');?>/type/"+type).done(function (data) {
            console.log(data);
            set_visit_count(data);
        });
    });

    function set_visit_count (data) {
        visitChart.hideLoading();
        var option = {};
        option.title  = {
            left: 'center',
            text:'访问统计'
        };
        option.toolbox = {
            show: true,
            feature: {
                dataZoom: {
                    yAxisIndex: 'none'
                },
                dataView: {readOnly: false},
                magicType: {type: ['line', 'bar']},
                restore: {},
                saveAsImage: {}

            }
        };
        option.tooltip =  {};
        option.legend = {
            left: 'left',
            data:['pv', 'uv', 'ipv']
        };
        option.xAxis = {
            data: data.date
        };
        option.yAxis = {};
        option.series = [
            {
                'name' : 'pv',
                'type': 'line',
                'data': data.pv
            },
            {
                'name' : 'uv',
                'type': 'line',
                'data': data.uv
            },
            {
                'name' : 'ipv',
                'type': 'line',
                'data': data.ipv
            }
        ];
        visitChart.setOption(option);
    }

</script>

</div>
<div id="footer">
    <p>感谢使用雨良商务平台</p>
</div>



</body>

</html>