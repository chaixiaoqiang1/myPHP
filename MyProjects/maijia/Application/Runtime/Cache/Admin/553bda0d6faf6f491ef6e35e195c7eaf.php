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
                try {
                    ace.settings.check('breadcrumbs', 'fixed')
                } catch (e) {
                }
            </script>
            <div class="breadcrumbs" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home home-icon"></i>
                        <a href="<?php echo U('Cart/index');?>">车辆管理</a>
                    </li>
                    <li class="active"><span class="Current_page iframeurl">车辆添加</span></li>
                    <li class="active" id="parentIframe"><span class="parentIframe iframeurl"></span></li>
                    <li class="active" id="parentIfour"><span class="parentIfour iframeurl"></span></li>
                </ul>
            </div>
            <div class=" clearfix" id="advertising">
                <form name="frm" action="<?php echo U('Cart/add');?>" method="post" enctype="multipart/form-data" onsubmit="return check()">
                <div id="add_ads_style" >
                    <div class="add_adverts">
                        <ul >
                            <div id="cart_type">
                                <li id="demio_brand">
                                    <label class="label_name">品牌选择</label>
                                  <span class="cont_style">
                                      <select class="form-control"  name="brands" onchange="query(this.value,1)">
                                          <option selected value="">请选择品牌</option>
                                          <?php if(is_array($brands)): foreach($brands as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                      </select>
                                  </span>
                                </li>
                            </div>
                            <li>
                                <label class="label_name">价格</label><span class="cont_style"><input name="price" type="text"  placeholder="0.00"  onkeyup="value=value.replace(/[^\0-9\.]/g,'')" class="col-xs-10 col-sm-5" style="width:450px" onkeyup='this.value=this.value.replace(/\D/gi,"")'></span>
                            </li>
                            <li><label class="label_name">图片尺寸</label><span class="cont_style">
                              <input name="height" type="text" placeholder="0" class="col-xs-10 col-sm-5" style="width:80px">
                              <span class="l_f" style="margin-left:10px;">x</span><input name="width" type="text" id="form-field-1" placeholder="0"
                                                                                         class="col-xs-10 col-sm-5" style="width:80px"></span>
                            </li>
                            <li><label class="label_name">状&nbsp;&nbsp;态：</label>
                               <span class="cont_style">
                                 &nbsp;&nbsp;<label><input name="status" value="1" type="radio" checked="checked" class="ace"><span
                                       class="lbl">显示</span></label>&nbsp;&nbsp;&nbsp;
                                 <label><input name="status" value="0" type="radio" class="ace"><span class="lbl">隐藏</span></label></span>
                                <div class="prompt r_f"></div>
                            </li>
                            <li><label class="label_name">图片</label><span class="cont_style">
                                <div class="prompt" style="top:150px; left: 0;">
                                    <p>最多上传6张，图片大小小于5MB,支持.jpg;.gif;.png;.jpeg格式的图片</p>
                                    <button style="margin-top: 20px; width: 30%;" class="btn btn-success" type="submit" id="tijiao">提交</button>
                                </div>
                                 <div class="demo" style="width: 1000px;">
                                     <div class="col-lg-12 col-sm-12 col-md-12">
                                         <div class="row">
                                             <div class="col-lg-2 col-sm-2 col-md-2 text-center" style="border: 1px solid #E3E3E3;">
                                                <img src="/maijia/Public/new/images/image.png" width="100px" alt="" height="100px" onclick="document.getElementById('upload').click()"/>
                                                <input type="file" multiple name="goods_img[]" id="upload"  class="upfile uplo" style="display: none;" />
                                             </div>
                                             <div class="col-lg-10 col-sm-10 col-md-10" id="inform_show_img">
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                               </span>
                            </li>
                        </ul>
                    </div>
                </div>
               </form>
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
<script src="/maijia/Public/script/jquery-1.8.0.min.js"></script>
<div class="footer_style" id="footerstyle">
    <p class="l_f">版权所有： 西安大麦网络科技有限公司  陕ICP备15003277号 </p>
    <p class="r_f">地址：西安市碑林区火炬路东新世纪广场裙楼3层D区  邮编：710000 公司名称： 西安大麦网络科技有限公司</p>
</div>

<script>
    $('#upload').on('change',function (event)
    {
        console.log(event.target.files);
        var allLen=event.target.files.length;
        if(allLen >6){
            alert('图片不能多于6张');
            return false;
        }
        var html = "";
        for(var i=0; i<allLen; i++){
            var tmppath = window.webkitURL.createObjectURL(event.target.files[i]);
            html+='<img src="'+tmppath+'" alt="" style="width:100px; height:100px; margin-right:20px;">';
        }
        $("#inform_show_img").html(html);
    });
    function query(id,type){
        $.ajax({
            type:"post",
            data:{"id":id,"type":type},
            url:"<?php echo U('Cart/query');?>",
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
                        html+='<label class="label_name">车系选择</label>';
                        html+='<span class="cont_style">';
                        html+='<select class="form-control" name="demio_id" id="demio_list">';
                        html+='<option selected value="">请选择车系</option>';
                    }else{
                        html+='<label class="label_name">车型选择</label>';
                        html+='<span class="cont_style">';
                        html+='<select class="form-control" id="model_list" name="model_id">';
                        html+='<option selected value="">请选择车型</option>';
                    }
                    for(var i=0; i<len;i++){
                        html+='<option value='+e[i]['id']+'>'+e[i]["demio_name"]+'</option>';
                    }
                        html+='</select>';
                        html+='</span>';
                        html+='</li>';

                    $('#cart_type').append(html);
                }
            }

        })

    }
    $('#demio_list').live('change',function(){
      $(this).parent().parent('li').next('li').remove();
      query($(this).val(),2)
    })
    function check(){
        if(document.frm.brands.value==""){
            alert("请选择品牌名称");
            return false;
        }
        var demio_list=document.getElementById("demio_list");
        if(demio_list == null){
            alert("请选择车系名称");
            return false;
        }

        if(document.frm.demio_id.value==""){
            alert("请选择车系名称");
            return false;
        }
        var model_list=document.getElementById("model_list");
        if(model_list==null){
            alert("请选择车型名称");
            return false;
        }
        if(document.frm.model_id.value==""){
            alert("请选择车型名称");
            return false;
        }
        if(!$("input[name='price']").val()){
            alert("价格不能为空");
            return false;
        }
        if(!$("input[name='height']").val()){
            alert("图片尺寸不能为空");
            return false;
        }
        if(!$("input[name='width']").val()){
            alert("图片尺寸不能为空");
            return false;
        }
        if($("#upload").val()==""){
            alert("请上传车辆图片");
            return false;
        }

    }





</script>
</body>
</html>