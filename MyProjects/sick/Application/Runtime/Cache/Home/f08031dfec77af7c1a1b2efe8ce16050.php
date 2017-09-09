<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>服务历史</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/Chat_record.css"/>
</head>

<body>
<div class="container">

    <div class="row top">
        <a href="<?php echo U('Index/Chat_record');?>">
            <div class="col-md-6 col-sm-6 col-xs-6 left" align="center" style="color: #9a9a9a;">图文咨询历史</div>
        </a>
        <a href="">
            <div class="col-md-6 col-sm-6 col-xs-6 right cl" align="center">预约咨询历史</div>

        </a>
    </div>
    <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="row">
        <a href="<?php echo U('Index/chat',array('fid'=> $vo['id']));?>">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="content">
                    <div class="info fl">
                        <div class="doctor lf"><?php echo ($vo["office"]); ?></div>
                        <div class="names lf"><?php echo ($vo["username"]); ?></div>
                        <div class="judge lf">预约咨询</div>
                    </div>
                    <div class="evaluate_tx">
                        <div class="evaluate_content">
                            <?php echo ($vo["id"]); ?>
                        </div>
                        <div class="time"><?php echo ($vo["addtime"]); ?></div>
                    </div>
                </div>

            </div>
        </a>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>

</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

</body>

</html>