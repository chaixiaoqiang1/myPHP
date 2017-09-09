<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>我的医生</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/my_doctor.css"/>
    <style>
        .purpose {
            border: 1px solid #5dc64c;
            border-radius: 5px;
            color: #f93446;
            margin-right: 10px;
            padding: 2px 10px;
        }

        .rt {
            float: right;
        }
    </style>

</head>

<body>
<div class="container">
    <!-- 顶部导航-->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 top_nav">
            <ul class="fl top_nav_content">
                <a href="<?php echo U('Index/current_service');?>">
                    <li class="lf">当前服务</li>
                </a>
                <a href="<?php echo U('Index/my_doctor');?>">
                    <li class="lf current">我的医生</li>
                </a>
                <a href="<?php echo U('Index/service_history');?>">
                    <li class="lf">服务历史</li>
                </a>
            </ul>
        </div>
    </div>
    <!-- -->
    <div class="loop_page">
        <?php if(is_array($doctordata)): $i = 0; $__LIST__ = $doctordata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="doctor_page">
                <div class="row fl doctor_info">
                    <a href="<?php echo U('Index/doctor_detail',array('id'=> $vo['id']));?>">
                        <div class="col-md-3 col-sm-3 col-xs-3 doctor_info_content">
                            <div class="doctor_img">
                                <img src="/public_html/Uploads/<?php echo ($vo["image"]); ?>" class="img-responsive img-circle"
                                     style="width: 55px;height: 55px;" alt=""/>
                            </div>
                        </div>
                        <div class="col-md-9 col-sm-9 col-xs-9 clear_padding">
                            <div class="info fl">
                                <div class="top fl">
                                    <div class="lf doctor_name"><?php echo ($vo["username"]); ?></div>
                                    <div class="lf state">
                                        <div class="state_tx lf">
                                            <?php if($vo['is_online'] == 1): ?>在线
                                                <?php else: ?>
                                                离线<?php endif; ?>
                                        </div>
                                        <img src="/public_html/Public/doctor/img/chenggong.png"
                                             class="img-responsive img-circle state_img lf" alt=""/>
                                    </div>

                                    <a href="<?php echo U('Newuser/Send_the_mind',array('id'=>$vo['id']));?>">
                                        <div class="purpose rt">送心意</div>
                                    </a>
                                </div>
                                <div class="content fl">
                                    <div class="classfiy">
                                        <?php if($vo['rank'] == 1): ?>初级医生
                                            <?php elseif($vo['rank'] == 2): ?>
                                            中级医生
                                            <?php elseif($vo['rank'] == 3): ?>
                                            高级医生<?php endif; ?>
                                    </div>
                                </div>
                                <div class="bottom fl">
                                    <div class="fl">
                                        <div class="lf">执业编号 ：</div>
                                        <div class="number lf"><?php echo ($vo["practice"]); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12 col-sm-12 col-xs-12 admit" align="center">
                            <div class="fl admit_page">
                                <div class="info_admit">接待人数 <br/><span class="number_color"><?php echo ($vo["servernum"]); ?></span>
                                </div>
                                <div class="info_admit">评价人数 <br/><span class="number_color"><?php echo ($vo["evaluatenum"]); ?></span>
                                </div>
                                <div class="info_admit">应答时间 <br/><span class="number_color"><?php echo ($vo["responsetime"]); ?></span>
                                </div>
                                <div class="info_admit">综合评分 <br/><span class="number_color"><?php echo ($vo["zscore"]); ?></span></div>
                            </div>

                        </div>
                        <!-- <div class="col-md-12 col-sm-12 col-xs-12">
                             <?php echo ($vo[""]); ?>...
                         </div>-->
                    </a>
                    <div class="col-md-12 col-sm-12 col-xs-12 button">
                        <div class=" fl">
                            <div class=" btn_left lf" align="center">
                                <a href="<?php echo U('Index/Chat_record');?>">
                                    <button class="chat_btn lf">聊天记录</button>
                                </a>

                            </div>
                            <div class=" btn_left lf">
                                <a href="<?php echo U('Index/doctor_detail',array('id'=> $vo['id']));?>" class="fl">
                                    <button class="chat_btn lf">购买服务</button>
                                </a>

                            </div>
                            <div class=" btn_left lf">
                                <a href="<?php echo U('Index/evaluate_page',array('did'=> $vo['id']));?>" class="fl">
                                    <button class="chat_btn lf">查看评价</button>
                                </a>
                            </div>
                            <!-- <div class=" btn_right rt">
                                 <button class=" delete_btn lf">删除</button>
                             </div>-->
                        </div>

                    </div>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>

</div>

<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>
<script>
    $(".loop_page:last").children().css("margin-bottom", "10px");
    $(".delete_btn").click(function () {
        if (confirm("确定删除？")) {
            $(this).parent().parent().parent().parent().remove()
        }
    })
</script>
</body>

</html>