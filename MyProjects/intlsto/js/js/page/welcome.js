/**
 * 欢迎首页
 */
$(function(){

    //加载第一个包裹列表
    loadList(0);

	/*订单状态列表切换*/
	$('.status_list').on('click',function(){
		$(this).addClass('active').siblings().removeClass('active');

        var index=$(this).index(),//获取当前索引
            load=$(this).attr('load');//是否加载过
        if(load==='0'){
            loadList(index);
        }

        $('.order_box').eq(index).addClass('order_box_block').siblings().removeClass('order_box_block');

	});

	
	/*鼠标经过订单弹出物流信息框*/
	$(".order_box").on('mouseover',function (e) {

	    //滑过可点击的确认收货按钮
	    if($(e.target).hasClass('btn-primary-outline')){
            $(e.target).parent().siblings('.order_show_box').hide();
            return false;
        }
        var boxObj=$(e.target).children('div.order_show_box'),
            Height=$(e.target).height()+15+'px';
        boxObj.css('top',Height).show();

        /*鼠标离开隐藏物流信息框*/
        $('.order_box dl').on('mouseleave',function(e){
            $(this).children('div.order_show_box').hide();
        });
    });


    /*国家切换*/

    $('#list_nation .nation_title ul li').on('click',function(){
        if($(".nation_title ul li").length<=1) return false;
        var titleLi=$(this).index();
        $(this).addClass('active').siblings().removeClass('active');
        $('#list_nation .nation_list').eq(titleLi).addClass('nation_list_block').siblings().removeClass('nation_list_block');
    });






    /**
	 * 查看集散转运仓列表
     */
    $('.warehouse').on('click',function(){
        layer.open({
            type: 2,
            title:"集散转运仓地址查看",
            shadeClose: true,
            area: ['600px', '550px'], //宽高
            content: ware_url,
        });
    });

    /**
     * 查看新闻详情
     */
    $('.news').on('click',function () {
        layer.open({
            type: 1,
            title:"查看新闻",
            shadeClose: true,
            maxmin:true,
            area: ['800px', '550px'], //宽高
            content: $(this).attr('new_data')
        });

    });

    /**
	 * 切换注册国家
     */
	$('.chage_nation').on('click',function(){
        layer.open({
            type: 1,
            title: false,
            closeBtn: 1,
            area:['450px','auto'],
            shadeClose: true,
            skin: 'layui-layer-demo', //样式类名
            content: $('#list_nation')
        });
	});

});



/**
 * 会员切换已注册国家
 * @param countryCode
 */
function switchCountry(countryCode) {
    $.ajax({
        url:switchCountry_url,
        type:'post',
        data:{countryCode:countryCode},
        dataType:'json',
        success:function (data) {
            sysException(data);
            if(data.state){
                $(".countryName",parent.document).html("("+data.countryName+")");
                window.location.href=location.href;
            }else {
                layer.msg(data.message);
            }
        }
    });
}


/**
 * 会员开通未注册国家
 * @param countryCode
 */
function openCountry(countryCode) {
    layer.confirm("当前会员未在所选国家注册，是否现在注册开通？",function () {
        layer.closeAll();
        loading();
        $.ajax({
            url:openCountry_url,
            type:'post',
            data:{countryCode:countryCode},
            dataType:'json',
            success:function (data) {
                sysException(data);
                if(data.state){
                    $(".countryName",parent.document).html("("+data.countryName+")");
                    //开通成功，切换国家，刷新页面
                    window.location.href=location.href;
                }else {
                    layer.msg(data.message);
                }
            },
            error:function () {
                layer.msg("系统错误，请稍后重试");
            }
        });
    });
}

/**
 * 加载包裹列表
 * @param index
 */
function loadList(index) {
    var headerObj=$(".status_list").eq(index),
        bodyObj=$('.order_box').eq(index),
        statusCode=headerObj.attr('code');
    headerObj.attr('load',"1");
    $.ajax({
        url:orderList_url,
        data:{statusCode: statusCode},
        success:function (data) {
            sysException(data);
            var html='';
            if(data.state){
                $.each(data.data,function (k,v) {
                    html+='<dl>';
                    if(v.ifSure===1){
                        html+='<dt><button class="btn btn-primary-outline" id="sto'+v.stoNo+'" onclick="confirmOrder(\''+v.stoNo+'\')" >确认收货</button></dt>';
                    }else {
                        html+='<dt><button class="btn disabled">确认收货</button></dt>';
                    }
                    html+='<dd>';
                    html+='<h5>'+v.receiverName+'<cite class="pl-20 pr-20">'+v.receiverMobile+'</cite>'+v.detailAddress+'</h5>';
                    html+='<h6><small>申通运单：'+v.stoNo;
                    if(v.log&&v.log.length>0){
                        //判断是否有物流轨迹
                        html+= '<cite class="pl-10"><mark class="pl-5 pr-5">'+v.log[0]['Memo']+'</mark></cite>'+BJToLocal(v.log[0]['ScanDate']);
                        html+= '</small></h6>';
                        html+='</dd><div class="order_show_box"><div class="arrow-up"></div>';
                        //物流轨迹写入html
                        html+= '<ul class="order_info">';
                        $.each(v.log,function (kk,vv) {
                            //只展示前两条
                            if(kk>=2){return false;}
                            if(kk===0){
                                html+='<li class="active clearfix">';
                                html+=   '<ol class="active"></ol>';
                            }else{
                                html+='<li class="clearfix">';
                                html+=   '<ol></ol>';
                            }
                            html+=   '<span>'+BJToLocal(vv.ScanDate)+'</span>';
                            html+='<div class="info_text">'+vv.Memo+'</div></li>';
                        });
                        if(v.log.length>2){
                            html+='<li class="clearfix">';
                            html+=   '<ol></ol>';
                            html+=   '<span></span>';
                            html+='<button class="btn btn-outline radius" onclick="jumpDetail(\''+v.stoNo+'\')">查看更多</button></li>'
                        }
                        html+='</ul></div></dl>';
                    }else {
                       html+='<cite class="pl-10"><mark class="pl-5 pr-5">暂无物流信息</mark></cite>';
                       html+= '</small></h6>';
                       html+='</dd><div class="order_show_box"><div class="arrow-up"></div>暂无物流信息</div></dl>';
                    }

                });
                html+='<dl> <dt style="width: 100%;text-align: center;"><button class="btn btn-secondary-outline radius" onclick="jumpStatus('+statusCode+')">查看更多</button></dt> </dl>';
                bodyObj.html(html);
            }else {
                headerObj.attr('load',0);
                html+=data.message;
                html+='<dl> <dt style="width: 100%;text-align: center;"><button class="btn btn-secondary-outline radius" onclick="jumpStatus('+statusCode+')">查看更多</button></dt> </dl>';

                bodyObj.html(html);
            }
        },
        error:function () {
            headerObj.attr('load',0);
            bodyObj.html('系统错误，请稍后重试');
        }
    });
}


/**
 *  确认收货
 * @param stoNo
 */
function confirmOrder(stoNo) {
    layer.confirm("确认已收到包裹？",function () {
        $.ajax({
            url:confirmOrder_url,
            data:{stoNo:stoNo},
            success:function (data) {
                sysException(data);
                layer.msg(data.message);
                if(data.state){
                    $("#sto"+stoNo).removeClass('btn-primary-outline').addClass('disabled');
                }
            },
            error:function () {
                layer.msg('系统错误，请稍后重试');
            }
        })
    });

}

/**
 * 跳转到对应的包裹列表
 * @param statusCode
 */
function jumpStatus(statusCode) {
    window.location.href=Order_url+"?state="+statusCode;
}

/**
 * 包裹详情
 */
function jumpDetail(stoNo) {
    window.location.href=order_detail_url+"?stoNo="+stoNo;
}