<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>我的收入</title>

    <link href="/public_html/Public/sick/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/newdoctor/css/earning.css"/>
</head>

<body>
<div class="container">
    <div class="row parent">
        <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 col-sm-12 col-xs-12 br">

            <div class="user_img lf">
                <img src="../img/timg.jpg" class="img-responsive img-circle" alt=""/>
            </div>
            <div class="user_info lf fl">
                <div class="user_time fl">
                    <div class="user_name lf"><?php echo ($vo["username"]); ?></div>
                    <div class="user_name rt"><?php echo (date("y-m-d",$vo["servetime"])); ?></div>
                </div>
                <div class="user_money fl">
                    <div class="lf  cost">
                        <?php if($vo['type'] == 1): ?>图文<?php else: ?>预约<?php endif; ?>咨询费用<span class="cl"><?php echo ($vo["price"]); ?></span>元
                    </div>
                 <!--   <div class="rt intention">
                        心意<span class="cl">20</span>元
                    </div>-->
                </div>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div class="navbar navbar-fixed-bottom">
        <div style="color: #ffffff;font-size: 16px;text-align:center;line-height: 50px">总共收入 <span
                style="color:#F93446;">
            <?php if($price != ''): echo ($price); ?>
                <?php else: ?>
                0<?php endif; ?>
        </span> 元
        </div>
    </div>
</div>
<script src="/public_html/Public/sick/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/sick/js/bootstrap.js"></script>
<script>
    $(".parent").children().last().css("margin-bottom","50px");
</script>
</body>

</html>