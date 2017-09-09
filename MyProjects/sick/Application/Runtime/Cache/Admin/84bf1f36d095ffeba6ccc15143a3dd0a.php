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
                
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
             <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 shop_right">
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 ">
                            <!--<a href="<?php echo U('User/index');?>">-->
                                <p class="shop_right_color">
                                     用户信息列表
                                </p>
                            <!--</a>-->
                    </div>
                  <!--  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="text-align: left;">
                        <a href="<?php echo U('Sick/insertUser');?>">
                            <p class="shop_right_color">
                                添加用户
                            </p>
                        </a>
                    </div>-->
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pull-right">
                        <form class="form-horizontal" method="post" id="user-editor" enctype="multipart/form-data" action="<?php echo U('Sick/searchsick');?>" style="margin-top:5px">
                            <div class="row form-group">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p><input class="shop_search_input" placeholder="输入用户名关键字" name="username" type="text"> <button>搜索</button></p>
                                </div>
                            </div>
                        </form>
                    </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 shop_list">
                <div class="row">
                    <table class="table table-bordered">
                        <tr class="text-center">
                            
                            <td class="active">序号</td>
                            <td class="active">用户名</td>
                            <td class="active">性别</td>
                            <td class="active">身高</td>
                            <td class="active">年龄</td>
                            <td class="active">体重</td>
                            <td class="active">联系方式</td>
                            <td class="active">家庭住址</td>
                            <td class="active">添加时间</td>
                            <!--<td class="active">服务历史</td>-->
                            <td class="active">详细信息</td>
                            <td class="active">状态</td>
                            <td class="active">操作</td>
                        </tr>

                        <?php if(is_array($list)): foreach($list as $k=>$vo): ?><tr class="text-center">
                                    <td class=""><?php echo ($vo["id"]); ?></td>
                                    <td class=""><?php echo ($vo["username"]); ?></td>
                                    <td class="">
                                        <?php if($vo['sex'] == 2): ?>女
                                            <?php else: ?>
                                            男<?php endif; ?>
                                    </td>
                                    <td class="">
                                        <?php echo ($vo["height"]); ?>
                                    </td>
                                    <td class="">
                                        <?php echo ($vo["userage"]); ?>
                                    </td>
                                    <td class="">
                                        <?php echo ($vo["weight"]); ?>
                                    </td>
                                    <td class="">
                                        <?php echo ($vo["phonenum"]); ?>
                                    </td>
                                      <td class="">
                                        <?php echo ($vo["address"]); ?>
                                    </td>
                                    </td>
                                    <td class="">
                                        <?php echo date("Y-m-d",$vo['addtime']);?>
                                    </td>

                             <!--       <td class="info">
                                        <a href="<?php echo U('Sick/historylist',array('id' => $vo['id']));?>"><span class="label btn-info">我的医生</span></a>
                                    </td>-->

                                    <td class="">
                                        <a href="<?php echo U('Sick/sickinfo',array('id' => $vo['id']));?>"><span class="label btn-success">更多内容</span></a>
                                    </td>
                                    <td class="">
                                        <?php if($vo['is_status'] == 1): ?>正常
                                            <?php elseif($vo['is_status'] == 2): ?>
                                            冻结<?php endif; ?>
                                    </td>
                                    <td class="">
                                        <?php if($vo['is_status'] == 1): ?><a href="<?php echo U('Sick/sickstatus',array('id'=>$vo['id']));?>">
                                                <span class="label btn-warning">冻结</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo U('Sick/sickstatus',array('id'=>$vo['id']));?>">
                                                <span class="label btn-success">正常</span>
                                            </a><?php endif; ?>
                                        <a href="<?php echo U('Sick/saveUser',array('id'=>$vo['id']));?>">
                                            <span class="label btn-primary"> 修改</span>
                                        </a>

                                        <a href="<?php echo U('Sick/delUser',array('id'=>$vo['id']));?>">
                                            <span class="label btn-danger">删除</span>
                                        </a>
                                       <!--  <a href="<?php echo U('User/doctoredit',array('id'=>$vo['id']));?>">
                                            <span class="label btn-primary">编辑</span>
                                        </a> -->
                                    </td>
                                </tr><?php endforeach; endif; ?>
                    </table>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  text-center" id="page" style="margin-top: 20px;">
                <?php echo ($page); ?>
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
  
</body>
</html>