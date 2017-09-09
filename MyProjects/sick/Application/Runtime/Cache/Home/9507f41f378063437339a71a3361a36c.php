<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>当前服务</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/service_history.css"/>
</head>

<body>
<div class="container">
    <!-- 顶部导航-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 top_nav">
            <ul class="fl top_nav_content">
                <a href="<?php echo U('Index/current_service');?>">
                    <li class="lf current ">当前服务</li>
                </a>
                <a href="<?php echo U('Index/my_doctor');?>">
                    <li class="lf ">我的医生</li>
                </a>
                <a href="<?php echo U('Index/service_history');?>">
                    <li class="lf ">服务历史</li>
                </a>
            </ul>
        </div>
    </div>
    <!-- 服务历史-->
    <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="row bj">
        <div class="col-md-12 col-sm-12 col-xs-12 img_txt_content">
            <div class="img_txt lf">
                <img src="/public_html/Public/doctor/img/images-text.png" class="img-responsive" alt=""/>
            </div>
            <div class="txt_content lf">
                <?php if($vo['type'] == 1): ?>图文咨询
                    <?php elseif($vo['type'] == 2): ?>
                    预约咨询<?php elseif($vo['type'] == 3): ?>
                    免费咨询<?php endif; ?></div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 content_tx">
            <span class="department"><?php echo ($vo["office"]); ?></span>|<span class="classfiy"><?php if($vo['type'] == 1): ?>图文咨询<?php elseif($vo['type'] == 2): ?>预约咨询<?php elseif($vo['type'] == 3): ?>免费咨询<?php endif; ?></span>|<span class="doctor_names"><?php echo ($vo["username"]); ?></span>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="time fl">
                <div class="time_number lf"><?php echo (date($vo["servetime"],"y-m-d")); ?></div>
                <?php if($vo['is_pay'] == 1): ?><a href="<?php echo U('Index/pay',array('id'=> $vo['id']));?>">
                        <div class="evaluate rt" style="background: green none repeat scroll 0% 0%; margin-right: 5px;">
                            未支付
                        </div>
                    </a>
                <?php elseif($vo['is_status'] == 1): ?>
                    <?php if($vo['u_reply_status'] == 1): ?><a href="<?php echo U('Index/setchat',array('fid'=> $vo['id'],'did'=> $vo['d_id'],'guan'=> '1'));?>">
                            <div class="evaluate rt" style="margin-right: 5px;">聊天关闭</div>
                        </a>
                        <?php else: ?>
                        <a href="<?php echo U('Index/setchat',array('fid'=> $vo['id'],'did'=> $vo['d_id'],'guan'=> '2'));?>">
                            <div class="evaluate rt" style="margin-right: 5px;">开启聊天</div>
                        </a><?php endif; ?>
                    <a href="<?php echo U('Index/setchat',array('fid'=> $vo['id'],'did'=> $vo['d_id'],'guan'=> '3'));?>">
                        <div class="evaluate rt" style="margin-right: 5px;">关闭服务</div>
                    </a>
                <?php elseif($vo['is_status'] == 2): ?>
                    <a href="<?php echo U('Index/evaluate',array('fid'=> $vo['id'],'did'=> $vo['d_id']));?>">
                        <div class="evaluate rt" style="margin-right: 5px;">待评价</div>
                    </a><?php endif; ?>

            </div>
           <!-- <a href="<?php echo U('Index/Third_party_opinions', array('fid'=> $vo['id']));?>">
                <div class="look_over">查看第三方意见...</div>
            </a>-->
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>


</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>

</body>

</html>