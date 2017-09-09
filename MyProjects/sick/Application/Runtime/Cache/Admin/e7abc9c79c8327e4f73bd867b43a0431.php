<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<meta charset="utf-8">
<!-- Viewport Metatag -->
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo ($title); ?></title>
<style>
    .btn{
        height:40px;
    }
</style>

    <link rel="stylesheet" href="/public_html/Public/doctor/css/chat.css"/>
    <!-- Plugin Stylesheets first to ease overrides -->
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/plugins/colorpicker/colorpicker.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/custom-plugins/wizard/wizard.css" media="screen">
    <!-- Required Stylesheets -->
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/bootstrap/css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/fonts/ptsans/stylesheet.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/fonts/icomoon/style.css" media="screen">

    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/mws-style.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/icons/icol16.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/icons/icol32.css" media="screen">

    <!-- Demo Stylesheet -->
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/demo.css" media="screen">

    <!-- jQuery-UI Stylesheet -->
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/jui/css/jquery.ui.all.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/jui/jquery-ui.custom.css" media="screen">

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/mws-theme.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/Template/css/themer.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/public_html/Public/style/page.css" media="screen">
    <style>
        #page a.num,#page .current{padding:5px;font-size:18px;}
    </style>

</head>
<!-- Themer End -->
<body>
<!-- Themer End -->
<div id="mws-header" class="clearfix">

    <!-- Logo Container -->
    <div id="mws-logo-container">

        <!-- Logo Wrapper, images put within this wrapper will always be vertically centered -->
        <div id="mws-logo-wrap">
            <img src="/public_html/Public/images/mws-logo.png" alt="mws admin">
        </div>
    </div>
    <div style="float: left; padding: 9px 0;color:#fff;">
        <!--<span class="label label-danger"><span id="tip">0</span>个新订单</span>-->
    </div>

    <!-- User Tools (notifications, logout, profile, change password) -->
    <div id="mws-user-tools" class="clearfix">
        <!-- User Information and functions section -->
        <div id="mws-user-info" class="mws-inset">

            <!-- User Photo -->
            <div id="mws-user-photo">
                <!--<img src="/public_html/Public/example/profile.jpg" alt="User Photo">-->
            </div>

            <!-- Username and Functions -->
            <div id="mws-user-functions">
                <div id="mws-username">
                    你好,<?php echo ($_SESSION['admin_name']); ?>
                </div>
                <ul>
                    <!--<li><a href="javascript:void(0)">-->
                       <!--</a></li>-->
                    <!--<li><a href="<?php echo U("Aedit/gedit");?>">更改密码</a></li>-->
                    <li><a href="<?php echo U("Public/logout");?>">退出</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Start Main Wrapper -->
<div id="mws-wrapper">

    <!-- Necessary markup, do not remove -->
<div id="mws-sidebar-stitch"></div>
<div id="mws-sidebar-bg"></div>

<!-- Sidebar Wrapper -->
<div id="mws-sidebar">

    <!-- Hidden Nav Collapse Button -->
    <div id="mws-nav-collapse">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div id="mws-navigation">
        <ul>
            <?php if(is_array($catgoryTop)): $i = 0; $__LIST__ = $catgoryTop;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li>
                <a href="/public_html/index.php/<?php echo ($v["auth_path"]); ?>"><i class="<?php echo ($v["ico"]); ?>"></i><?php echo ($v["title"]); ?></a>
                <ul class="closed" style="overflow: hidden;">
                    <?php if(is_array($v[_child])): $i = 0; $__LIST__ = $v[_child];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><li><a href="/public_html/index.php/<?php echo ($v1["auth_path"]); ?>"><?php echo ($v1["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div>











    <!-- Main Container Start -->
    <div id="mws-container" class="clearfix">

        <!-- Inner Container Start -->
        <div class="pull-left" style="width:100%;">
            <!-- Statistics Button Container -->
            <div class="mws-stat-container clearfix">
                <link href="/public_html/Public/style/bootstrap.min.css" rel="stylesheet">
                <link href="/public_html/Public/style/admin.min.css" rel="stylesheet">
                
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div class="col-xs-3 col-xs-12 col-sm-12 col-md-12 col-lg-12  shop_right">
                <div class="row">
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center">
                        <div class="row">
                            <a href="<?php echo U('User/index');?>">
                                <p class="shop_right_color">
                                    &nbsp;医生详细信息
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right">
                        <form class="form-horizontal" method="post" id="user-editor" enctype="multipart/form-data" action="<?php echo U('User/index');?>">
                            <div class="row form-group">
<!--                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p><input class="shop_search_input" placeholder="输入用户名关键字" name="user_name" type="text"> <button>搜索</button></p>
                                </div>-->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--//新加的按钮-->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 shop_list">
                <a href="<?php echo U('Service/servicelist',array('id'=> $info['id']));?>"><button type="button" class="btn btn-default">服务历史</button></a>
                <a href="<?php echo U('Service/imagetextlist',array('id'=> $info['id']));?>"><button type="button" class="btn btn-primary">图文咨询</button></a>
                <a href="<?php echo U('Service/orderlist',array('id'=> $info['id']));?>"><button type="button" class="btn btn-success">预约咨询</button></a>
                <!--<a href="<?php echo U('Service/threeidea',array('id'=> $info['id']));?>"><button type="button" class="btn btn-warning">第三方建议</button></a>-->
                <a href="<?php echo U('Service/sickcomment',array('id'=> $info['id']));?>"><button type="button" class="btn btn-danger">用户评论</button></a>
               <!-- <a href="<?php echo U('Service/allfriend',array('id'=> $info['id']));?>"><button type="button" class="btn btn-info">朋友圈</button></a>-->
                <a href="<?php echo U('Service/orderset',array('id'=> $info['id']));?>"><button type="button" class="btn btn-info">预约设置信息</button></a>
                <!--<button type="button" class="btn btn-link">（链接）Link</button>-->
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 shop_list">
                <div class="row">
                    <table class="table table-striped">
                        <tr>
                            <th>医生简介</th>
                            <td><?php echo ($info['intro']); ?></td>
                        </tr>
                        <tr>
                            <th>擅长领域</th>
                            <td><?php echo ($info['speciality']); ?></td>
                        </tr>

                        <!--//后加字段-->
                        <tr>
                            <th>服务费用</th>
                            <td><?php echo ($info['serverprice']); ?>元</td>
                        </tr>
                        <tr>
                            <th>应答时间</th>
                            <td><?php echo ($info['responsetime']); ?></td>
                        </tr>
                        <tr>
                            <th>服务人数</th>
                            <td><?php echo ($info['servernum']); ?>人</td>
                        </tr>
                        <tr>
                            <th>评价人数</th>
                            <td><?php echo ($info['evaluatenum']); ?>人</td>
                        </tr>
                        <tr>
                            <th>综合评分</th>
                            <td><?php echo ($info['zscore']); ?>分</td>
                        </tr>
                        <tr>
                            <th>图文咨询费用</th>
                            <td><?php echo ($info['tuwenpirce']); ?>元</td>
                        </tr>
                        <tr>
                            <th>预约就诊费用</th>
                            <td><?php echo ($info['orderprice']); ?>元</td>
                        </tr>
                        <tr>
                            <th>关注人数</th>
                            <td><?php echo ($info['attentionnum']); ?></td>
                        </tr>
                        <tr>
                            <th>购买服务次数</th>
                            <td><?php echo ($info['buyservernum']); ?>次</td>
                        </tr>
                        <tr>
                            <th>接收咨询人数</th>
                            <td><?php echo ($info['receiveconsultnum']); ?>人</td>
                        </tr>
                        <tr>
                            <th>学历</th>
                            <td><?php echo ($info['education']); ?></td>
                        </tr>
                        <tr>
                            <th>邮箱</th>
                            <td><?php echo ($info['mailbox']); ?></td>
                        </tr>
                        <tr>
                            <th>专业</th>
                            <td><?php echo ($info['major']); ?></td>
                        </tr>
                        <tr>
                            <th>资格证编号</th>
                            <td><?php echo ($info['qualification']); ?></td>
                        </tr>

                        <tr>
                            <th>身份证号</th>
                            <td><?php echo ($info['identity']); ?></td>
                        </tr>
                        <tr>
                            <th>执照图片</th>
                            <td><img src="/public_html/Uploads/<?php echo ($info['licenseimg']); ?>" style="height: 120px; width: 120px;" id="img01"></td>
                        </tr>
                        <!--<tr>
                            <th>接收过治疗的患者</th>
                            <td><?php echo ($info['acceptsick_id']); ?></td>
                        </tr>-->

                        <tr>
                            <th>免费咨询状态</th>
                            <td>
                                <?php if($info['freeconsult'] == 1): ?>免费
                                    <?php else: ?>不免费<?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>图文咨询状态</th>
                            <td>
                                <?php if($info['is_picture'] == 1): ?>已开通
                                    <?php else: ?>已关闭<?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>预约就诊状态</th>
                            <td>
                                <?php if($info['is_orderstatus'] == 1): ?>已开通
                                    <?php else: ?>已关闭<?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>二维码</th>
                            <td><img src="/public_html/<?php echo ($info['twocode']); ?>" style="height: 120px; width: 120px;"></td>
                        </tr>

                    </table>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  text-center" id="page" style="margin-top: 20px;">
                <?php echo ($page); ?>
            </div>
        </div>
    </div>

            </div>
            <!-- Panels End -->
        </div>
        <!-- Inner Container End -->

        <!-- Footer -->

        <div class="col-lg-12 col-md-12" style="margin-top: 5%;">
            <div class="navbar-fixed-bottom" style="  margin: 0 15%;text-align: center; padding: 16px 0;bottom: 0;left: 206px;right: 0;">
                Copyright Your Website 2016. All Rights Reserved.
            </div>
        </div>

    </div>
    <!-- Main Container End -->

</div>

<!-- JavaScript Plugins -->
<script src="/public_html/Public/Template/js/libs/jquery-1.8.3.min.js"></script>
<script src="/public_html/Public/Template/js/libs/jquery.mousewheel.min.js"></script>
<script src="/public_html/Public/Template/js/libs/jquery.placeholder.min.js"></script>
<script src="/public_html/Public/Template/custom-plugins/fileinput.js"></script>

<!-- jQuery-UI Dependent Scripts -->
<script src="/public_html/Public/Template/jui/js/jquery-ui-1.9.2.min.js"></script>
<script src="/public_html/Public/Template/jui/jquery-ui.custom.min.js"></script>
<script src="/public_html/Public/Template/jui/js/jquery.ui.touch-punch.js"></script>

<!-- Plugin Scripts -->
<script src="/public_html/Public/Template/plugins/datatables/jquery.dataTables.min.js"></script>
<!--[if lt IE 9]>
<script src="/public_html/Public/Template/js/libs/excanvas.min.js"></script>
<![endif]-->
<script src="/public_html/Public/Template/plugins/flot/jquery.flot.min.js"></script>
<script src="/public_html/Public/Template/plugins/flot/plugins/jquery.flot.tooltip.min.js"></script>
<script src="/public_html/Public/Template/plugins/flot/plugins/jquery.flot.pie.min.js"></script>
<script src="/public_html/Public/Template/plugins/flot/plugins/jquery.flot.stack.min.js"></script>
<script src="/public_html/Public/Template/plugins/flot/plugins/jquery.flot.resize.min.js"></script>
<script src="/public_html/Public/Template/plugins/colorpicker/colorpicker-min.js"></script>
<script src="/public_html/Public/Template/plugins/validate/jquery.validate-min.js"></script>
<script src="/public_html/Public/Template/custom-plugins/wizard/wizard.min.js"></script>

<!-- Core Script -->
<script src="/public_html/Public/Template/bootstrap/js/bootstrap.min.js"></script>
<script src="/public_html/Public/Template/js/core/mws.js"></script>

<!-- Themer Script (Remove if not needed) -->
<script src="/public_html/Public/Template/js/core/themer.js"></script>

<!-- Demo Scripts (remove if not needed) -->
<script src="/public_html/Public/Template/js/demo/demo.dashboard.js"></script>
<?php if($_SESSION['admin_id']== 1): ?><script>
        var a = setInterval(ajaxReturn,5000);
        window.setTimeout(ajaxReturn,0);
        function ajaxReturn(){
            $.get("<?php echo U('Admin/Order/tip');?>",function(data){
                $('#tip').html(data);
            });
        }
</script><?php endif; ?>

    <script src="/public_html/Public/script/jquery-2.1.1.min.js"></script>
    <script src="/public_html/Public/script/bootstrap.min.js"></script>
    <script>
        $("#img01").hover(
                function(){
                    $(this).animate({'width':'500px','height':'500px'})
                },
                function(){
                    $(this).animate({'width':'120px','height':'120px'})
                }
        )
    </script>

</body>
</html>