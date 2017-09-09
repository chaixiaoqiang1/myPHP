<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>我的患者</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <link href="/public_html/Public/sick/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/sick/css/my_patient.css"/>
    <script src="/public_html/Public/sick/js/jquery-1.11.3.js"></script>
    <script src="/public_html/Public/sick/js/bootstrap.js"></script>
    <script src="/public_html/Public/sick/js/my_patient.js"></script>
</head>
<body>
<div class="container">
    <div class="row seach">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="seach_left fl lf">
                <div class="seach_img fl lf">
                    <img src="/public_html/Public/sick/img/seach.png" class="img-responsive lf" alt=""/>
                </div>
                <input type="text" class="lf seach_tx" placeholder="搜索患者"/>
            </div>
            <div class="add rt" style="color: #A9A9A9;line-height: 52px">添加分组</div>
        </div>
    </div>
    <div class="row dropdown_menu">
        <div class="col-md-12 col-sm-12 col-xs-12 clear_pd">
            <!--分组-->
                <div class="parent_container">
                <div class="panel-title fl">
                    <div class="down_img lf">
                        <img src="/public_html/Public/sick/img/right_img.png" class="img-responsive cut_way" alt=""/>
                    </div>
                    <div class="lf sufferer">我的患者</div>
                    <div class="number rt"><?php echo ($info['acceptsick_id']['count']); ?></div>
                </div>
                    <?php if(is_array($info['acceptsick_id'])): $k = 0; $__LIST__ = $info['acceptsick_id'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($k % 2 );++$k; if($k != 'count'): ?><a href="<?php echo U('doctor/Patient_name_card',array('sid'=> $v1['id'],'t'=> 3));?>">
                                  <div class="ensconce fl">
                                      <div class="user_name lf">
                                            <img src="<?php echo ($v1["icon"]); ?>" class="img-responsive img-circle" alt=""/>
                                      </div>
                                      <div class="user_info lf"><?php echo ($v1['username']); ?></div>
                                  </div>
                            </a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <div class="parent_container">
                <div class="panel-title fl">
                    <div class="down_img lf">
                        <img src="/public_html/Public/sick/img/right_img.png" class="img-responsive cut_way" alt=""/>
                    </div>
                    <div class="lf sufferer">我的粉丝</div>
                    <div class="number rt"><?php echo ($info['sick_id']['count']); ?></div>
                </div>
                <?php if(is_array($info['sick_id'])): $i = 0; $__LIST__ = $info['sick_id'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Doctor/Patient_name_card',array('sid'=>$vo['id'],'t'=> 1));?>">
                    <div class="ensconce fl">
                        <div class="user_name lf">
                            <img src="<?php echo ($vo["icon"]); ?>" class="img-responsive img-circle" alt=""/>
                        </div>
                        <div class="user_info lf"><?php echo ($vo['username']); ?></div>
                    </div>
                </a><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
         <!--   <div class="parent_container consult">
                <div class="panel-title fl">
                    <div class="down_img lf">
                        <img src="/public_html/Public/sick/img/right_img.png" class="img-responsive cut_way" alt=""/>
                    </div>
                    <div class="lf sufferer">咨询过我的人</div>
                    <div class="number rt"><?php echo ($count_arr[1]); ?></div>
                </div>
                <?php if(is_array($userconsultid)): $i = 0; $__LIST__ = $userconsultid;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Doctor/Patient_name_card',array('sid'=>$vo['id'],'t'=> 2));?>">
                    <div class="ensconce fl">
                        <div class="user_name lf">
                            <img src="/public_html/Public/sick/img/doctor.jpg" class="img-responsive img-circle" alt=""/>
                        </div>
                        <div class="user_info lf"><?php echo ($vo["username"]); ?></div>
                    </div>
                </a><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>-->

            <?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="parent_container" id="xinjia">
                    <div class="panel-title fl">
                        <div class="down_img lf">
                            <img src="/public_html/Public/sick/img/right_img.png" class="img-responsive cut_way" alt=""/>
                        </div>
                        <div class="lf sufferer"><?php echo ($vo["listname"]); ?></div>
                        <div class="number rt"><?php echo ($vo['count']); ?></div>
                    </div>
                    <?php if(is_array($vo['s_id'])): $i = 0; $__LIST__ = $vo['s_id'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Doctor/Patient_name_card',array('sid'=>$v1['id'],'haoyou'=> $vo['id'] ));?>">
                            <div class="ensconce fl">
                                <div class="user_name lf">
                                    <img src="<?php echo ($v1['icon']); ?>" class="img-responsive img-circle" alt=""/>
                                </div>
                                <div class="user_info lf"><?php echo ($v1['username']); ?></div>
                            </div>
                        </a><?php endforeach; endif; else: echo "" ;endif; ?>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>
<form action="/public_html/index.php/Home/Doctor/my_patient.html" method="post">
    <div class="pop">
        <div class="parent">
                <div class="fl tx lf">分组名称：</div>
                <input type="text" name="listname" class="lf ipt"/>
            <div class="button">
                <div class="confirm lf" id="tijiao">确定</div>
                <div class="cancel lf">取消</div>
            </div>
        </div>
    </div>
</form>
<div class="window"></div>
<script>
  $('#tijiao').click(function(){
      if(!$("input[name='listname']").val()){
          alert('请输入分组名');
          return false;
      }
      $('form:first').submit();
  })
</script>
</body>
</html>