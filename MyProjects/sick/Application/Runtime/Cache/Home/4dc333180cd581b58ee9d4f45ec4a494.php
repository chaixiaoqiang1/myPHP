<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>服务历史</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/service_history.css"/>
</head>

<body>
<div class="container">
    <!-- 顶部导航-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 top_nav">
            <ul class="fl top_nav_content">
                <a href="current_service.html">
                    <li class="lf  ">当前服务</li>
                </a>
                <a href="my_doctor.html">
                    <li class="lf ">我的医生</li>
                </a>
                <a href="#">
                    <li class="lf current">服务历史</li>
                </a>
            </ul>
        </div>
    </div>
    <!-- 服务历史-->
    <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['type'] == 1): ?><div class="row bj">
        <div class="col-md-12 col-sm-12 col-xs-12 img_txt_content">
            <div class="img_txt lf">
                <img src="/public_html/Public/doctor/img/images-text.png" class="img-responsive" alt=""/>
            </div>
            <div class="txt_content lf">图文咨询</div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 content_tx">
            <span class="department"><?php echo ($vo["office"]); ?></span>|<span class="classfiy">图文咨询</span>|<span class="doctor_names"><?php echo ($vo["username"]); ?></span>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="time fl">
                <div class="time_number lf"><?php echo (date("y-m-d",$vo["servetime"])); ?></div>
                    <div class="evaluate rt">已完成</div>
            </div>
         <!--   <a href="<?php echo U('Index/Third_party_opinions', array('fid'=> $vo['id']));?>">
                <div class="look_over">查看第三方意见...</div>
            </a>-->
        </div>
    </div>
            <?php elseif($vo['type'] == 2): ?>
    <div class="row bj">
        <div class="col-md-12 col-sm-12 col-xs-12 img_txt_content">
            <div class="img_txt lf">
                <img src="/public_html/Public/doctor/img/images-text.png" class="img-responsive" alt=""/>
            </div>
            <div class="txt_content lf">预约就诊</div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 content_tx">
            <span class="department"><?php echo ($vo["office"]); ?></span>|<span class="classfiy">预约就诊</span>|<span class="doctor_names"><?php echo ($vo["username"]); ?></span>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="time fl">
                <div class="time_number lf"><?php echo (date("y-m-d",$vo["servetime"])); ?></div>
                    <div class="to_evaluate rt">已评价</div>
            </div>
            <!--<a href="<?php echo U('Index/Third_party_opinions', array('fid'=> $vo['id']));?>">
                <div class="look_over">查看第三方意见...</div>
            </a>-->

        </div>
    </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>


</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>

</body>

</html>