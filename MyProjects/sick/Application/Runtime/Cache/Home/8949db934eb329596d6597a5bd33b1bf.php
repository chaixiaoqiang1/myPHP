<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>预约门诊</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/quick_consultation.css"/>
    <link rel="stylesheet" href="/public_html/Public/doctor/css/common.css"/>
    <script src="/public_html/Public/doctor/js/quick_consultation.js"></script>

</head>

<body onload="init()">
<div class="container">
    <div class="row content">
        <div class="col-md-12 col-sm-12 col-xs-12 ">
            <form action="<?php echo U('Index/order');?>" id="user_reg" name="user_reg" method="post">
                <!-- 添加 文字 图片 的病情描述-->
                <div class="text_field fl">
                    <textarea id="depict" class="text_field_content" name="depict" rows="3"
                              placeholder="请详细描述您的症状、疾病和身体情况，便于医生更准确地分析，易问诊将保障您的安全。"></textarea>

                    <!--<div class="fl add_content">
                        <div class="add_img lf">
                            <img src="" class="img-responsive" alt=""/>
                        </div>
                        <div class="fileInputContainer pull-right">
                            <input class="fileInput" type="file" name=""/>
                        </div>
                    </div>-->

                </div>
                <!-- 年龄 性别 城市区域-->
                <div class="classfiy fl">
                    <div class="classfiy_top fl">
                        <div class="age fl lf">
                            <sapn>
                                年龄
                            </sapn>
                            <span class="age_number" >
                                <input type="text" name="age" placeholder="请输入年龄" onkeyup="value=value.replace(/[^0-9]/g,'')" onpaste="value=value.replace(/[^0-9]/g,'')" oncontextmenu = "value=value.replace(/[^0-9]/g,'')" />
                            </span>
                        </div>
                    </div>
                    <div class="classfiy_top fl">
                        <div class="left lf">
                            性别
                        </div>
                        <div class="select rt">
                            <input type="radio" name="sex" id="inlineRadio1" value="2"> 女
                        </div>
                        <div class="select rt">
                            <input type="radio" name="sex" id="inlineRadio2" value="1"> 男
                        </div>
                    </div>
                    <div class="classfiy_bottom fl">
                        <div class="left lf">
                            所在城市
                        </div>
                        <div class="select rt">
                            <select name="shi" onChange="select()"  class="text" style="text-align: right"></select>
                        </div>
                        <div class="select rt">
                            <select  name="sheng" onchange="select()"  class="text" style="text-align: right"></select>

                        </div>
                    </div>
                    <!--<a href="<?php echo U('Index/select_time',array('id'=> $serviceId));?>" style="color: #666666;">
                        <div class="times fl">
                            <div class="set_up lf" style="line-height: 20px">设置预约时间</div>
                            <div class="set_up_img rt" style="width: 20px"><img src="/public_html/Public/doctor/img/right.png" class="img-responsive" alt=""/></div>
                            <div class="rt" style="line-height: 20px">周一</div>
                        </div>
                    </a>-->
                    <div id="datePlugin"></div>


                </div>
                <!-- 日期时间-->
            </form>
                <button class="button" id="tijiao">提交</button>

        </div>
    </div>
</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>
<script src="/public_html/Public/doctor/js/quick_consultation.js"></script>
<script src="/public_html/Public/doctor/js/bootstrap.js"></script>
<script src="/public_html/Public/doctor/js/date.js"></script>
<script src="/public_html/Public/doctor/js/iscroll.js"></script>
<script>
    $(".femininity").click(function () {
        if ($(".menfolk").attr("src") == "../img/succeed.png") {
            $(".menfolk").attr("src", "../img/default.png")
        }
        if ($(this).attr("src") == "../img/default.png") {
            $(this).attr("src", "../img/succeed.png")
        } else {
            $(this).attr("src", "../img/default.png")
        }
    })

    $(".menfolk").click(function () {
        if ($(".femininity").attr("src") == "../img/succeed.png") {
            $(".femininity").attr("src", "../img/default.png")
        }
        if ($(this).attr("src") == "../img/default.png") {
            $(this).attr("src", "../img/succeed.png")
        } else {
            $(this).attr("src", "../img/default.png")
        }

    })
        $('#beginTime').date();
        $('#endTime').date({theme:"datetime"});

        $("#tijiao").click(function(){

            if(!$('#depict').val()){
                alert('请描述您的病情');
                return false;
            }

            if(!$("input[name='age']").val()){
                alert('请选择您的年龄！');
                return false;
            }
            if(!$("input:checked").val()){
                alert('请选择您的性别！');
                return false;
            }

            if($('select[name="shi"]').val() == '请选择' || $('select[name="sheng"]').val() == '请选择'){
                alert('请选择正确的地址');
                return false;
            }
             $("#user_reg").submit();
            // location.href = "<?php echo U('Index/select_time');?>";
        })
</script>
</body>

</html>