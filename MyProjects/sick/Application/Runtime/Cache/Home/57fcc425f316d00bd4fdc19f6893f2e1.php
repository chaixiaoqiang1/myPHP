<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>心意墙</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/newdoctor/css/Mind_wall.css"/>

</head>

<body>
<div class="container">
    <div class="row">
        <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 col-sm-12 col-xs-12 mr">
            <div class="user_img lf">
                <img src="<?php echo ($vo['s_id']['icon']); ?>" class="img-responsive img-circle" alt=""/>

                <div class="money">
                    <span class="money_number"><?php echo ($vo["price"]); ?>元</span>
                </div>
            </div>
            <div class="criticism lf fl">
                <div class="user_name"><?php echo ($vo['s_id']['username']); ?></div>
                <div class="info"><?php echo ($vo["content"]); ?></div>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <button type="button" class="btn btn-warning btn-lg btn-block">心意总收入
        <?php if($price == ''): ?>0
            <?php else: ?>
            <?php echo ($price); endif; ?>
        元
    </button>
    <!--<div class="navbar navbar-fixed-bottom">
        <div style="color: #ffffff;font-size: 16px;text-align:center;line-height: 50px">心意总共收入 <span style="color:#F93446;"><?php echo ($price); ?></span> 元</div>
    </div>-->
</div>

<script src="/public_html/Public/sick/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/sick/js/bootstrap.js"></script>

</body>

</html>