<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>网站后台管理系统  </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="/mj/Public/new/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/mj/Public/new/assets/css/font-awesome.min.css" />
    <!--[if IE 7]>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/font-awesome-ie7.min.css" />
    <![endif]-->
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace.min.css" />
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace-rtl.min.css" />
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace-skins.min.css" />
    <link rel="stylesheet" href="/mj/Public/new/css/style.css"/>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/mj/Public/new/assets/css/ace-ie.min.css" />
    <![endif]-->
    <script src="/mj/Public/new/assets/js/ace-extra.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/mj/Public/new/assets/js/html5shiv.js"></script>
    <script src="/mj/Public/new/assets/js/respond.min.js"></script>
    <![endif]-->
    <!--[if !IE]> -->
    <script src="/mj/Public/new/js/jquery-1.9.1.min.js"></script>
    <!-- <![endif]-->
    <!--[if IE]>
    <script type="text/javascript">window.jQuery || document.write("<script src='/mj/Public/new/assets/js/jquery-1.10.2.min.js'>"+"<"+"script>");</script>
    <![endif]-->
    <script type="text/javascript">
        if("ontouchend" in document) document.write("<script src='/mj/Public/new/assets/js/jquery.mobile.custom.min.js'>"+"<"+"script>");
    </script>
    <script src="/mj/Public/new/assets/js/bootstrap.min.js"></script>
    <script src="/mj/Public/new/assets/js/typeahead-bs2.min.js"></script>
    <!--[if lte IE 8]>
    <script src="/mj/Public/new/assets/js/excanvas.min.js"></script>
    <![endif]-->
    <script src="/mj/Public/new/assets/js/ace-elements.min.js"></script>
    <script src="/mj/Public/new/assets/js/ace.min.js"></script>
    <script src="/mj/Public/new/assets/layer/layer.js" type="text/javascript"></script>
    <script src="/mj/Public/new/assets/laydate/laydate.js" type="text/javascript"></script>
    <script src="/mj/Public/new/assets/dist/echarts.js"></script>
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
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>
            <div class="breadcrumbs" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home home-icon"></i>
                        <a href="<?php echo U('Index/index');?>">首页</a>
                    </li>
                    <li class="active"><span class="Current_page iframeurl"></span></li>
                    <li class="active" id="parentIframe"><span class="parentIframe iframeurl"></span></li>
                    <li class="active" id="parentIfour"><span class="parentIfour iframeurl"></span></li>
                </ul>
            </div>
            <div class="page-content clearfix">
                <div class="alert alert-block alert-success">
                    <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                    <i class="icon-ok green"></i>欢迎使用<strong class="green">后台管理系统<small>(v1.2)</small></strong>,你本次登陆时间为<?php echo (session('mj_login_time')); ?>
                </div>
                <div class="state-overview clearfix" style="height: 118px">
                    <div class="col-lg-3 col-sm-6">
                        <section class="panel">
                            <a href="#" title="商城会员">
                                <div class="symbol terques">
                                    <i class="icon-user"></i>
                                </div>
                                <div class="value">
                                    <h1>34522</h1>
                                    <p>商城用户</p>
                                </div>
                            </a>
                        </section>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <section class="panel">
                            <div class="symbol red">
                                <i class="icon-tags"></i>
                            </div>
                            <div class="value">
                                <h1>140</h1>
                                <p>分销记录</p>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <section class="panel">
                            <div class="symbol yellow">
                                <i class="icon-shopping-cart"></i>
                            </div>
                            <div class="value">
                                <h1>345</h1>
                                <p>商城订单</p>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <section class="panel">
                            <div class="symbol blue">
                                <i class="icon-bar-chart"></i>
                            </div>
                            <div class="value">
                                <h1>￥34,500</h1>
                                <p>交易记录</p>
                            </div>
                        </section>
                    </div>
                </div>
                <!--实时交易记录-->
                <div class="clearfix">
                    <div class="t_Record" style="width: 1393px!important;">
                        <div id="main" style="height:300px; overflow:hidden; width:100%; overflow:auto" ></div>
                    </div>
                    <div class="news_style">
                        <div class="title_name">最新消息</div>
                        <ul class="list">
                            <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
                            <li><i class="icon-bell red"></i><a href="#">6月共处理订单3451比，作废为...</a></li>
                            <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
                            <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
                            <li><i class="icon-bell red"></i><a href="#">后台系统找那个是开通了。</a></li>
                        </ul>
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


<!-- /.main-container -->
<!-- basic scripts -->
<script type="text/javascript">
    //							$(document).ready(function(){
    //
    //								$(".t_Record").width($(window).width()-320);
    //								//当文档窗口发生改变时 触发
    //								$(window).resize(function(){
    //									$(".t_Record").width($(window).width()-320);
    //								});
    //							});


    require.config({
        paths: {
            echarts: '/mj/Public/new/assets/dist'
        }
    });
    require(
            [
                'echarts',
                'echarts/theme/macarons',
                'echarts/chart/line',   // 按需加载所需图表，如需动态类型切换功能，别忘了同时加载相应图表
                'echarts/chart/bar'
            ],
            function (ec,theme) {
                var myChart = ec.init(document.getElementById('main'),theme);
                option = {
                    title : {
                        text: '月购买订单交易记录',
                        subtext: '实时获取用户订单购买记录'
                    },
                    tooltip : {
                        trigger: 'axis'
                    },
                    legend: {
                        data:['所有订单','待付款','已付款','代发货']
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            mark : {show: true},
                            dataView : {show: true, readOnly: false},
                            magicType : {show: true, type: ['line', 'bar']},
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            data : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            name:'所有订单',
                            type:'bar',
                            data:[120, 49, 70, 232, 256, 767, 1356, 1622, 326, 200,164, 133],
                            markPoint : {
                                data : [
                                    {type : 'max', name: '最大值'},
                                    {type : 'min', name: '最小值'}
                                ]
                            }
                        },
                        {
                            name:'待付款',
                            type:'bar',
                            data:[26, 59, 30, 84, 27, 77, 176, 1182, 487, 188, 60, 23],
                            markPoint : {
                                data : [
                                    {name : '年最高', value : 1182, xAxis: 7, yAxis: 1182, symbolSize:18},
                                    {name : '年最低', value : 23, xAxis: 11, yAxis: 3}
                                ]
                            },


                        }
                        , {
                            name:'已付款',
                            type:'bar',
                            data:[26, 59, 60, 264, 287, 77, 176, 122, 247, 148, 60, 23],
                            markPoint : {
                                data : [
                                    {name : '年最高', value : 172, xAxis: 7, yAxis: 172, symbolSize:18},
                                    {name : '年最低', value : 23, xAxis: 11, yAxis: 3}
                                ]
                            },

                        }
                        , {
                            name:'代发货',
                            type:'bar',
                            data:[26, 59, 80, 24, 87, 70, 175, 1072, 48, 18, 69, 63],
                            markPoint : {
                                data : [
                                    {name : '年最高', value : 1072, xAxis: 7, yAxis: 1072, symbolSize:18},
                                    {name : '年最低', value : 22, xAxis: 11, yAxis: 3}
                                ]
                            },

                        }
                    ]
                };

                myChart.setOption(option);
            }
    );
</script>

</body>
</html>
<script>
    $(".t_Record").css("width","1393px")
</script>