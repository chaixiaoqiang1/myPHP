<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>个人中心</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/my_content.css"/>

</head>

<body>
<div class="container">
    <!-- 顶部个人信息-->
    <div class="row top_user_info">
        <div class="col-md-12 col-sm-12 col-xs-12 ">
            <a href="revise_info.html" class="fl cl">
                <div class="user_info fl">
                    <div class="user_head lf">
                        <img src="<?php echo ($sickusername["icon"]); ?>" class="img-responsive img-circle" alt=""/>
                    </div>
                    <div class="user_name lf"><?php echo ($sickusername["username"]); ?></div>

                    <div class="next_step rt">
                        <img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row content">
        <!--积分 档案管理 -->
        <div class="col-md-12 col-sm-12 col-xs-12 integral">
            <!--<a href="my_integral.html" class="cl">
                <div class="integral_top fl">
                    <div class="integral_img lf">
                        <img src="/public_html/Public/doctor/img/integral.png" class="img-responsive" style="width: 25px" alt=""/>
                    </div>
                    <div class="integral_tx lf" style="line-height: 25px">我的积分</div>
                    <div class="integral_next rt">
                        <img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>-->
            <a href="<?php echo U('Newuser/change');?>" class="cl">
                <div class="integral_bottom integral_top fl">
                    <div class="integral_img lf">
                        <img src="/public_html/Public/newuser/img/my_money.png" class="img-responsive" style="width: 20px" alt=""/>
                    </div>
                    <div class="integral_tx lf" style="">我的零钱</div>
                    <div class="integral_next rt">
                        <img src="/public_html/Public/sick/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>
            <a href="<?php echo U('Index/my_health');?>" class="cl">
                <div class="integral_bottom fl">
                    <div class="integral_img lf">
                        <img src="/public_html/Public/doctor/img/file.png" class="img-responsive" style="width: 20px" alt=""/>
                    </div>
                    <div class="integral_tx lf" style="line-height: 20px">健康档案</div>
                    <div class="integral_next rt">
                        <img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>
        </div>

        <!--二维码 邀请好友 -->
        <div class="col-md-12 col-sm-12 col-xs-12 integral">
            <a href="<?php echo U('Index/my_Orcode');?>" class="cl">
                <div class="integral_top fl">
                    <div class="integral_img lf">
                        <img src="/public_html/Public/doctor/img/codd.png" class="img-responsive"  style="width: 20px" alt=""/>
                    </div>
                    <div class="integral_tx lf" style="line-height: 20px">我的二维码</div>
                    <div class="integral_next rt">
                        <img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>
            <a href="<?php echo U('Index/my_follower');?>" class="cl">
                <div class="integral_bottom fl">
                    <div class="integral_img lf">
                        <img src="/public_html/Public/doctor/img/follower1.png" class="img-responsive" style="width: 25px" alt=""/>
                    </div>
                    <div class="integral_tx lf">我的关注</div>
                    <div class="integral_next rt">
                        <img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>


        </div>
        <!-- 添加桌面 意见反馈-->

        <div class="col-md-12 col-sm-12 col-xs-12 integral" style="padding-left: 13px">
       <!--     <a href="" class="cl">
                <div class="integral_top fl">
                    <div class="integral_img lf">
                        <img src="/public_html/Public/doctor/img/invite.png" class="img-responsive" style="" alt=""/>
                    </div>
                    <div class="integral_tx lf">邀请好友</div>
                    <div class="integral_next rt">
                        <img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>-->
            <a href="<?php echo U('Index/feedback');?>" class="cl">
                <div class="integral_bottom fl" style="padding-top: 10px">
                    <div class="integral_img lf">
                        <img src="/public_html/Public/doctor/img/view.png" class="img-responsive" alt=""/>
                    </div>
                    <div class="integral_tx lf">意见反馈</div>
                    <div class="integral_next rt">
                        <img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/>
                    </div>
                </div>
            </a>



        </div>
    </div>
</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>

</body>

</html>