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
        <span class="label label-danger"><span id="tip">0</span>个新订单</span>
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
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo U('Delivery/update');?>">
            <input type="hidden" value="<?php echo ($list["id"]); ?>" name="id">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="border:1px solid #eeeeee;">
                    <div class="row" style="margin-top: 20px;">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">头像:</label>
                            <div class="col-lg-1">
                                <div class="fileInput left" id="upload-container" >
                                    <input type="hidden" value="<?php echo ($list["user_logo"]); ?>" name="old_user_logo">
                                    <input type="file" name="user_logo" id="upload"  class="upfile uplo" />
                                    <input class="upFileBtn uplo" type="button" value="上传图片" onclick="document.getElementById('upload').click()" />
                                </div>
                            </div>
                            <div class="col-lg-5 height-center text-danger" id="show_img">
                                <img src="/taiyou/<?php echo ($list["user_logo"]); ?>" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">名称:</label>
                            <div class="col-lg-4">
                                <input type="text"  class="form-control" value="<?php echo ($list["s_name"]); ?>" name="s_name" required>
                            </div>
                            <span class="col-lg-5 height-center text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">年龄:</label>
                            <div class="col-lg-4">
                                <input type="text"  class="form-control" maxlength="3" value="<?php echo ($list["age"]); ?>" name="age" onkeyup='this.value=this.value.replace(/\D/gi,"")'  required>
                            </div>
                            <span class="col-lg-5 height-center text-danger">只能输入数字</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">出生日期:</label>
                            <div class="col-lg-4">
                                <input type="text"  class="form-control" value="<?php echo ($list["birth_date"]); ?>"  name="birth_date" required>
                            </div>
                            <span class="col-lg-5 height-center text-danger">格式为2016-04-06</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">身份证号码:</label>
                            <div class="col-lg-4">
                                <input type="text" maxlength="18" value="<?php echo ($list["s_sfz"]); ?>"  class="form-control" name="s_sfz" onkeyup='this.value=this.value.replace(/\D/gi,"")'  required>
                            </div>
                            <span class="col-lg-5 height-center text-danger">只能输入数字</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">电话:</label>
                            <div class="col-lg-4">
                                <input type="text" value="<?php echo ($list["phone"]); ?>"  class="form-control" onkeyup='this.value=this.value.replace(/\D/gi,"")' name="phone"  maxlength="11" required>
                            </div>
                            <span class="col-lg-5 height-center text-danger">只能输入数字</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">账号:</label>
                            <div class="col-lg-4">
                                <input type="text"  class="form-control" value="<?php echo ($list["account_num"]); ?>" name="account_num" onkeyup='this.value=this.value.replace(/[^\w\.\/]/ig,"")' required>
                            </div>
                            <span class="col-lg-5 height-center text-danger">只能输入数字字母下划线</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">密码:</label>
                            <div class="col-lg-4">
                                <input type="text"  class="form-control" value="<?php echo ($list["password"]); ?>" onkeyup='this.value=this.value.replace(/[^\w\.\/]/ig,"")' name="password"  required>
                            </div>
                            <span class="col-lg-5 height-center text-danger">只能输入数字字母下划线</span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">选择店铺:</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="shop_id">
                                    <?php if(is_array($shop_list)): foreach($shop_list as $key=>$vo): if($vo['id'] == $list['shop_id']): ?><option selected value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["shop_name"]); ?></option>
                                            <?php else: ?>
                                            <option  value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["shop_name"]); ?></option><?php endif; endforeach; endif; ?>
                                </select>
                            </div>
                            <span class="col-lg-5 height-center text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">性别:</label>
                            <div class="col-lg-4">
                                <?php if($list['sex'] == 1): ?><input type="radio" checked name="sex" value="1" checked style="margin-top: 10px;">男
                                    <input type="radio"  name="sex" value="2" style="margin-top: 10px;">女
                                    <?php else: ?>
                                    <input type="radio" name="sex" value="1" checked style="margin-top: 10px;">男
                                    <input type="radio" checked name="sex" value="2" style="margin-top: 10px;">女<?php endif; ?>
                            </div>
                            <span class="col-lg-5 height-center text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">状态:</label>
                            <div class="col-lg-4">
                                <?php if($list['sex'] == 1): ?><input type="radio" checked name="status" value="1" checked style="margin-top: 10px;">正常
                                    <input type="radio"  name="status" value="2" style="margin-top: 10px;">冻结
                                    <?php else: ?>
                                    <input type="radio" name="status" value="1" checked style="margin-top: 10px;">正常
                                    <input type="radio" checked name="status" value="2" style="margin-top: 10px;">冻结<?php endif; ?>
                            </div>
                            <span class="col-lg-5 height-center text-danger"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 50px;">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-2">
                            <button class="btn btn-danger" style="width: 30%; margin-left:40px;">通过</button>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-danger" style="width: 30%;">不通过</button>
                        </div>
                        <span class="col-lg-5 height-center text-danger"></span>
                    </div>
                </div>
            </div>
    </div>
    </form>
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


    <script>
        $('.uplo').click(function(){
            $('#show_img').html('');
        })

        $(function()
        {
            $('.uplo').on('change',function (e)
            {
                var tmppath = window.webkitURL.createObjectURL(event.target.files[0]);
                var html = "";
                html+='<img src='+tmppath+'>';
                $("#show_img").html(html);
                audiojs.events.ready(function() {
                    audiojs.createAll();
                });
            });
        });
    </script>


</body>
</html>