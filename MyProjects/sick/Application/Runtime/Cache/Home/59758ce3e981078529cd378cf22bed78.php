<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>医生详情</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/doctor_detail.css"/>


</head>

<body>
<div class="container">
    <!-- 顶部一医生信息-->
    <div class="row fl doctor_info">
        <div class="col-md-3 col-sm-3 col-xs-3 doctor_info_content">
            <div class="doctor_img">
                <img src="/public_html/Uploads/<?php echo ($doctordata["image"]); ?>" class="img-responsive img-circle" style="width: 55px;height: 55px;" alt=""/>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9 clear_padding">
            <div class="info fl">
                <div class="top fl">
                    <div class="lf doctor_name"><?php echo ($doctordata["username"]); ?></div>
                    <div class="lf state">
                        <div class="state_tx lf" style="margin-left: 1px">
                            <?php if($doctordata['is_online'] == 1): ?><span style="line-height: 16px;" class="pull-left">&nbsp;在线</span>
                                <img  src="/public_html/Public/doctor/img/chenggong.png" class="img-responsive img-circle state_img lf pull-left" alt=""/>
                                <?php else: ?>
                                 <span style="line-height: 16px;" class="pull-left">&nbsp;离线</span>
                                <img src="/public_html/Public/doctor/img/close.png" class="img-responsive img-circle state_img lf pull-eft" alt=""/><?php endif; ?>
                        </div>

                    </div>

                    <?php if($type == 1): ?><a href="<?php echo U('Index/ajax',array('id'=>$did));?>">
                            <div class="collect pull-right judge fl" id="<?php echo ($_GET['id']); ?>">
                                <div class="lf collect_img">
                                    <img src="/public_html/Public/doctor/img/scdown.png" class="img-responsive img" style="width: 15px" alt=""/>
                                </div>
                                <div class="lf collect_tx" >已关注</div>
                            </div>
                        </a>
                     <?php else: ?>
                        <a href="<?php echo U('Index/ajax',array('id'=>$did));?>">
                            <div class="collect pull-right  fl" id="<?php echo ($_GET['id']); ?>">
                                <div class="lf collect_img">
                                    <img src="/public_html/Public/doctor/img/xin_init.png" class="img-responsive img" style="width: 15px" alt=""/>
                                </div>
                                <div class="lf collect_tx" >加关注</div>
                            </div>
                        </a><?php endif; ?>
                </div>
                <div class="content fl">
                    <div class="classfiy"><?php echo ($doctordata["office"]); ?>
                        <?php if($doctordata['rank'] == 1): ?>初级医生
                            <?php elseif($doctordata['rank'] == 2): ?> 中级医生
                            <?php elseif($doctordata['rank'] == 3): ?> 高级医生<?php endif; ?>
                    </div>
                </div>
                <div class="bottom fl">
                    <div class="fl">
                        <div class="lf">执业编号 ：</div>
                        <div class="number lf"><?php echo ($doctordata["practice"]); ?></div>
                        <div class="evaluate rt">
                            <span>评分</span>
                            <span class="evaluate_number"><?php echo ($doctordata["zscore"]); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 医生简介-->
    <div class="row summary_content">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="fl">
                <div class="summary lf">
                    <img src="/public_html/Public/doctor/img/abstract.png" class="summary_img lf" alt=""/>
                </div>
                <div class="summary_tx lf">
                    简介
                </div>
            </div>

            <div class="summary_content_tx">
                <?php echo ($doctordata["intro"]); ?>
            </div>
        </div>
    </div>
    <!--擅长-->
    <div class="row summary_content" id="tx">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="fl">
                <div class="summary lf" style="background: #E33F3F">
                    <img src="/public_html/Public/doctor/img/jiangpai.png" class="summary_img lf" alt=""/>
                </div>
                <div class="summary_tx lf">
                    擅长
                </div>
            </div>

            <div class="summary_content_tx">
                <span class="a1" id="a">
                   <?php echo ($doctordata["speciality"]); ?>
                </span>
                <span class="c">
                    <?php echo ($doctordata["speciality"]); ?>
                </span>
                <span id="aa" class="b" style="font-size: 12px">展开</span>
            </div>
        </div>
    </div>
    <!-- 综合评价-->
    <div class="row summary_content">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="fl">
                <div class="summary lf" style="background: #1CCA1C">
                    <img src="/public_html/Public/doctor/img/evaluate.png" class="summary_img lf" style="margin-left: 3px" alt=""/>
                </div>
                <div class="summary_tx lf">
                    综合评价
                </div>
            </div>
            <div class="summary_content_tx fl">
                <div class="consult fl">
                    <div class="consult_left lf">
                        <span>接受咨询 :</span>
                        <span class="number"><?php echo ($doctordata["receiveconsultnum"]); ?>人</span>
                    </div>
                    <div class="consult_left rt">
                        <span>平均应答时间 :</span>
                        <span class="number"><?php echo ($doctordata["responsetime"]); ?></span>
                    </div>
                </div>
                <div class="consult fl" style="padding-top: 10px">
                    <div class="consult_left lf">
                        <span>好评 :</span>
                        <span class="number"><?php echo ($doctordata["haoping"]); ?>人</span>
                    </div>
                    <div class="consult_left  rt ">
                        <span>购买服务 :</span>
                        <span class="number"><?php echo ($doctordata["buyservernum"]); ?>人</span>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!-- 评论内容-->

    <!-- 诊所服务-->
    <div class="row summary_content">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="fl">
                <div class="summary lf" style="background: #F3A433">
                    <img src="/public_html/Public/doctor/img/fuwu.png" class="summary_img lf" style="margin-left: 3px" alt=""/>
                </div>
                <div class="summary_tx lf">
                    诊所服务
                </div>
            </div>

            <div class="summary_content_tx">

                    <?php if($servicedata['is_picture'] == 1): ?><div class="image_text fl">
                    <div class="image_text_img lf">
                        <img src="/public_html/Public/doctor/img/img.png" class="img-responsive top_img" alt=""/>
                    </div>
                    <div class="image_text_tx lf">
                        <div class="picture_consulting_top">图文咨询&nbsp; <span class="money"> &yen; <?php echo ($doctordata["tuwenpirce"]); ?></span></div>
                        <div class="picture_consulting_bottom picture_consulting">通过图片、文字、语音进行交流</divpicture_consulting>
                        </div>
                    </div>
                    <div class="sex_right rt">
                        <div class="right_img lf">
                            <img src="/public_html/Public/doctor/img/default.png" class="img-responsive menfolk" alt="" id="order01" info="1"/>
                        </div>
                    </div>
                </div><?php endif; ?>
                        <?php if($servicedata['is_orderstatus'] == 1): ?><div class="bespoke fl">
                    <div class="image_text_img lf" style="background: #2B9FE2">
                        <img src="/public_html/Public/doctor/img/note.png" class="img-responsive bottom_img" alt="" />
                    </div>
                    <div class="image_text_tx lf">
                        <div class="picture_consulting_top">预约就诊&nbsp; <span class="money"> &yen; <?php echo ($doctordata["orderprice"]); ?></span></div>
                        <div class="picture_consulting_bottom">通过图片、文字、语音进行交流</div>
                        </div>
                    <div class="sex_right rt">
                        <div class="right_img lf">
                            <img src="/public_html/Public/doctor/img/default.png" class="img-responsive femininity" alt="" id="order02" info="1"/>
                        </div>
                    </div>
                    </div><?php endif; ?>

                </div>
            </div>
        </div>
    </div>
    <div class="row evaluate_br">
        <?php if(is_array($commentdata)): $i = 0; $__LIST__ = $commentdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col-md-12 col-sm-12 col-xs-12">
            <div class="evaluate_content fl">
                <div class="evaluate_content_tx">
                    评论内容：<span class="evaluate_txt"><?php echo ($vo["content"]); ?> </span>
                </div>
                <div class="user_info fl">
                    <div class="user_img lf">
                        <img src="<?php echo ($vo["s_id"]["icon"]); ?>" class="img-responsive img-circle"  alt=""/>
                    </div>
                    <div class="user_evaluate_tx lf">
                        <div class="up fl">
                            <div class="cryptonym lf"><?php echo ($vo["s_id"]["username"]); ?>&nbsp;&nbsp;&nbsp;<span class="sex"><?php if($vo['s_id']['sex'] == 1): ?>男<?php else: ?>女<?php endif; ?></span></div>
                            <div class="city lf">
                                来自：<span class="city_content"><?php echo ($vo["s_id"]["address"]); ?></span>
                            </div>
                        </div>
                        <div class="down fl">
                            <div class=""><?php echo date("Y-m-d H:i:s",$vo['addtime']);?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<div class="height"></div>
<form action="/public_html/index.php/Home/Index/doctor_detail/id/3.html" method="post">
    <input name="type" type="hidden" id="type01" />
    <input name="did" type="hidden" value="<?php echo ($did); ?>" />
<!--<a href="<?php echo U('Index/pay_immediately',array('id'=>$doctordata['id']));?>" style="color: #ffffff;text-decoration: none">-->
    <div class="navbar navbar-fixed-bottom" id="liji_01">立即咨询</div>
<!--</a>-->
</form>

<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>
<script>
    //    if($(".a").text().length>40){ $(".a").text($(".a").text().substring(0,40)); $(".a").html($(".a").html()+'…');
    //    }

    $(".b").click(function () {
        if ($(".c").css("display") == "block") {
            $(".c").hide();
            $(".a1").show()
            $(".b").html("收起")
        } else {
            $(".a1").hide();
            $(".c").show()
            $(".b").html("展开")
        }
    })



/*

    $(".collect").click(function () {
        var xiang = $(this).attr('id');
        $.post("<?php echo U('Index/ajax');?>", {'id': xiang});
     /!*  $.post("<?php echo U('Index/ajax');?>", {'id': xiang},function(data){
            if(data == 1){
                var url="/public_html/index.php/Home/Index/doctor_detail";
                location.href=url+'id/'+xiang;
            }else{
                return false;
            }
        });*!/
    })
*/

    $(".femininity").click(function () {
        if ($(".menfolk").attr("src") == "/public_html/Public/doctor/img/succeed.png") {
            $(".menfolk").attr("src", "/public_html/Public/doctor/img/default.png")
        }
        if ($(this).attr("src") == "/public_html/Public/doctor/img/default.png") {
            $(this).attr("src", "/public_html/Public/doctor/img/succeed.png")
            $('#type01').val('2');
        } else {
            $('#type01').val('');
            $(this).attr("src", "/public_html/Public/doctor/img/default.png")
        }
    })

    $(".menfolk").click(function () {
        if ($(".femininity").attr("src") == "/public_html/Public/doctor/img/succeed.png") {
            $(".femininity").attr("src", "/public_html/Public/doctor/img/default.png")
        }
        if ($(this).attr("src") == "/public_html/Public/doctor/img/default.png") {
            $(this).attr("src", "/public_html/Public/doctor/img/succeed.png")
            $('#type01').val('1');
        } else {
            $('#type01').val('');
            $(this).attr("src", "/public_html/Public/doctor/img/default.png")
        }
    })


</script>
<script>
        $('#liji_01').bind('click',function(){
            var ser=$('#type01').val();
            if(!ser){
                alert('请先选择服务类型');
                return false;
            }
            $('form:first').submit();
        })
</script>
</body>

</html>