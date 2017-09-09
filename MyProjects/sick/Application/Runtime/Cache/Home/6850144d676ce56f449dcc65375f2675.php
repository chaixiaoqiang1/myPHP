<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>我的零钱</title>

    <link href="/public_html/Public/newdoctor/css/bootstrap.css" rel="stylesheet">

    <link rel="stylesheet" href="/public_html/Public/newdoctor/css/doctor_change.css"/>

</head>

<body>
<div class="container">
    <div class="row top">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="user_info fl">
                <div class="user_head">
                    <p style="margin-top: 20px; text-align: center;">
                        <img alt="" class="img-responsive img-circle" src="/public_html/Uploads/<?php echo ($list["image"]); ?>" style="width:60px; height: 60px; border-radius: 50%;">
                    </p>
                </div>
            </div>
            <div class="money" style="margin: 0; padding-top: 3%;color:green;" ><?php echo ($list["balance"]); ?></div>
            <div class="balance" style="color: green">余额(元)</div>
            <!-- <a href="accounts.html" class="accounts" style="z-index: 1000;text-decoration: none">
                 <div class="">账目明细</div>
             </a>
             <a href="user_Alipay.html" class="accounts" style="top: 0;left: 0px;text-decoration: none">
                 <div class="" >支付宝账号</div>
             </a>-->
        </div>
    </div>
    <div class="row content">
        <!--<div class="col-md-12 col-sm-12 col-xs-12 pd">-->
            <!--<a href="recharge%20.html" class="fl"style="display: block">-->
                <!--<div class="left lf">充值</div>-->
                <!--<div class="right rt">-->
                    <!--<img src="../img/right.png" class="img-responsive" alt=""/>-->
                <!--</div>-->
            <!--</a>-->
        <!--</div>-->
        <div class="col-md-12 col-sm-12 col-xs-12 pd"style="border: none">
            <a href="doctor_withdraw_deposit.html" class="fl"style="display: block">
                <div class="left lf">提现</div>
                <div class="right rt">
                    <img src="../img/right.png" class="img-responsive" alt=""/>
                </div>
            </a>

        </div>

    </div>
</div>
<script src="../js/jquery-1.11.3.js"></script>

<script src="../js/bootstrap.js"></script>

</body>

</html>