<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>我的关注</title>

    <link href="/public_html/Public/sick/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/sick/css/doctor_follower.css"/>

</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 ">
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="follower_content fl">
                <div class="doctor_img lf">
                    <img src="<?php echo ($vo["icon"]); ?>" class="img-responsive img-circle" alt=""/>
                </div>
                <div class="doctor_names lf">
                    <?php echo ($vo["username"]); ?>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>
<script src="/public_html/Public/sick/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/sick/js/bootstrap.js"></script>

</body>

</html>