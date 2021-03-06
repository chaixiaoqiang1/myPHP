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
                
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  shop_right">
                <div class="row">
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center">
                        <div class="row">
                            <a href="<?php echo U('User/index');?>">
                                <p class="shop_right_color">
                                    修改列表
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right">
                        <form class="form-horizontal" method="post" id="user-editor" enctype="multipart/form-data" action="<?php echo U('User/index');?>">
                            <div class="row form-group">
                                
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <!--<p><input class="shop_search_input" placeholder="输入用户名关键字" name="user_name" type="text"> <button>搜索</button></p>-->
<!--                                    <button>添加数据</button>-->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 shop_list">
                <div class="row">
                    <form action='/taiyou/index.php/Admin/User/edit/id/3.html' method="post" >
                    
                    <table class="table table-bordered">
                        <tr class="text-center">
                          <td class="active">姓名</td>
                          <td class="active"><input type="text" name="username" value="<?php echo ($info['username']); ?>" /></td>
                        </tr>
                        <!--<tr class="text-center">-->
                            <!--<td class="active">头像</td>->
                            <!--<td class="active"><input type="file" name="image" /></td>-->
                        <!--</tr>-->

                        <tr class="text-center">
                          <td class="active">性别</td>
                          <td class="active">
                              <?php if($info['sex'] == 0): ?><input type="radio" name="sex"  value="0" checked="checked" />男
                                  <input type="radio" name="sex"  value="1"  />女
                               <?php else: ?>
                                  <input type="radio" name="sex"  value="0"  />男
                                  <input type="radio" name="sex"  value="1" checked="checked" />女<?php endif; ?>
                          </td>
                        </tr>
                        
                        
                       <tr class="text-center">
                          <td class="active">科室</td>
                          <td class="active">
                              <!--<input type="text" name="office" value="<?php echo ($info['office']); ?>" />-->
                              <select name="office">
                                     <?php if(is_array($catinfo)): $i = 0; $__LIST__ = $catinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($info['office'] == $vo['id']): ?>selected="selected"<?php endif; ?> ><?php echo ($vo["catname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                              </select>

                          </td>
                        </tr>

                        <tr class="text-center">
                          <td class="active">执业编号</td>
                          <td class="active"><input type="text" name="practice" value="<?php echo ($info['practice']); ?>" /></td>
                        </tr>
                        
                         <tr class="text-center">
                          <td class="active">医生级别</td>
                          <td class="active">
                              <!--<input type="text" name="rank"  value="<?php echo ($info['rank']); ?>" />-->
                              <?php if($info['rank'] == 1): ?><input type="radio" name="rank" value="1" checked="checked">初级&nbsp;&nbsp;
                                      <input type="radio" name="rank" value="2" >中级&nbsp;&nbsp;
                                      <input type="radio" name="rank" value="3" >高级
                                  <elseif condition="$info['rank'] eq 2">
                                      <input type="radio" name="rank" value="1" >初级&nbsp;&nbsp;
                                      <input type="radio" name="rank" value="2" checked="checked" >中级&nbsp;&nbsp;
                                      <input type="radio" name="rank" value="3" >高级
                                  <?php else: ?>
                                      <input type="radio" name="rank" value="1" >初级&nbsp;&nbsp;
                                      <input type="radio" name="rank" value="2"  >中级&nbsp;&nbsp;
                                      <input type="radio" name="rank" value="3" checked="checked" >高级<?php endif; ?>
                          </td>
                        </tr>
                        
                        <tr class="text-center">
                          <td class="active">联系方式</td>
                          <td class="active"><input type="text" name="iphone"  value="<?php echo ($info['iphone']); ?>" /></td>
                        </tr>
                        
                         <tr class="text-center">
                          <td class="active">擅长领域</td>
                          <td class="active">
                               <textarea rows="3"  name='speciality' class="form-control">
                                        <?php echo ($info['practice']); ?>
                               </textarea>
                          </td>
                        </tr>
                        
                        <tr class="text-center">
                          <td class="active">个人简介</td>
                          <td class="active">
                               <textarea rows="3"  name='intro' class="form-control" style="text-align: left;">
                                   <?php echo ($info['intro']); ?>
                               </textarea>
                          </td>
                        </tr>
                        
                        
<!--                        <tr class="text-center">
                          <td class="active">添加时间</td>
                          <td class="active"><input type="text" name="addtime" /></td>
                        </tr>-->

                    <tr class="text-center">
                         <td><input type='submit' value='提交' class="btn btn-default"></td>
                    </tr>

                    </table>
                        
                    </form>
                </div>
            </div>
         <!--   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  text-center" id="page" style="margin-top: 20px;">
                <?php echo ($page); ?>
            </div>-->
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