<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>患者名片</title>
    <link href="/public_html/Public/sick/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/sick/css/Patient_name_card.css"/>
</head>

<body>
<div class="container">

        <!-- 患者资料-->
        <div class="row top_user_info">
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px">
                <a href="" class="fl cl">
                    <div class=" fl">
                        <div class="user_head lf">
                            <img src="<?php echo ($info["icon"]); ?>" class="img-responsive img-circle" alt=""/>
                        </div>
                        <div class="info lf">
                            <div class="card"><?php echo ($info["username"]); ?></div>
                            <div class="user_info"><?php echo ($info["phonenum"]); ?></div>
                        </div>


                        <!--<div class="next_step rt">-->
                            <!--<img src="/public_html/Public/sick/img/right.png" class="img-responsive" alt=""/>-->
                        <!--</div>-->
                    </div>
                </a>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 clear_pd">
                <div class="height"></div>
            </div>
        </div>

        <!-- 分类-->
        <div class="row top_user_info">
            <div class="col-md-12 col-sm-12 col-xs-12 ">

                    <div class="subgroup fl">
                        <div class="left lf">修改分组</div>
                        <!--<div class="right rt">-->
                        <div class="right_img lf rt">
                            <img src="/public_html/Public/sick/img/right.png" class="img-responsive" alt=""/>
                        </div>

                            <div class="select rt">
                                <form action="/public_html/index.php/Home/Doctor/Patient_name_card/sid/25/haoyou/25.html" method="post">
                                    <input type="hidden" name="sickid" value="<?php echo ($sickId); ?>"/>
                                    <input type="hidden" name="t" value="<?php echo ($_GET['t']); ?>"/>
                                    <input type="hidden" name="haoyou" value="<?php echo ($_GET['haoyou']); ?>"/>
                                    <select name="groupname" class="text" style="text-align: right">
                                        <option value="1" <?php if($type == '3'): ?>selected<?php endif; ?>>我的患者</option>
                                        <option value="2" <?php if($type == '1'): ?>selected<?php endif; ?>>我的粉丝</option>
                                      <!--  <option value="3" <?php if($type == '2'): ?>selected<?php endif; ?>>咨询过我的人</option>-->
                                        <?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["listname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </form>
                            </div>
                        <!--</div>-->

                    </div>

                <!--<a href="" class="fl cl">-->
                    <!--<div class="subgroup fl clear_br">-->
                        <!--<div class="left lf">分组</div>-->
                        <!--<div class="right rt">-->
                            <!--<div class="right_img lf">-->
                                <!--<img src="/public_html/Public/sick/img/right.png" class="img-responsive" alt=""/>-->
                            <!--</div>-->
                        <!--</div>-->

                    <!--</div>-->
                <!--</a>-->
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 clear_pd">
                <div class="height"></div>
            </div>
        </div>

        <!--&lt;!&ndash; 患者信息&ndash;&gt;        >field('username,phonenum,sex,userage,height,weight')-->

        <div class="row top_user_info">
            <div class="col-md-12 col-sm-12 col-xs-12 ">
                <div class="subgroup fl">
                    <div class="left lf">性别</div>
                    <div class="right rt">
                        <div class="right_tx lf">
                            <?php if($info['sex'] == '1'): ?>男
                                <?php elseif($info['sex'] == '2'): ?>女
                                <?php else: ?> 用户未设置<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="subgroup fl">
                    <div class="left lf">年龄</div>
                    <div class="right rt">
                        <div class="right_tx lf"><?php echo ((isset($info['userage']) && ($info['userage'] !== ""))?($info['userage']):"用户未设置"); ?></div>

                    </div>
                </div>
                <div class="subgroup fl">
                    <div class="left lf">身高</div>
                    <div class="right rt">
                        <div class="right_tx lf"><?php echo ((isset($info['height']) && ($info['height'] !== ""))?($info['height']):"用户未设置"); ?></div>
                    </div>
                </div>
                <div class="subgroup fl clear_br">
                    <div class="left lf">体重</div>
                    <div class="right rt">
                        <div class="right_tx lf"><?php echo ((isset($info['weight']) && ($info['weight'] !== ""))?($info['weight']):"用户未设置"); ?></div>

                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 clear_pd">
                <div class="height"></div>
            </div>
        </div>
        <!-- 咨询历史-->
     <!--   <div class="row">
            <div class="col-md-12v col-sm-12 col-xs-12">
                <a href="img_consult.html" class="fl cl">
                    <div class="subgroup fl clear_br">
                        <div class="left lf" >咨询历史</div>
                        <div class="right rt">
                            <div class="right_img lf">
                                <img src="/public_html/Public/sick/img/right.png" class="img-responsive" alt=""/>
                            </div>
                        </div>

                    </div>
                </a>
            </div>
        </div>-->
    <button class="button" id="tijiao">提交</button>
</div>


</div>
<script src="/public_html/Public/sick/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/sick/js/bootstrap.js"></script>
<script>
    $('#tijiao').click(function(){
        $('form:first').submit();
    })
</script>
</body>

</html>