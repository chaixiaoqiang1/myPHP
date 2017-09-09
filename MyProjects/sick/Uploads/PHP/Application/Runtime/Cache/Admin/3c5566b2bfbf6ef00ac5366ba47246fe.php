<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>后台管理系统</title>
    <link href="/taiyou/Public/style/bootstrap.min.css" rel="stylesheet">
    <link href="/taiyou/Public/style/admin.min.css" rel="stylesheet">
    <style>
        #login {

            background: -webkit-linear-gradient(#ffffff,#F7F7F7 );
            background: -o-linear-gradient(#ffffff,#F7F7F7 );
            background: -moz-linear-gradient( #ffffff,#F7F7F7);
            background: linear-gradient(#ffffff,#F7F7F7 );
        }
    </style>
</head>
<body class="login_bg">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div class="col-xs-2 col-sm-2 col-md-4  col-lg-4"></div>
        <div class="col-xs-8 col-sm-8 col-md-4  col-lg-4" id="login" style="border: 1px solid #e3e3e3;margin-top: 10%;">
            <div class="row" style="margin-top: 10%;">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                    <p>
                       <h2>后台管理</h2> 
                    </p>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-xs-offset-2 col-xs-8 col-sm-offset-2 col-sm-8 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2" style="" >
                    <form class="m-t" role="form" action="<?php echo U('Public/login');?>" method="post">
                        <div class="form-group">
                          <p>用户名:&nbsp;&nbsp;<input name="username" type="text" style="width: 80%; height: 32px; border-radius: 5px; background-color: #ffffff;" placeholder="请输入用户名" required=""></p>
                        </div>
                        <div class="form-group">
                            <p>密&nbsp;&nbsp;&nbsp;码:&nbsp;&nbsp;<input name="password" type="password" style="width: 80%; height: 32px; border-radius: 5px; background-color: #ffffff;" placeholder="请输入密码" required="">
                            </p>
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 92%; margin-bottom: 80px;">登录</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-4  col-lg-4"></div>
    </div>


</body>
</html>