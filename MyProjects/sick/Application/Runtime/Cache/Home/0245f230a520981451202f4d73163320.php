<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>问题详情</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/chat.css"/>


</head>

<body>
<div class="container">
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['reply_type'] == 1): ?><div class="user fl">
                <div class="user_img lf">
                    <img src="<?php echo ($vo["s_id"]); ?>" class="img-responsive img-circle" alt="" />
                </div>
                <?php if(substr($vo['content'],-3) == 'jpg'): ?><div class="user_tx lf" >
                        <img src="/public_html/<?php echo ($vo["content"]); ?>" style="width: 100px;height: 100px;">
                    </div>
                    <?php else: ?>
                    <div class="user_tx lf">
                         <?php echo ($vo["content"]); ?>
                    </div><?php endif; ?>
            </div>
                    <?php else: ?>
            <div class="doctor fl">
                <div class="user_img rt" style="padding-bottom: 10px">
                    <img src="/public_html/Uploads/<?php echo ($vo["d_id"]); ?>" class="img-responsive img-circle" alt="" style="height: 50px;"/>
                </div>

                <?php if(substr($vo['content'],-3) == 'jpg'): ?><div class="user_tx rt" style="background: #ffffff">
                         <img src="/public_html/<?php echo ($vo["content"]); ?>" style="width: 100px;height: 100px;">
                    </div>
                    <?php else: ?>
                    <div class="user_tx rt" style="background: #ffffff">
                    <?php echo ($vo["content"]); ?>
                    </div><?php endif; ?>



            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>
</body>

</html>