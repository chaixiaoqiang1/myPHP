<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>查找医生</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/index.css"/>
    <style>

    </style>
</head>

<body style="overflow: hidden">

<div class="container">
    <!-- 顶部搜索框-->
    <div class="navbar navbar-fixed-top" style="z-index: 999">
        <div class="row seach">
            <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="seach_br fl">
                        <div class="seach_img lf">
                            <img src="/public_html/Public/doctor/img/seach1.png" class="img-responsive" alt=""/>
                        </div>
                        <div class="seach_ipt lf" style="width: 87%">
                            <form method="post" action="/public_html/index.php">
                            <input be_adept_at="text" placeholder="搜索医生名" name="username" style="width: 100%"/>
                            </form>
                        </div>
                    </div>

            </div>
        </div>
    </div>
    <!-- 分类-->
    <div class="row classfiy">
        <div class="col-md-2 col-sm-3 col-xs-3 clear_left" align="center">
            <ul class="classfiy_ct">
                <a href="">
                    <li class="names all">全部</li>
                </a>
                <?php if(is_array($categorydata)): $i = 0; $__LIST__ = $categorydata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Index/index_state',array('id'=>$vo['id']));?>">
                        <li class="names"><?php echo ($vo["catname"]); ?></li>
                    </a><?php endforeach; endif; else: echo "" ;endif; ?>

            </ul>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9 classfiy_right">
            <ul class="fl sort">
                <form action="<?php echo U('Index/index');?>" method="post" id="xiangying">
                        <input type="hidden" name="shijian" value="1">
                    <li class="sort_content lf sort_ct">综合排序</li>
                    <li class="sort_content2 lf sort_ct" id="sj01">按相应时间</li>
                    <li class="sort_content3 rt sort_ct">更多条件 &gt;</li>
                </form>
            </ul>
            <?php if(is_array($doctordata)): foreach($doctordata as $key=>$vo): ?><!--  <p ><?php echo ($vo["id"]); echo ($vo["speciality"]); ?></p>-->
                <a href="<?php echo U('Index/doctor_detail',array('id'=>$vo['id']));?>">
                <div class="row doctor ">
                    <div class="col-md-4 col-sm-4 col-xs-4 doctor_ct">
                        <div class="doctor_img">
                            <img src="/public_html/Uploads/<?php echo ($vo["image"]); ?>" class="img-responsive img-circle doctor_img_size" alt=""/>
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-8 col-xs-8 clear_pd_left">
                        <div class="info_content">
                            <div class=" info fl">
                                <div class="status lf">
                                    <?php if($vo['is_online'] == '1'): ?><span class="lf info_txt">在线</span>
                                        <img src="/public_html/Public/doctor/img/chenggong.png" class="img-responsive lf info_img" alt=""/>
                                    <?php else: ?>
                                        <span class="lf info_txt">离线</span>
                                        <img src="/public_html/Public/doctor/img/close.png" class="img-responsive lf info_img" alt=""/><?php endif; ?>
                                </div>
                                <div class="money_number rt">
                                    <p style="color:blue;font-size: 5px">图文<?php echo ($vo["tuwenpirce"]); ?>元/次</p>
                                    <p style="font-size: 5px">预约<?php echo ($vo["orderprice"]); ?>元/次</p>
                                </div>

                            </div>
                            <div class="type"><?php echo ($vo["catname"]); ?> 医师</div>
                            <div class="time">平均应答时间 ：<?php echo ($vo["responsetime"]); ?></div>
                        </div>

                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 doctor_names">
                        <div class="name lf" align="center"><?php echo ($vo["username"]); ?></div>
                        <div class="number lf">执业编号：<span class="number_content"><?php echo ($vo["practice"]); ?></span></div>

                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="be_adept_at"><?php echo (substr($vo["speciality"],0,30)); ?>...</div>
                        <div class="fl consult_content">
                            <a href="<?php echo U('Index/doctor_detail',array('id'=>$vo['id']));?>">
                                <div class="help_number lf">已帮助<span class="Help_number">
                                <?php if($vo['servernum'] != ''): echo ($vo["servernum"]); ?>
                                    <?php else: ?>
                                    0<?php endif; ?>
                                </span>人
                                </div>
                                <div class="consult rt">向TA咨询</div>
                            </a>
                        </div>
                    </div>
                </div>
            </a><?php endforeach; endif; ?>
        </div>
    </div>
</div>
<div class="window"></div>
<div class="more">
    <form action="/public_html/index.php" method="post">
    <div class="academic_title">职称 :</div>
    <ul class="checktype fl">
            <input type="hidden" name="rank" val="1">
        <li class="more_content1 init">初级</li>
        <li class="more_content2 init">中级</li>
        <li class="more_content3 init">高级</li>
    </ul>

    </form>
    <div class="function">
        <div class="left" id="left">清除设置</div>
        <div class="right" >完成设置</div>
    </div>
</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>
<script src="/public_html/Public/doctor/js/index_state.js"></script>
<script src="/public_html/Public/doctor/js/bootstrap.js"></script>
<script>
   /* if ($(".be_adept_at").text().length > 15) {
        $(".be_adept_at").text($(".be_adept_at").text().substring(0, 15));
        $(".be_adept_at").html($(".be_adept_at").html() + '…');
    }*/
    $(".left").click(function () {
        $(".init").css("background", "#e9e9e9");
        $(".init").css("color", "#888");
    })

    $('.more_content1').click(function(){
        $("input:hidden").val(1);
    });
   $('.more_content2').click(function(){
       $("input:hidden").val(2);
   });
   $('.more_content3').click(function(){
       $("input:hidden").val(3);
   });

    $('.right').click(function(){
        $('form').eq(1).submit();
    })


    /*$("#shijian").click(function(){
    $.post("<?php echo U('Index/index');?>",{'sj':'shijian'});
    })*/


    $('#sj01').click(function(){
        $("#xiangying").submit();
    })

</script>
</body>

</html>