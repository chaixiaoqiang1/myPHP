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
</head>
<style>
    .open_demio{
        display: block;
        width: 16px;
        height: 16px;
        line-height: 14px;
        text-align: center;
        border: 1px solid #676A6C;
        font-weight: bold;
        cursor: pointer;
    }
</style>
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
                    <li class="active"><span class="Current_page iframeurl">车系列表</span></li>
                    <li class="active" id="parentIframe"><span class="parentIframe iframeurl"></span></li>
                    <li class="active" id="parentIfour"><span class="parentIfour iframeurl"></span></li>
                </ul>
            </div>

            <div class=" clearfix" id="advertising">

                <div class="Ads_list"  style="width: 1730px">
                    <div class="search_style" style="height: 80px">
                        <div class="title_names">搜索查询</div>
                        <form name="frm" action="/maijia/index.php/Admin/Brand/demio" method="post">
                            <ul class="search_content clearfix">
                                <li><label class="l_f">品牌</label>
                                    <select class="" name="brands_id" style="margin-left: 10px;">
                                        <option selected value="">选择品牌</option>
                                        <?php if(is_array($brands)): foreach($brands as $key=>$v): ?><option  value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </li>
                                <li><label class="l_f">车系关键字</label><input name="demio_name" type="text" class="text_add" placeholder="输入车系关键字"
                                                                        style=" width:250px"/></li>
                                <li style="width:90px;">
                                    <button type="submit" class="btn_search"><i class="icon-search"></i>查询</button>
                                </li>
                            </ul>
                        </form>
                    </div>
                    <div class="border clearfix">
                            <span class="l_f">
                            <a href="<?php echo U('Brand/add_demio');?>" title="添加车系" class="btn btn-warning Order_form"><i class="icon-plus"></i>添加车系</a>
                           </span>
                        <span class="r_f">共：<b><?php echo ($count); ?></b>条记录</span>
                    </div>
                    <table class="table table-hover table-mail">
                        <colgroup>
                            <col width="20%"/>
                            <col width="20%"/>
                            <col width="20%"/>
                            <col width="20%"/>
                        </colgroup>
                        <thead>
                        <tr pid="0">
                            <th style="text-align: center;">折叠</th>
                            <th style="text-align: center;">分类名称</th>
                            <th style="text-align: center;">状态</th>
                            <th style="text-align: center;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr id="<?php echo ($v["id"]); ?>" pid="<?php echo ($v["pid"]); ?>">
                                <td align="center">
                                    <?php if($v['child']): ?><span class="open_demio">+</span><?php endif; ?>
                                </td>
                                <td align="center">
                                    <input type="hidden" style="width: 50px;text-align: center;" value="">
                                    <?php echo ($v["demio_name"]); ?>
                                </td>
                                <td align="center">
                                    <?php if($v['status'] == 1): ?><span class="label label-warning">正常</span>
                                        <?php else: ?>
                                        <span class="label label-primary">冻结</span><?php endif; ?>
                                </td>
                                <td align="center">
                                    <a href="<?php echo U('Brand/addChild',array('id'=>$v['id']));?>" class="label label-primary">添加车型</a>
                                    <a href="<?php echo U('Brand/demio_update',array('id'=>$v['id']));?>" class="label label-info">修改</a>
                                    <a href="javascript:member_del(this,<?php echo ($v["id"]); ?>)" class="label label-danger">删除</a>
                                </td>
                            </tr>
                            <?php if(is_array($v['child'])): $i = 0; $__LIST__ = $v['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="<?php echo ($vo["id"]); ?>" pid="<?php echo ($vo["pid"]); ?>">
                                    <td align="center">
                                        <?php if($vo['child']): ?><span class="open_demio">+</span><?php endif; ?>
                                    </td>
                                    <td >
                                        <input type="hidden" style="width: 50px;text-align: center;" value="">
                                        |&#45;&#45;&#45;&#45;<?php echo ($vo["demio_name"]); ?>
                                    </td>
                                    <td align="center">
                                        <?php if($vo['status'] == 1): ?><span class="label label-warning">正常</span>
                                            <?php else: ?>
                                            <span class="label label-primary">冻结</span><?php endif; ?>
                                    </td>
                                    <td align="center">
                                        <a href="<?php echo U('Brand/demio_update',array('id'=>$vo['id']));?>" style="margin-left: 68px;" class="label label-info">修改</a>
                                        <a href="javascript:member_del(this,<?php echo ($vo["id"]); ?>)" class="label label-danger">删除</a>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>

                    </table>
                    <div id="page" class="text-center">
                    <?php echo ($show); ?>
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
    $('tr[pid!=0]').hide();
    $('.open_demio').click(function(){
        var index=$(this).parents('tr').attr('id');
        var sign=$(this).html();
        if(sign=='+'){
            $(this).html('-');
            $('tr[pid='+index+']').show();
        }else{
            $(this).html('+');
            $('tr[pid='+index+']').hide();
        }
    });
    $("#all_che").on("click" , function(){
        var che= $(this).attr('checked');
        if(che == undefined){
            $(this).attr('checked','checked');
            $('input[name="id"]').each(function(index,i){
                $(this).attr('checked','checked')
            })

        }else{
            $(this).attr('checked',false);
            $('input[name="id"]').each(function(index,i){
                $(this).attr('checked',false)
            })
        }
    });
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
            location.href='/maijia/index.php/Admin/Brand/delete_all/id/'+str+'';
        }
    })

    /*广告图片-停用*/
    function member_stop(obj, id) {
        layer.confirm('确认要关闭吗？', {icon: 0,}, function (index) {
            $.ajax({
                type:"post",
                url:"/maijia/index.php/Admin/Brand/stop",
                data:"id="+id,
                dataType:"html",
                success:function (re){
                    if(re==1){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs " onClick="member_start(this,'+id+')" href="javascript:;" title="显示"><i class="fa fa-close bigger-120"></i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已关闭</span>');
                        $(obj).remove();
                        layer.msg('关闭!', {icon: 5, time: 1000});
                    }
                }
            });

        });
    }
    /*广告图片-启用*/
    function member_start(obj, id) {
        layer.confirm('确认要显示吗？', {icon: 0,}, function (index) {
            $.ajax({
                type:"post",
                url:"/maijia/index.php/Admin/Brand/stop",
                data:"id="+id,
                dataType:"html",
                success:function(re){
                    if(re==1){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" class="btn btn-xs btn-success" onClick="member_stop(this,'+id+')" href="javascript:;" title="关闭"><i class="fa fa-check  bigger-120"></i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">显示</span>');
                        $(obj).remove();
                        layer.msg('显示!', {icon: 6, time: 1000});
                    }
                }
            });
        });
    }
    /*广告图片-删除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？', {icon: 0,}, function (index) {
            window.location="/maijia/index.php/Admin/Brand/demio_delete/id/"+id;
        });
    }
    function update(id){
        window.location="/maijia/index.php/Admin/Brand/update/id/"+id;
    }
    laydate({
        elem: '#start',
        event: 'focus'
    });
    function orderChange(id){
        var order=$("#order"+id).val();
        $.ajax({
            type:"post",
            url:"/maijia/index.php/Admin/Brand/reorder",
            data:"id="+id+"&order="+order,
            dataType:"html",
            success:function (re){
                if(re=="ok"){
                    $("#order"+id).val(re);
                }
            }
        });
    }
    function check(){
        if(document.frm.brands_id.value==""){
            alert("请选择要搜索的品牌");
            return false;
        }
    }
</script>