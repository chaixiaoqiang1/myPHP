<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<meta charset="utf-8">
<!-- Viewport Metatag -->
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo ($title); ?></title>

    <!-- Plugin Stylesheets first to ease overrides -->
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/plugins/colorpicker/colorpicker.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/custom-plugins/wizard/wizard.css" media="screen">
    <!-- Required Stylesheets -->
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/bootstrap/css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/fonts/ptsans/stylesheet.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/fonts/icomoon/style.css" media="screen">

    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/mws-style.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/icons/icol16.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/icons/icol32.css" media="screen">

    <!-- Demo Stylesheet -->
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/demo.css" media="screen">

    <!-- jQuery-UI Stylesheet -->
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/jui/css/jquery.ui.all.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/jui/jquery-ui.custom.css" media="screen">

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/mws-theme.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/taiyou/Public/Template/css/themer.css" media="screen">
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
            <img src="/taiyou/Public/images/mws-logo.png" alt="mws admin">
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
                <!--<img src="/taiyou/Public/example/profile.jpg" alt="User Photo">-->
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
                <a href="/taiyou/index.php/<?php echo ($v["name"]); ?>"><i class="<?php echo ($v["ico"]); ?>"></i><?php echo ($v["title"]); ?></a>
                <ul class="closed" style="overflow: hidden;">
                    <?php if(is_array($v[_child])): $i = 0; $__LIST__ = $v[_child];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><li><a href="/taiyou/index.php/<?php echo ($v1["name"]); ?>"><?php echo ($v1["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>

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
                <link href="/taiyou/Public/style/bootstrap.min.css" rel="stylesheet">
                <link href="/taiyou/Public/style/admin.min.css" rel="stylesheet">
                
        <!--<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">-->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  shop_right">
                <div class="row">
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center">
                        <div class="row">
                            <a href="<?php echo U('User/index');?>">
                                <p class="shop_right_color">
                                    医生列表
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right">
<!--                        <form class="form-horizontal" method="post" id="user-editor" enctype="multipart/form-data" action="<?php echo U('User/index');?>">
                            <div class="row form-group">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p><input class="shop_search_input" placeholder="输入用户名关键字" name="user_name" type="text"> <button>搜索</button></p>
                                </div>
                            </div>
                        </form>-->

 <a href="<?php echo U('User/add');?>"><button type="button" class="btn btn-primary">添加医生</button></a>

    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 shop_list">
                <div class="row">
                    <table class="table table-bordered">
                        <tr class="text-center">
                          <td class="active">编号</td>
                            <td class="active">用户名</td>
                            <td class="active">头像</td>
                            <td class="active">所属科室</td>
                            <td class="active">医师职称</td>
                            <td class="active">执业编号</td>
                            <td class="active">性别</td>
                            <td class="active">联系方式</td>
                            <td class="active">添加时间</td>
                            <td class="active">详细信息</td>
                            <td class="active">操作</td>
                        </tr>
                        <?php if(is_array($list)): $v = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($v % 2 );++$v;?><tr class="text-center">
                                  <td class="info"><?php echo ($v); ?></td>
                                    <td class="info"><?php echo ($vo["username"]); ?></td>
                                    <td class="info">
                       <img src="/taiyou/Uploads/<?php echo ($vo["image"]); ?>" alt="" style="height: 30px; width: 30px; border-radius: 15px;" />
                                    </td>

                                    <td class="info">
                                        <?php echo ($vo["office"]); ?>
                                    </td>
                                    <td class="info">
                                    <?php if($vo['rank'] == 1): ?>初级
                                        <elseif condition="$vo['rank'] eq 2">
                                            中级
                                        <?php else: ?>
                                            高级<?php endif; ?>

                                    </td>

                                    <td class="info">
                                        <?php echo ($vo["practice"]); ?>
                                    </td>

                                    <td class="info">
                                        <?php if($vo['sex'] == 0): ?>男
                                        <?php else: ?>
                                            女<?php endif; ?>
                                    </td>


                                    <td class="info"><?php echo ($vo["iphone"]); ?></td>

                                    <td class="info"><?php echo (date("y-m-d",$vo["addtime"])); ?></td>
                                    <td class="info"><a href="<?php echo U('User/infolist',array('id' => $vo['id']));?>"><span class="label btn-success">更多内容</span></a></td>
                                    <td class="info">
                                        <?php if($vo['status'] == 1): endif; ?>
                                        <a href="<?php echo U('User/edit',array('id'=> $vo['id']));?>"><span class="label btn-warning">修改</span></a>&nbsp;&nbsp;
										<a href="<?php echo U('User/del',array('id'=> $vo['id']));?>"><span class="label btn-danger">删除</span></a>&nbsp;&nbsp;
										<!--<a href="">审核</a>-->
									</td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
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
        <div id="mws-footer" >
            Copyright Your Website 2016. All Rights Reserved.
        </div>

    </div>
    <!-- Main Container End -->

</div>

<!-- JavaScript Plugins -->
<script src="/taiyou/Public/Template/js/libs/jquery-1.8.3.min.js"></script>
<script src="/taiyou/Public/Template/js/libs/jquery.mousewheel.min.js"></script>
<script src="/taiyou/Public/Template/js/libs/jquery.placeholder.min.js"></script>
<script src="/taiyou/Public/Template/custom-plugins/fileinput.js"></script>

<!-- jQuery-UI Dependent Scripts -->
<script src="/taiyou/Public/Template/jui/js/jquery-ui-1.9.2.min.js"></script>
<script src="/taiyou/Public/Template/jui/jquery-ui.custom.min.js"></script>
<script src="/taiyou/Public/Template/jui/js/jquery.ui.touch-punch.js"></script>

<!-- Plugin Scripts -->
<script src="/taiyou/Public/Template/plugins/datatables/jquery.dataTables.min.js"></script>
<!--[if lt IE 9]>
<script src="/taiyou/Public/Template/js/libs/excanvas.min.js"></script>
<![endif]-->
<script src="/taiyou/Public/Template/plugins/flot/jquery.flot.min.js"></script>
<script src="/taiyou/Public/Template/plugins/flot/plugins/jquery.flot.tooltip.min.js"></script>
<script src="/taiyou/Public/Template/plugins/flot/plugins/jquery.flot.pie.min.js"></script>
<script src="/taiyou/Public/Template/plugins/flot/plugins/jquery.flot.stack.min.js"></script>
<script src="/taiyou/Public/Template/plugins/flot/plugins/jquery.flot.resize.min.js"></script>
<script src="/taiyou/Public/Template/plugins/colorpicker/colorpicker-min.js"></script>
<script src="/taiyou/Public/Template/plugins/validate/jquery.validate-min.js"></script>
<script src="/taiyou/Public/Template/custom-plugins/wizard/wizard.min.js"></script>

<!-- Core Script -->
<script src="/taiyou/Public/Template/bootstrap/js/bootstrap.min.js"></script>
<script src="/taiyou/Public/Template/js/core/mws.js"></script>

<!-- Themer Script (Remove if not needed) -->
<script src="/taiyou/Public/Template/js/core/themer.js"></script>

<!-- Demo Scripts (remove if not needed) -->
<script src="/taiyou/Public/Template/js/demo/demo.dashboard.js"></script>
<?php if($_SESSION['admin_id']== 1): ?><script>
        var a = setInterval(ajaxReturn,5000);
        window.setTimeout(ajaxReturn,0);
        function ajaxReturn(){
            $.get("<?php echo U('Admin/Order/tip');?>",function(data){
                $('#tip').html(data);
            });
        }
</script><?php endif; ?>

    <script src="/taiyou/Public/script/jquery-2.1.1.min.js"></script>
    <script src="/taiyou/Public/script/bootstrap.min.js"></script>

</body>
</html>