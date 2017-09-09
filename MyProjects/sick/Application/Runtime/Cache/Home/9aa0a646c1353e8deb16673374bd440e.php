<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>评价</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/evaluate.css"/>
    <link rel="stylesheet" type="text/css" href="/public_html/Public/doctor/css/application.css" >
    <link rel="stylesheet" href="/public_html/Public/doctor/css/application.css"/>
</head>

<body>
<div class="container">
    <!-- 顶部信息栏-->
    <div class="doctor_page">
        <div class="row fl doctor_info">
            <div class="col-md-3 col-sm-3 col-xs-3 doctor_info_content">
                <div class="doctor_img" style="width:60px;">
                    <img src="/public_html/Uploads/<?php echo ($doctordata["image"]); ?>" class="img-responsive img-circle" alt="" style="width:60px;height: 60px;"/>
                </div>
            </div>
            <div class="col-md-9 col-sm-9 col-xs-9 clear_padding">
                <div class="info fl">
                    <div class=" fl">
                        <div class="lf doctor_name"><?php echo ($doctordata["username"]); ?></div>
                        <div class="lf state">
                            <div class="state_tx lf">
                                <?php if($doctordata['is_online'] == 1): ?>在线
                                    <img src="/public_html/Public/doctor/img/chenggong.png" class="img-responsive img-circle state_img lf" alt=""/>
                                    <?php else: ?>离线
                                    <img src="/public_html/Public/doctor/img/close.png" class="img-responsive img-circle state_img lf" alt=""/><?php endif; ?>
                            </div>

                        </div>
                    </div>
                    <div class="content fl">
                        <div class="classfiy"><?php echo ($doctordata["office"]); ?>
                            <?php if($doctordata['rank'] == 1): ?>初级医生
                                <?php elseif($doctordata['rank'] == 2): ?>中级医生
                                <?php elseif($doctordata['rank'] == 3): ?>高级医生<?php endif; ?>
                            </div>
                    </div>
                    <div class="bottom fl">
                        <div class="fl">
                            <div class="lf">执业编号 ：</div>
                            <div class="number lf"><?php echo ($doctordata["practice"]); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- 评分-->
    <!-- 评论内容-->
    <div class="row content">
        <div class="col-md-12 col-sm-12 col-xs-12 ">
            <form action="/public_html/index.php/Home/Index/evaluate/3" method="post">
                <input type="hidden" name="fid" value="<?php echo ($get["fid"]); ?>">
                <input type="hidden" name="did" value="<?php echo ($get["did"]); ?>">

                <input type="hidden" name="xingxing" id="xingxing"/>
                <input type="hidden" name="pingjia" id="pingjia"/>
                <!-- 评分-->
                <div class="evaluate">
                    <div class="evaluate_tx">综合评分 ： </div>
                        <div class="demo fl" >
                            <div id="function-demo1" class="target-demo lf"></div>
                            <div class="fen rt" style="line-height: 24px;color: #FFA242">分</div>
                            <div id="function-hint1" class="hint rt"></div>
                        </div>
                </div>
                <div class="top fl">
                    <div class="br_style_top click_status lf">回复很及时</div>
                    <div class="br_style_top click_status lf">态度非常好</div>
                    <div class="br_style_top click_status lf">非常清楚</div>
                </div>
                <div class="bottom fl">
                    <div class="br_style_bottom click_status  lf">意见很有帮助</div>
                    <div class="br_style_bottom click_status  lf">非常认真专业</div>
                    <div class="br_style_bottom click_status  lf">非常敬业</div>
                </div>
                <div class="text_field">
                    <textarea class="text_field_content"  id="" rows="5" placeholder="请输入您的评价~" name="content"></textarea>
                </div>
            </form>
            <!--<a href="success_evalute.html">-->
                <button class="button" id="tijiao">提交</button>
            <!--</a>-->
        </div>
    </div>
</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>
<script src="/public_html/Public/doctor/js/jquery.raty.min.js"></script>
<script src="/public_html/Public/doctor/js/bootstrap.js"></script>
<script>

    $(".click_status").click(function () {
        if ($(this).hasClass("click_status")) {
            $(this).removeClass("click_status").addClass("bj")
        } else {
            $(this).removeClass("bj").addClass("click_status")
        }
    })

    $.fn.raty.defaults.path = '../img';
    $('#function-demo').raty({
        number: 5,//多少个星星设置
        targetType: 'hint',//类型选择，number是数字值，hint，是设置的数组值
        path      : '/public_html/Public/doctor/img',
        hints     : ['差','一般','好'],
        cancelOff : 'cancel-off-big.png',
        cancelOn  : 'cancel-on-big.png',
        size      : 24,
        starHalf  : 'star-half-big.png',
        starOff   : 'star-off-big.png',
        starOn    : 'star-on-big.png',
        target    : '#function-hint',
        cancel    : false,
        targetKeep: true,
        targetText: '请选择评分',
        click: function(score, evt) {
            alert('ID: ' + $(this).attr('id') + "\nscore: " + score + "\nevent: " + evt.type);
        }
    });

    $('#function-demo1').raty({
        number: 5,//多少个星星设置
        score: 2,//初始值是设置
        targetType: 'number',//类型选择，number是数字值，hint，是设置的数组值
        path      : '/public_html/Public/doctor/img',
        cancelOff : 'cancel-off-big.png',
        cancelOn  : 'cancel-on-big.png',
        size      : 24,
        starHalf  : 'star-half-big.png',
        starOff   : 'star-off-big.png',
        starOn    : 'star-on-big.png',
        target    : '#function-hint1',
        cancel    : false,
        targetKeep: true,
        precision : false,//是否包含小数
    });

    $('#function-demo1').click(function(){
        var xing = $('#function-hint1').text();
            $('#xingxing').val(xing);
    })

    $('#tijiao').click(function(){
        $('form:first').submit();
    })


    $('.click_status').click(function(){
         var aaa = $(this).text();
        var com = $('#pingjia').val() + aaa;
        $('#pingjia').val(com);
    })

</script>
</body>

</html>