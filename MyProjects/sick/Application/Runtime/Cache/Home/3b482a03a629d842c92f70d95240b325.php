<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>提现</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">

    <link rel="stylesheet" href="/public_html/Public/newuser/css/aaa.css"/>

</head>

<body>
<div class="container">
    <form action="<?php echo U('Newuser/withdraw_deposit');?>" method="post">
        <div class="row apply">
            <div class="col-md-12 col-sm-12 col-xs-12">


                <div  class="pull-left number" >支付宝账号 </div>
                <input type="text" style="width: 60%" class="form-control pull-left" id="exampleInputEmail1" placeholder="请输入支付宝账号" name="account_number" onkeyup="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" oncontextmenu = "value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')">

            </div>
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 15px">

                <div class="pull-left money" >提现金额</div>
                <input type="text" style="width: 60%" class="form-control" id="exampleInputPassword1" placeholder="请输入提现金额" name="price" onkeyup="value=value.replace(/[^0-9]/g,'')" onpaste="value=value.replace(/[^0-9]/g,'')" oncontextmenu = "value=value.replace(/[^0-9]/g,'')">
            </div>
        </div>
        <button class="button" id="tijiao">下一步</button>
    </form>
</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>

<script>
    $('#tijiao').click(function(){
        $('form:first').submit(function(){
//           alert($("input[name='account_number']").val());
            if(!$("input[name='account_number']").val()){
                alert("支付宝账号不能为空");
                return false;
            }
            if(!$("input[name='price']").val()){
                alert("金额不能为空");
                return false;
            }
        });
    })

</script>


</body>

</html>