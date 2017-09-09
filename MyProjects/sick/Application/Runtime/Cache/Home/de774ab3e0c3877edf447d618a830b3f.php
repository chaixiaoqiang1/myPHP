<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>修改个人信息</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/my_health.css"/>
    <link rel="stylesheet" href="/public_html/Public/doctor/css/revise_info.css"/>
</head>
<body>
<div class="container">
    <form action="/public_html/index.php/Home/Index/revise_info.html" method="post">
    <div class="row change">
        <div class="col-md-12 col-sm-12 col-xs-12 my_datum_content">
            <div class="my_info fl">
                <div class="height lf">呢称</div>
                <input type="text" class="ipt rt" name="username" placeholder="请输入昵称" value="<?php echo ($sickdata["username"]); ?>" />
            </div>
            <div class="my_info fl">
                <div class="sex lf">性别</div>
                <div class="sex_right rt" >
                    <div class="right_img lf" >
                        <?php if($sickdata['sex'] == 2): ?><img src="/public_html/Public/doctor/img/succeed.png" class="img-responsive menfolk" alt="" />
                            <?php else: ?>
                             <img src="/public_html/Public/doctor/img/default.png" class="img-responsive menfolk" alt="" /><?php endif; ?>
                    </div>
                    <div class="man lf"><input type="hidden" name="sex" id="nv1" />女</div>
                </div>
                <div class="sex_right rt">
                    <div class="right_img lf" >
                        <?php if($sickdata['sex'] == 1): ?><img src="/public_html/Public/doctor/img/succeed.png" class="img-responsive femininity" alt=""/>
                            <?php else: ?>
                        <img src="/public_html/Public/doctor/img/default.png" class="img-responsive femininity" alt=""/><?php endif; ?>
                    </div>
                    <div class="man lf">男</div>
                </div>
            </div>
            <div class="my_info fl">
                <div class="height lf">年龄</div>
                <input type="text" class="ipt rt" name="userage" placeholder="请输入年龄" value="<?php echo ($sickdata["userage"]); ?>" onkeyup="value=value.replace(/[^0-9]/g,'')" onpaste="value=value.replace(/[^0-9]/g,'')" oncontextmenu = "value=value.replace(/[^0-9]/g,'')" />
            </div>

            <div class="my_info fl">
                <div class="height lf">手机号码</div>
                <input type="text" class="ipt rt" name="phonenum" placeholder="请输入手机号码" value="<?php echo ($sickdata["phonenum"]); ?>" onkeyup="value=value.replace(/[^0-9]/g,'')" onpaste="value=value.replace(/[^0-9]/g,'')" oncontextmenu = "value=value.replace(/[^0-9]/g,'')" />
            </div>
        </div>
    </div>
        <!--<input type="submit" value="确认修改" class="button"/>-->
    </form>
        <button class="button" id="tijiao01">确认修改</button>

</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>
<script>
    $(".femininity").click(function () {
        if ($(".menfolk").attr("src") == "/public_html/Public/doctor/img/succeed.png") {
            $(".menfolk").attr("src", "/public_html/Public/doctor/img/default.png")
            $("#nv1").val('1');
        }
        if ($(this).attr("src") == "/public_html/Public/doctor/img/default.png") {
            $(this).attr("src", "/public_html/Public/doctor/img/succeed.png")
            $("#nv1").val('1');
        } else {
            $(this).attr("src", "/public_html/Public/doctor/img/default.png")
        }
    })

    $(".menfolk").click(function () {
        if ($(".femininity").attr("src") == "/public_html/Public/doctor/img/succeed.png") {
            $(".femininity").attr("src", "/public_html/Public/doctor/img/default.png")
            $("#nv1").val('2');
        }
        if ($(this).attr("src") == "/public_html/Public/doctor/img/default.png") {
            $(this).attr("src", "/public_html/Public/doctor/img/succeed.png")
            $("#nv1").val('2');
        } else {
            $(this).attr("src", "/public_html/Public/doctor/img/default.png")
        }

    })
    $(".my_info").click(function(){
        $(this).children().focus();
    })

   /* $("input[name='phonenum']").blur(function(){
        var t = $(this).val();
        var z = /^1\d{10}$/;
        if(!z.test(t)){
            alert("手机号格式输入有误");
            $(this).val(null);
        };
    })*/
    $("#tijiao01").click(function(){
        if($("input[name='userage']").val() == 0){
            alert('请输入年龄');
            return false;
        }
        var t = $("input[name='phonenum']").val();
        var z = /^1\d{10}$/;
        if(!z.test(t)){
            alert("手机号格式输入有误");
            $(this).val(null);
            return false;
        };
        $("form:first").submit();
    })
</script>

</body>

</html>