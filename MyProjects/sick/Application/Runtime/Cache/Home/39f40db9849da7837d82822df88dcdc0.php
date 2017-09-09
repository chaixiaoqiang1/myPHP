<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>送心意</title>

    <link href="/public_html/Public/sick/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/newuser/css/Send%20_the_mind.css"/>
    <style>
        .img-circle {
            border-radius: 50%;
        }

        .img-responsive{
            display: block;
            height: auto;
            max-width: 100%;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 br">
            <div class="doctor_img" style="width:65px;margin: auto">
                <img src="/public_html/Uploads/<?php echo ($dname["image"]); ?>" class="img-responsive img-circle" alt="" style="width: 65px;height: 65px;"/>
            </div>
            <div class="doctor_user"><?php echo ($dname["username"]); ?></div>
            <div class="thanks">谢谢您的真诚帮助</div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12" align="center">
            <form action="<?php echo U('Newuser/Send_the_mind');?>" method="post">
                <input type="hidden" name="did" value="<?php echo ($did); ?>">
                <div class="money">
                    <div class="tx lf">请输入答谢金额</div>
                    <input type="text" class="lf ipt" name="price" onkeyup="value=value.replace(/[^0-9]/g,'')" onpaste="value=value.replace(/[^0-9]/g,'')" oncontextmenu = "value=value.replace(/[^0-9]/g,'')"/>
                    <div class="tx lf">元</div>
                </div>
                <div class="text_field">
                    <textarea class="text_field_content" name="content" id="" rows="5" placeholder="说点感谢医生的话~"></textarea>
                </div>
            </form>
            <!--<a href="Send_the_mind_money.html">-->
                <button class="button" id="tijiao">提交</button>
        </div>
    </div>
</div>
<script src="/public_html/Public/sick/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/sick/js/bootstrap.js"></script>

<script>
    $("#tijiao").click(function(){
        if(!$("input[name='price']").val()){
            alert('输入金额不能为空');
            return false;
        }
        $("form:first").submit();
    })

</script>
</body>

</html>