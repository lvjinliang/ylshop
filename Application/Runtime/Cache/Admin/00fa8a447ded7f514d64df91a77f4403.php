<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
<head>
   <title>雨良商务系统</title>
   <meta name="viewport" content="width=device-width, 
                                     initial-scale=1.0, 
                                     maximum-scale=1.0, 
                                     user-scalable=no">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="">
	<meta name="keywords" content="">
    <link href="/Public/Admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="/Public/Admin/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="box">
        <div class="login-box">
            <div class="login-title text-center">
                <h1><small>雨良商务系统</small></h1>
            </div>
            <div class="login-content ">
                <div class="form">
                    <form action="" method="post">
                        <div class="form-group <?php if(!empty($data['error']['name'])): ?>has-error<?php endif; ?>">
                            <div class="col-xs-12  ">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="用户名" value="<?php echo ($data['name']); ?>" />
                                </div>
                                <?php if(!empty($data['error']['name'])): ?><span class="help-block"><?php echo ($data['error']['name']); ?></span><?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group <?php if(!empty($data['error']['password'])): ?>has-error<?php endif; ?>">
                            <div class="col-xs-12  ">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="密码" value="<?php echo ($data['password']); ?>">
                                </div>
                                <?php if(!empty($data['error']['password'])): ?><span class="help-block"><?php echo ($data['error']['password']); ?></span><?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group form-actions">
                            <div class="col-xs-4 col-xs-offset-4 ">
                                <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-off"></span> 登录</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
<!--[if lt IE 9]>
<script src="/Public/Admin/js/ie8-responsive-file-warning.js"></script>
<![endif]-->
<script src="/Public/Admin/js/ie-emulation-modes-warning.js"></script>
<!--[if lt IE 9]>
<script src="/Public/Admin/js/html5shiv.min.js"></script>
<script src="/Public/Admin/js/respond.min.js"></script>
<![endif]-->
<script src="/Public/Admin/js/jquery-1.9.1.min.js"></script>
<script src="/Public/Admin/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/Public/Admin/js/ie10-viewport-bug-workaround.js"></script>
</html>