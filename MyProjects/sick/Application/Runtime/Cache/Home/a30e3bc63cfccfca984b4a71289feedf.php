<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>评论</title>

    <link href="/public_html/Public/doctor/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/doctor/css/evaluate_page.css"/>
</head>

<body>
<div class="container">
    <div class="row ">
        <div class="col-md-12 col-sm-12 col-xs-12 a">
            <?php if(is_array($comment)): $i = 0; $__LIST__ = $comment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="evaluate_content fl">
                <div class="evaluate_content_tx">
                    评论内容：<span class="evaluate_txt"><?php echo ($vo["content"]); ?></span>
                </div>
                <div class="user_info fl">
                    <div class="user_img lf">
                        <img src="<?php echo ($vo["s_id"]["icon"]); ?>" class="img-responsive img-circle"  alt=""/>
                    </div>
                    <div class="user_evaluate_tx lf">
                        <div class="up fl">
                            <div class="cryptonym lf"><?php echo ($vo["s_id"]["username"]); ?>&nbsp;&nbsp;&nbsp;<span class="sex">
                              <?php if($vo['s_id']['sex'] == 1): ?>男
                                  <?php else: ?>女<?php endif; ?>
                            </span></div>
                            <div class="city lf">
                                来自：<span class="city_content"><?php echo ($vo["s_id"]["address"]); ?></span>
                            </div>
                        </div>
                        <div class="down fl">
                            <div class=""><?php echo date("Y-m-d H:i:s",$vo['addtime']);?></div>
                        </div>
                    </div>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>

        </div>
    </div>
</div>
<script src="/public_html/Public/doctor/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/doctor/js/bootstrap.js"></script>
<script>
    $(".a:last").children().last().css("border","none")
</script>
</body>

</html>