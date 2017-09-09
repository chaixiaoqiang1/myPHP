<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>网站后台管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="/maijia/Public/new/assets/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/maijia/Public/new/css/style.css"/>
    <link href="/maijia/Public/new/assets/css/codemirror.css" rel="stylesheet">
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/ace.min.css"/>
    <link rel="stylesheet" href="/maijia/Public/new/font/css/font-awesome.min.css"/>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/ace-ie.min.css"/>
    <![endif]-->
    <link rel="stylesheet" href="/maijia/Public/new/css/style.css"/>
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/ace.min.css"/>
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/font-awesome.min.css"/>

    <link rel="stylesheet" href="/maijia/Public/new/assets/css/font-awesome.min.css"/>
    <!--[if IE 7]>
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/font-awesome-ie7.min.css"/>
    <![endif]-->
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/ace.min.css"/>
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/ace-rtl.min.css"/>
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/ace-skins.min.css"/>
    <link rel="stylesheet" href="/maijia/Public/new/css/style.css"/>
    <link rel="stylesheet" href="/maijia/Public/style/page.css"/>
    <link rel="stylesheet" href="/maijia/Public/new/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
    <link href="/maijia/Public/new/Widget/icheck/icheck.css" rel="stylesheet" type="text/css"/>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/maijia/Public/new/assets/css/ace-ie.min.css"/>
    <![endif]-->

    <script src="/maijia/Public/new/js/jquery-1.9.1.min.js"></script>


    <script src="/maijia/Public/new/assets/js/ace-extra.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/maijia/Public/new/assets/js/html5shiv.js"></script>
    <script src="/maijia/Public/new/assets/js/respond.min.js"></script>
    <![endif]-->
    <!--[if !IE]> -->
    <!-- <![endif]-->
    <!--[if IE]>

    <![endif]-->

    <script src="/maijia/Public/new/assets/js/bootstrap.min.js"></script>
    <script src="/maijia/Public/new/assets/js/typeahead-bs2.min.js"></script>
    <!--[if lte IE 8]>
    <script src="/maijia/Public/new/assets/js/excanvas.min.js"></script>
    <![endif]-->
   <script src="/maijia/Public/new/assets/js/ace-elements.min.js"></script>
    <script src="/maijia/Public/new/assets/js/jquery.dataTables.min.js"></script>
    <script src="/maijia/Public/new/assets/js/jquery.dataTables.bootstrap.js"></script>
    <script type="text/javascript" src="/maijia/Public/new/js/H-ui.js"></script>
    <script type="text/javascript" src="/maijia/Public/new/js/H-ui.admin.js"></script>
    <script src="/maijia/Public/new/js/lrtk.js" type="text/javascript"></script>
    <script type="text/javascript" src="/maijia/Public/new/Widget/zTree/js/jquery.ztree.all-3.5.min.js"></script>
    <script src="/maijia/Public/new/assets/js/ace.min.js"></script>
    <script src="/maijia/Public/new/assets/layer/layer.js" type="text/javascript"></script>
    <script src="/maijia/Public/new/assets/laydate/laydate.js" type="text/javascript"></script>
    <script type="text/javascript" src="/maijia/Public/new/Widget/swfupload/swfupload.js"></script>
    <script type="text/javascript" src="/maijia/Public/new/Widget/swfupload/swfupload.queue.js"></script>
    <script type="text/javascript" src="/maijia/Public/new/Widget/swfupload/swfupload.speed.js"></script>
    <script type="text/javascript" src="/maijia/Public/new/Widget/swfupload/handlers.js"></script>
    <script src="/maijia/Public/new/js/common.js"></script>
    <style>
        .icon-close:before {
            content: "\f00d";
        }
        #page span.rows {
            margin-right: 5px;
            display: inline-block;
            font-size: 18px;
            font-weight: bolder;
            border: 1px solid #999999;
            color: #999999;
            width: 120px;
            height: 30px;
            text-align: center;
        }
    </style>
</head>
<script type="text/javascript">window.jQuery || document.write("<script src='/maijia/Public/new/assets/js/jquery-1.10.2.min.js'>"+"<"+"script>");</script>
<![endif]-->
<script type="text/javascript">
    if("ontouchend" in document) document.write("<script src='/maijia/Public/new/assets/js/jquery.mobile.custom.min.js'>"+"<"+"script>");
</script>
<body>
<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>
    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <img src="/maijia/Public/new/images/logo.png">
                </small>
            </a>
        </div>
        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <span  class="time"><em id="time"></em></span><span class="user-info"><small>欢迎光临,</small><?php echo (session('mj_admin_name')); ?></span>
                        <i class="icon-caret-down"></i>
                    </a>
                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li><a href="<?php echo U('Public/logout');?>" id=""><i class="icon-off"></i>退出</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main-container" id="main-container">
    <script type="text/javascript">
        try{ace.settings.check('main-container' , 'fixed')}catch(e){}
    </script>
    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>
        <div class="sidebar" id="sidebar">
    <script type="text/javascript">
        try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
    </script>
    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
            网站后台管理系统
        </div>
        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>
            <span class="btn btn-info"></span>
            <span class="btn btn-warning"></span>
            <span class="btn btn-danger"></span>
        </div>
    </div><!-- #sidebar-shortcuts -->
    <ul class="nav nav-list" id="nav_list">
        <li class="home"><a href="<?php echo U('Index/index');?>" name="home.html" class="iframeurl" title=""><i class="icon-dashboard"></i><span class="menu-text"> 系统首页 </span></a></li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-picture "></i><span class="menu-text">图片管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Banner/index');?>" name="advertising.html" title="图片列表" class="iframeurl"><i class="icon-double-angle-right"></i>图片列表</a></li>
                <li class="home"><a href="<?php echo U('Banner/add');?>" name="advertising.html" title="添加图片" class="iframeurl"><i class="icon-double-angle-right"></i>添加图片</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-folder-open"></i><span class="menu-text">课程管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Question/index');?>" name="advertising.html" title="课程分类" class="iframeurl"><i class="icon-double-angle-right"></i>课程分类</a></li>
                <li class="home"><a href="<?php echo U('Question/course');?>" name="advertising.html" title="课程列表" class="iframeurl"><i class="icon-double-angle-right"></i>课程列表</a></li>
                <li class="home"><a href="<?php echo U('Question/course_com');?>" name="advertising.html" title="课程评论" class="iframeurl"><i class="icon-double-angle-right"></i>课程评论</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-barcode"></i><span class="menu-text">品牌管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Brand/index');?>" name="advertising.html" title="品牌列表" class="iframeurl"><i class="icon-double-angle-right"></i>品牌列表</a></li>
                <li class="home"><a href="<?php echo U('Brand/demio');?>" name="advertising.html" title="车系列表" class="iframeurl"><i class="icon-double-angle-right"></i>车系列表</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-truck"></i><span class="menu-text">车辆管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Cart/index');?>" name="advertising.html" title="车辆列表" class="iframeurl"><i class="icon-double-angle-right"></i>车辆列表</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-comments"></i><span class="menu-text">评车管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Comment/index');?>" name="advertising.html" title="图片管理" class="iframeurl"><i class="icon-double-angle-right"></i>图片管理</a></li>
                <li class="home"><a href="<?php echo U('Comment/info');?>" name="advertising.html" title="评车列表" class="iframeurl"><i class="icon-double-angle-right"></i>评车列表</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-bullhorn"></i><span class="menu-text">问车馆管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Ask/index');?>" name="advertising.html" title="问车馆管理" class="iframeurl"><i class="icon-double-angle-right"></i>问题列表</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-list"></i><span class="menu-text">试车馆管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Test/index');?>" name="advertising.html" title="试车信息列表" class="iframeurl"><i class="icon-double-angle-right"></i>试车信息列表</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-user"></i><span class="menu-text">用户管理 </span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('User/index');?>" name="advertising.html" title="用户列表" class="iframeurl"><i class="icon-double-angle-right"></i>用户列表</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-user"></i><span class="menu-text">管理员管理</span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Admin/index');?>" name="advertising.html" title="管理员列表" class="iframeurl"><i class="icon-double-angle-right"></i>管理员列表</a></li>
            </ul>
        </li>
        <li>
            <a href="#" class="dropdown-toggle"><i class="icon-user"></i><span class="menu-text">车主培训课</span><b class="arrow icon-angle-down"></b></a>
            <ul class="submenu">
                <li class="home"><a href="<?php echo U('Train/index');?>" name="advertising.html" title="管理员列表" class="iframeurl"><i class="icon-double-angle-right"></i>培训课列表</a></li>
            </ul>
        </li>
    </ul>
    <div class="sidebar-collapse" id="sidebar-collapse">
        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
    </div>
    <script type="text/javascript">
        try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
    </script>
</div>
        <div class="main-content">
            <script type="text/javascript">
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>
            <div class="breadcrumbs" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home home-icon"></i>
                        <a href="<?php echo U('Ask/index');?>">问题列表</a>
                    </li>
                    <li class="active"><span class="Current_page iframeurl">回答信息列表</span></li>
                    <li class="active" id="parentIframe"><span class="parentIframe iframeurl"></span></li>
                    <li class="active" id="parentIfour"><span class="parentIfour iframeurl"></span></li>
                </ul>
            </div>
            <div class=" page-content clearfix" style="width: 1700px">
                <div id="products_style">
                    <div class="h_products_list clearfix" id="products_list" style="margin-top: 30px;">
                        <div class="table_menu_list" id="wd">
                            <div class="search_style" style="height: 80px">
                                <div class="title_names">搜索查询</div>
                                <form action="<?php echo U('Ask/see_ask_info',array('id'=>$Think['get']['id']));?>" method="post">
                                    <input type="hidden" name="ask_id" value="<?php echo ($id); ?>">
                                    <ul class="search_content clearfix">
                                        <li>
                                            <label class="l_f">内容关键字</label><input name="content" type="text" class="text_add" placeholder="内容关键字"
                                                                                   style=" width:250px"/></li>
                                        </li>
                                        <li><label class="l_f">添加时间</label><input class="inline laydate-icon" name="add_time" id="start"
                                                                                  style=" margin-left:10px;"></li>
                                        <li style="width:90px;">
                                            <button type="submit" class="btn_search"><i class="icon-search"></i>查询</button>
                                        </li>
                                    </ul>
                                </form>
                            </div>
                            <div class="border clearfix">
                            <span class="l_f">
                            <a href="javascript:ovid()" class="btn btn-danger" id="piliang"><i class="icon-trash"></i>批量删除</a>
                           </span>
                                <span class="r_f">共：<b><?php echo ($count); ?></b>条记录</span>
                            </div>
                            <table class="table table-striped table-bordered table-hover" id="" >
                                <thead>
                                    <tr>
                                        <th width="5%"><label><input type="checkbox" id="all_che" class="ace"><span
                                                class="lbl"></span></label></th>
                                        <th width="5%">回答者</th>
                                        <th width="20%">内容</th>
                                        <th width="20%">图片</th>
                                        <th width="5%">获赞</th>
                                        <th width="5%">添加时间</th>
                                        <th width="5%">状态</th>
                                        <th width="15%">操作</th>
                                    </tr>
                                </thead>
                                <form action="<?php echo U('Ask/del_all_info');?>" id="form01" method="post">
                                    <input type="hidden" name="id" value="<?php echo ($_GET['id']); ?>">
                                <tbody>
                                <?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><label><input name="ids[]" type="checkbox" class="ace" value="<?php echo ($vo["id"]); ?>"><span class="lbl"></span></label></td>
                                        <td width="5%"><?php echo ($vo['user_id']); ?></td>
                                        <td width="20%">
                                        <?php echo ($vo["content"]); ?>
                                        </td>
                                        <td width="20%">
                                            <img src="/maijia/<?php echo ($vo["img"]); ?>" style="height: 100px; width: 100%;" />
                                        </td>
                                        <td width="5%"><?php echo ($vo["com_praise"]); ?></td>
                                        <td width="5%"><?php echo ($vo["add_time"]); ?></td>
                                        <?php if($vo['status'] == 1): ?><td class="td-status"><span class="label label-success radius">已启用</span></td>
                                        <td class="td-manage">
                                            <a onClick="member_stop(this,'<?php echo ($vo["id"]); ?>')" href="javascript:;" title="停用"
                                               class="btn btn-xs btn-success"><i class="icon-ok bigger-120"></i></a>
                                            <?php else: ?>
                                            <td class="td-status"><span class="label label-defaunt radius">已停用</span></td>
                                            <td class="td-manage">
                                                <a onClick="member_start(this,'<?php echo ($vo["id"]); ?>')" href="javascript:;" title="启用"
                                                   class="btn btn-xs"><i class="icon-close bigger-120"></i></a><?php endif; ?>
                                            <a title="查看详情"  href="<?php echo U('Ask/see_com_info',array('id'=>$vo['id']));?>"
                                               class="btn btn-xs btn-info"><i class="icon-eye-open bigger-120"></i></a>
                                            <a title="删除" href="javascript:;" onclick="member_del(this,'<?php echo ($vo["id"]); ?>')"
                                               class="btn btn-xs btn-warning"><i class="icon-trash  bigger-120"></i></a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                                </form>
                            </table>
                            <div id="page" style="margin-top: 5px;text-align: center;"><?php echo ($page); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ace-settings-container" id="ace-settings-container">
    <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
        <i class="icon-cog bigger-150"></i>
    </div>
    <div class="ace-settings-box" id="ace-settings-box">
        <div>
            <div class="pull-left">
                <select id="skin-colorpicker" class="hide">
                    <option data-skin="default" value="#438EB9">#438EB9</option>
                    <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                    <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                    <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                </select>
            </div>
            <span>&nbsp; 选择皮肤</span>
        </div>
    </div>
</div>
    </div><!-- /.main-container-inner -->

</div>
<!--底部样式-->

<div class="footer_style" id="footerstyle">
    <p class="l_f">版权所有： 西安大麦网络科技有限公司  陕ICP备15003277号 </p>
    <p class="r_f">地址：西安市碑林区火炬路东新世纪广场裙楼3层D区  邮编：710000 公司名称： 西安大麦网络科技有限公司</p>
</div>
<!--修改密码样式-->
<div class="change_Pass_style" id="change_Pass">
    <ul class="xg_style">
        <li><label class="label_name">原&nbsp;&nbsp;密&nbsp;码</label><input name="原密码" type="password" class="" id="password"></li>
        <li><label class="label_name">新&nbsp;&nbsp;密&nbsp;码</label><input name="新密码" type="password" class="" id="Nes_pas"></li>
        <li><label class="label_name">确认密码</label><input name="再次确认密码" type="password" class="" id="c_mew_pas"></li>

    </ul>
    <!--       <div class="center"> <button class="btn btn-primary" type="button" id="submit">确认修改</button></div>-->
</div>
<!-- /.main-container -->
<!-- basic scripts -->

</body>
</html>
<script>
    jQuery(function ($) {
        var oTable1 = $('#sample-table').dataTable({
            "aaSorting": [[1, "desc"]],//默认第几个排序
            "bStateSave": true,//状态保存
            "aoColumnDefs": [
                //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
                {"orderable": false, "aTargets": [0, 2, 3, 4, 5, 8, 9]}// 制定列不参与排序
            ]
        });


        $('table th input:checkbox').on('click', function () {
            var that = this;
            $(this).closest('table').find('tr > td:first-child input:checkbox')
                    .each(function () {
                        this.checked = that.checked;
                        $(this).closest('tr').toggleClass('selected');
                    });

        });


        $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
        function tooltip_placement(context, source) {
            var $source = $(source);
            var $parent = $source.closest('table')
            var off1 = $parent.offset();
            var w1 = $parent.width();

            var off2 = $source.offset();
            var w2 = $source.width();

            if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
            return 'left';
        }
    });
    laydate({
        elem: '#start',
        event: 'focus'
    });
    $(function () {
        $("#products_style").fix({
            float: 'left',
            //minStatue : true,
            skin: 'green',
            durationTime: false,
            spacingw: 30,//设置隐藏时的距离
            spacingh: 260,//设置显示时间距
        });
    });
</script>
<script type="text/javascript">
    //初始化宽度、高度
    $(".widget-box").height($(window).height() - 215);
    $(".table_menu_list").width($(window).width() - 250);
    $(".table_menu_list").height($(window).height() - 215);
    //当文档窗口发生改变时 触发
    $(window).resize(function () {
        $(".widget-box").height($(window).height() - 215);
        $(".table_menu_list").width($(window).width() - 260);
        $(".table_menu_list").height($(window).height() - 215);
    })

    /*******树状图*******/
    var setting = {
        view: {
            dblClickExpand: false,
            showLine: false,
            selectedMulti: false
        },
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "pId",
                rootPId: ""
            }
        },
        callback: {
            beforeClick: function (treeId, treeNode) {
                var zTree = $.fn.zTree.getZTreeObj("tree");
                if (treeNode.isParent) {
                    zTree.expandNode(treeNode);
                    return false;
                } else {
                    demoIframe.attr("src", treeNode.file + ".html");
                    return true;
                }
            }
        }
    };

    var zNodes = [
        {id: 1, pId: 0, name: "商城分类列表", open: true},
        {id: 11, pId: 1, name: "蔬菜水果"},
        {id: 111, pId: 11, name: "蔬菜"},
        {id: 112, pId: 11, name: "苹果"},
        {id: 113, pId: 11, name: "大蒜"},
        {id: 114, pId: 11, name: "白菜"},
        {id: 115, pId: 11, name: "青菜"},
        {id: 12, pId: 1, name: "手机数码"},
        {id: 121, pId: 12, name: "手机 "},
        {id: 122, pId: 12, name: "照相机 "},
        {id: 13, pId: 1, name: "电脑配件"},
        {id: 131, pId: 13, name: "手机 "},
        {id: 122, pId: 13, name: "照相机 "},
        {id: 14, pId: 1, name: "服装鞋帽"},
        {id: 141, pId: 14, name: "手机 "},
        {id: 42, pId: 14, name: "照相机 "},
    ];

    var code;

    function showCode(str) {
        if (!code) code = $("#code");
        code.empty();
        code.append("<li>" + str + "</li>");
    }

    $(document).ready(function () {
        var t = $("#treeDemo");
        t = $.fn.zTree.init(t, setting, zNodes);
        demoIframe = $("#testIframe");
        demoIframe.bind("load", loadReady);
        var zTree = $.fn.zTree.getZTreeObj("tree");
        zTree.selectNode(zTree.getNodeByParam("id", '11'));
    });
    /*产品-停用*/
    function member_stop(obj, id) {
        layer.confirm('确认要停用吗？', function (index) {
            $.post("<?php echo U('Ask/edit_status_com');?>",{'id':id},function(data){
                if(data == 'ok'){
                    location.reload();
                    $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,id)" href="javascript:;" title="启用"><i class="icon-ok bigger-120"></i></a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
                    $(obj).remove();
                    layer.msg('已停用!', {icon: 5, time: 1000});
                }
            })
        });
    }

    /*产品-启用*/
    function member_start(obj, id) {
        layer.confirm('确认要启用吗？', function (index) {
            $.post("<?php echo U('Ask/edit_status_com');?>",{'id':id},function(data){
                if(data == 'ok'){
                    location.reload();
                    $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="停用"><i class="icon-ok bigger-120"></i></a>');
                    $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
                    $(obj).remove();
                    layer.msg('已启用!', {icon: 6, time: 1000});
                }
            })
        });
    }
    /*产品-编辑*/
    function member_edit(title, url, id, w, h) {
        layer_show(title, url, w, h);
    }

    /*产品-删除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？', function (index) {
            $.post("<?php echo U('Ask/del_ask_info');?>",{'id':id},function(data){
                if(data == 'ok'){
                    $(obj).parents("tr").remove();
                    layer.msg('已删除!', {icon: 1, time: 1000});
                }
            })
        });
    }
    //面包屑返回值
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.iframeAuto(index);
    $('.Order_form').on('click', function () {
        var cname = $(this).attr("title");
        var chref = $(this).attr("href");
        var cnames = parent.$('.Current_page').html();
        var herf = parent.$("#iframe").attr("src");
        parent.$('#parentIframe').html(cname);
        parent.$('#iframe').attr("src", chref).ready();
        ;
        parent.$('#parentIframe').css("display", "inline-block");
        parent.$('.Current_page').attr({"name": herf, "href": "javascript:void(0)"}).css({
            "color": "#4c8fbd",
            "cursor": "pointer"
        });
        //parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+" class='iframeurl'>" + cnames + "</a>");
        parent.layer.close(index);

    });
</script>
<script>
     $("#piliang").click(function(){
         $("#form01").submit();
     })
</script>