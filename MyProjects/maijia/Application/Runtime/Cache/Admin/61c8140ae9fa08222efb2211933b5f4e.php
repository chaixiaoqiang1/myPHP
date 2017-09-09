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
    <script src="/maijia/Public/new/assets/js/ace-elements.min.js"></script>
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
<!--<script type="text/javascript">window.jQuery || document.write("<script src='/maijia/Public/new/assets/js/jquery-1.10.2.min.js'>"+"<"+"script>");</script>-->
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
                <li class="home"><a href="<?php echo U('Drive/index');?>" name="advertising.html" title="试驾列表" class="iframeurl"><i class="icon-double-angle-right"></i>试驾列表</a></li>
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
                        <a href="<?php echo U('Cart/index');?>">车辆管理</a>
                    </li>
                    <li class="active"><span class="Current_page iframeurl">车辆列表</span></li>
                    <li class="active" id="parentIframe"><span class="parentIframe iframeurl"></span></li>
                    <li class="active" id="parentIfour"><span class="parentIfour iframeurl"></span></li>
                </ul>
            </div>
            <div class=" page-content clearfix" style="width: 1700px">
                <div id="products_style">
                    <form action="<?php echo U('Cart/index');?>" method="post">
                    <div class="search_style" style="height: 80px">
                        <div class="title_names">搜索查询</div>
                        <ul class="search_content clearfix">
                            <div id="cart_type">
                                <li id="demio_brand"><label class="l_f" style="margin-right: 10px;">品牌</label>
                                    <select name="brands" onchange="query(this.value,1)">
                                        <option value="">请选择品牌</option>
                                        <?php if(is_array($brands)): foreach($brands as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </li>
                             </div>
                            <li><label class="l_f">添加时间</label><input name="add_time" class="inline laydate-icon" id="start"
                                                                      style=" margin-left:10px;"></li>
                            <li style="width:90px;">
                                <button type="submit" class="btn_search"><i class="icon-search"></i>查询</button>
                            </li>
                        </ul>
                    </div>
                    </form>
                    <div class="border clearfix">
                            <span class="l_f">
                            <a href="<?php echo U('Cart/add');?>" title="添加课程" class="btn btn-warning Order_form"><i class="icon-plus"></i>添加车辆</a>
                            <a href="javascript:ovid()" class="btn btn-danger" id="all_delete"><i class="icon-trash"></i>批量删除</a>
                           </span>
                            <span class="r_f">共：<b><?php echo ($count); ?></b>条记录</span>
                    </div>
                    <!--产品列表展示-->
                    <div class="h_products_list clearfix" id="products_list">

                        <div class="table_menu_list" id="wd">
                            <table class="table table-striped table-bordered table-hover" id="sample-table" >
                                <thead>
                                    <tr>
                                        <th width="5%"><label><input type="checkbox" id="all_che" class="ace"><span class="lbl"></span></label>
                                        </th>
                                        <th width="10%">品牌</th>
                                        <th width="10%">车系</th>
                                        <th width="10%">型号</th>
                                        <th width="10%">图片</th>
                                        <th width="5%">价格</th>
                                        <th width="10%">加入时间</th>
                                        <th width="10%">状态</th>
                                        <th width="20%">操作</th>
                                    </tr>
                                </thead>
                                <?php if(is_array($cartinfo)): foreach($cartinfo as $key=>$v): ?><tbody>
                                    <tr>
                                        <td width="5%"><label><input name="id" type="checkbox" value="<?php echo ($v["id"]); ?>" class="ace"><span class="lbl"></span></label>
                                        </td>
                                        <td width="10%"><?php echo ($v["brands_name"]); ?></td>
                                        <td width="10%"><?php echo ($v["demio"]); ?></td>
                                        <td width="10%"><?php echo ($v["model"]); ?></td>
                                        <td width="15%"> <img src="/maijia/<?php echo ($v["image"]); ?>"  style=" height: 100px; width: 100%;"/></td>
                                        <td width="5%"><?php echo ($v["price"]); ?></td>
                                        <td width="5%"><?php echo ($v["add_time"]); ?></td>
                                        <?php if($v["status"] == 1): ?><td class="td-status"><span class="label label-success radius">已启用</span></td>
                                            <td class="td-manage">
                                                <a onClick="member_stop(this,'<?php echo ($v["id"]); ?>')" href="javascript:;" title="停用"
                                               class="btn btn-xs btn-success"><i class="icon-ok bigger-120"></i></a>
                                        <?php else: ?>
                                            <td class="td-status"><span class="label label-defaunt radius">已停用</span></td>
                                            <td class="td-manage">
                                                <a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,'<?php echo ($v["id"]); ?>')" href="javascript:;" title="启用">
                                                    <i class="fa fa-close bigger-120"></i></a><?php endif; ?>
                                            <a title="编辑"  href="<?php echo U('Cart/editor',array('id'=>$v['id']));?>"
                                               class="btn btn-xs btn-info"><i class="icon-edit bigger-120"></i></a>
                                            <a title="删除" href="javascript:;" onclick="member_del(this,'<?php echo ($v["id"]); ?>')"
                                               class="btn btn-xs btn-warning"><i class="icon-trash  bigger-120"></i></a>
                                        </td>
                                    </tr>
                                </tbody><?php endforeach; endif; ?>
                            </table>
                            <div id="page" class="text-center">
                                <?php echo ($show); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- /.page-content -->
        </div><!-- /.main-content -->

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
<!--<script src="/maijia/Public/script/jquery-1.8.0.min.js"></script>-->
<script>

    $("#all_che").click(function(){
        var che= $(this).attr('checked');
        if(che == undefined){
            $(this).attr('checked','checked');
            $("[name=id]:checkbox").prop("checked", true);
        }else{
            $(this).attr('checked',false);
            $("[name=id]:checkbox").prop("checked", false);
        }
    })

    $('#all_delete').click(function(){
        var str='';
        $('input[name="id"]').each(function(){
            if($(this).is(":checked")){
                str+=$(this).val()+',';
            }
        })

        if(str ==''){
            alert('请选择要删除的选项');
            return false;
        }else{
            location.href='/maijia/index.php/Admin/Cart/delete_all/id/'+str+'';
        }
    });
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
            $.ajax({
                type:"post",
                url:"<?php echo U('Cart/status');?>",
                data:"id="+id,
                dataType:"html",
                success:function (re) {
                    if(re==1){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,' + id + ')" href="javascript:;" title="启用"><i class="fa fa-close bigger-120"></i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
                        $(obj).remove();
                        layer.msg('已停用!', {icon: 5, time: 1000});
                    }
                }
            });
        });
    }

    /*产品-启用*/
    function member_start(obj, id) {
        layer.confirm('确认要启用吗？', function (index) {
            $.ajax({
                type:"post",
                url:"<?php echo U('Cart/status');?>",
                data:"id="+id,
                dataType:"html",
                success:function (re) {
                    if(re==1){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,'+id+')" href="javascript:;" title="停用"><i class="icon-ok bigger-120"></i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
                        $(obj).remove();
                        layer.msg('已启用!', {icon: 6, time: 1000});
                    }
                }
            });
        });
    }
    /*产品-编辑*/
    function member_edit(title, url, id, w, h) {
        layer_show(title, url, w, h);
    }

    /*产品-删除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？', function (index) {
            $.ajax({
                type:"post",
                url:"<?php echo U('Cart/delete');?>",
                data:"id="+id,
                dataType:"html",
                success:function (re) {
                    if(re=='OK'){
                        $(obj).parents("tr").remove();
                        layer.msg('已删除!', {icon: 1, time: 1000});
                    }
                }
            });
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
    function query(id,type){
        $.ajax({
            type:"post",
            url:"<?php echo U('Cart/query');?>",
            data:{"id":id,"type":type},
            dataType:"json",
            success:function (e) {
                if(type==1){
                    $('#demio_brand').nextAll().remove();
                }
                if(e==0){
                    return false;
                }else{
                    var len=e.length;
                    var html='';
                    html+='<li>';
                    if(type == 1){
                        html+='<label class="l_f" style="margin-right: 10px;">车系名称</label>';
                        html+='<select name="demio_id" id="demio_list">';
                        html+='<option selected value="">请选择车系</option>';
                    }else{
                        html+='<label class="l_f" style="margin-right: 10px;">车型</label>';
                        html+='<select name="model_id">';
                        html+='<option selected value="">请选择车型</option>';
                    }
                    for(var i=0; i<len;i++){
                        html+='<option value='+e[i]['id']+'>'+e[i]["demio_name"]+'</option>';
                    }
                    html+='</select>';
                    html+='</li>';

                    $('#cart_type').append(html);
                }
            }
        });
    }
    $(document).on("change", "#demio_list", function() {
        $(this).parent('li').next('li').remove();
        query($(this).val(),2);
    });
</script>