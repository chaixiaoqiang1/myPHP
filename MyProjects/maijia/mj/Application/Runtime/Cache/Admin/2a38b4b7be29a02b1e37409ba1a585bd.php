<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>网站后台管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="/mj/Public/new/assets/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/mj/Public/new/css/style.css"/>
    <link href="/mj/Public/new/assets/css/codemirror.css" rel="stylesheet">
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace.min.css"/>
    <link rel="stylesheet" href="/mj/Public/new/font/css/font-awesome.min.css"/>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace-ie.min.css"/>
    <![endif]-->
    <link rel="stylesheet" href="/mj/Public/new/css/style.css"/>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace.min.css"/>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/font-awesome.min.css"/>

    <link rel="stylesheet" href="/mj/Public/new/assets/css/font-awesome.min.css"/>
    <!--[if IE 7]>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/font-awesome-ie7.min.css"/>
    <![endif]-->
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace.min.css"/>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace-rtl.min.css"/>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace-skins.min.css"/>
    <link rel="stylesheet" href="/mj/Public/new/css/style.css"/>
    <link rel="stylesheet" href="/mj/Public/new/Widget/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
    <link href="/mj/Public/new/Widget/icheck/icheck.css" rel="stylesheet" type="text/css"/>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace-ie.min.css"/>
    <![endif]-->

    <script src="/mj/Public/new/js/jquery-1.9.1.min.js"></script>

    <script src="/mj/Public/new/assets/js/ace-extra.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/mj/Public/new/assets/js/html5shiv.js"></script>
    <script src="/mj/Public/new/assets/js/respond.min.js"></script>
    <![endif]-->
    <!--[if !IE]> -->
    <!-- <![endif]-->
    <!--[if IE]>

    <![endif]-->

    <script src="/mj/Public/new/assets/js/bootstrap.min.js"></script>
    <script src="/mj/Public/new/assets/js/typeahead-bs2.min.js"></script>
    <!--[if lte IE 8]>
    <script src="/mj/Public/new/assets/js/excanvas.min.js"></script>
    <![endif]-->
    <script src="/mj/Public/new/assets/js/ace-elements.min.js"></script>
    <script src="/mj/Public/new/assets/js/jquery.dataTables.min.js"></script>
    <script src="/mj/Public/new/assets/js/jquery.dataTables.bootstrap.js"></script>
    <script type="text/javascript" src="/mj/Public/new/js/H-ui.js"></script>
    <script type="text/javascript" src="/mj/Public/new/js/H-ui.admin.js"></script>
    <script src="/mj/Public/new/js/lrtk.js" type="text/javascript"></script>
    <script type="text/javascript" src="/mj/Public/new/Widget/zTree/js/jquery.ztree.all-3.5.min.js"></script>
    <script src="/mj/Public/new/assets/js/ace.min.js"></script>
    <script src="/mj/Public/new/assets/layer/layer.js" type="text/javascript"></script>
    <script src="/mj/Public/new/assets/laydate/laydate.js" type="text/javascript"></script>
    <script type="text/javascript" src="/mj/Public/new/Widget/swfupload/swfupload.js"></script>
    <script type="text/javascript" src="/mj/Public/new/Widget/swfupload/swfupload.queue.js"></script>
    <script type="text/javascript" src="/mj/Public/new/Widget/swfupload/swfupload.speed.js"></script>
    <script type="text/javascript" src="/mj/Public/new/Widget/swfupload/handlers.js"></script>
    <script src="/mj/Public/new/js/common.js"></script>
</head>
<body>
<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>
    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <img src="/mj/Public/new/images/logo.png">
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
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {
        }
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
                <li class="home"><a href="<?php echo U('Brand/index');?>" name="advertising.html" title="课程分类" class="iframeurl"><i class="icon-double-angle-right"></i>品牌列表</a></li>
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
                try {
                    ace.settings.check('breadcrumbs', 'fixed')
                } catch (e) {
                }
            </script>
            <div class="breadcrumbs" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home home-icon"></i>
                        <a href="<?php echo U('Banner/index');?>">品牌管理</a>
                    </li>
                    <li class="active"><span class="Current_page iframeurl">品牌列表</span></li>
                    <li class="active" id="parentIframe"><span class="parentIframe iframeurl"></span></li>
                    <li class="active" id="parentIfour"><span class="parentIfour iframeurl"></span></li>
                </ul>
            </div>

            <div class=" clearfix" id="advertising">

                <div class="Ads_list"  style="width: 1730px">
                    <div class="search_style" style="height: 80px">
                        <div class="title_names">搜索查询</div>
                        <ul class="search_content clearfix">
                            <li><label class="l_f">品牌</label><input name="" type="text" class="text_add" placeholder="输入品牌关键字"
                                                                    style=" width:250px"/></li>
                            <li><label class="l_f">添加时间</label><input class="inline laydate-icon" id="start"
                                                                      style=" margin-left:10px;"></li>
                            <li style="width:90px;">
                                <button type="button" class="btn_search"><i class="icon-search"></i>查询</button>
                            </li>
                        </ul>
                    </div>
                    <div class="border clearfix">
                       <span class="l_f">
                        <a href="<?php echo U('Brand/add');?>" id="ads_add" class="btn btn-warning"><i class="fa fa-plus"></i> 添加品牌</a>
                        <a href="javascript:ovid()" class="btn btn-danger"><i class="fa fa-trash"></i> 批量删除</a>
                       </span>
                        <span class="r_f">共：<b>45</b>条图片</span>
                    </div>
                      <table class="table table-striped table-bordered table-hover" id="sample-table">
                            <thead>
                            <tr>
                                <th width="25"><label><input type="checkbox" class="ace"><span
                                        class="lbl"></span></label></th>
                                <th width="80">ID</th>
                                <th width="100">名称</th>
                                <th width="240px">图片</th>
                                <th width="150px">尺寸（大小）</th>
                                <th width="70">排序</th>
                                <th width="180">加入时间</th>
                                <th width="70">状态</th>
                                <th width="250">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
                                <td>1</td>
                                <td>幻灯片</td>
                                <td style="text-align: center;">
                                    <img src="/mj/Public/new/products/ad.jpg"  style="border-radius: 50%; height: 80px; width: 80px;"/>
                                </td>
                                <td>1890x1080</td>
                                <td ><input class="text-center" type="text" value="1" style="width: 30px;"></td>
                                <td>2016-6-29 12:34</td>
                                <td class="td-status"><span class="label label-success radius">显示</span></td>
                                <td class="td-manage">
                                    <a onClick="member_stop(this,'10001')" href="javascript:;" title="停用"
                                       class="btn btn-xs btn-success"><i class="fa fa-check  bigger-120"></i></a>
                                    <a title="编辑" onclick="member_edit('编辑','member-add.html','4','','510')"
                                       href="javascript:;" class="btn btn-xs btn-info"><i
                                            class="fa fa-edit bigger-120"></i></a>
                                    <a title="删除" href="javascript:;" onclick="member_del(this,'1')"
                                       class="btn btn-xs btn-warning"><i class="fa fa-trash  bigger-120"></i></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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
    </div>
</div>
<!--底部样式-->

<div class="footer_style" id="footerstyle">
    <p class="l_f">版权所有： 西安大麦网络科技有限公司  陕ICP备15003277号 </p>
    <p class="r_f">地址：西安市碑林区火炬路东新世纪广场裙楼3层D区  邮编：710000 公司名称： 西安大麦网络科技有限公司</p>
</div>


</body>
</html>


<script>

</script>
<script>
    //初始化宽度、高度
    //

    /*广告图片-停用*/
    function member_stop(obj, id) {
        layer.confirm('确认要关闭吗？', {icon: 0,}, function (index) {
            $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,id)" href="javascript:;" title="显示"><i class="fa fa-close bigger-120"></i></a>');
            $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已关闭</span>');
            $(obj).remove();
            layer.msg('关闭!', {icon: 5, time: 1000});
        });
    }
    /*广告图片-启用*/
    function member_start(obj, id) {
        layer.confirm('确认要显示吗？', {icon: 0,}, function (index) {
            $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,id)" href="javascript:;" title="关闭"><i class="fa fa-check  bigger-120"></i></a>');
            $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">显示</span>');
            $(obj).remove();
            layer.msg('显示!', {icon: 6, time: 1000});
        });
    }
    /*广告图片-删除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？', {icon: 0,}, function (index) {
            $(obj).parents("tr").remove();
            layer.msg('已删除!', {icon: 1, time: 1000});
        });
    }
</script>