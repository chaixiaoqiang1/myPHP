/**
 * 我的包裹列表
 * Created by Srako on 2017/05/15.
 */
var $table,stoNo=[];
var index = 0;
var payIndex = 0;
$(function () {

    $table = $('#table').bootstrapTable({
        url: orderList,
        method: 'get',
        striped: true, //隔行变色
        // height: 550,   //列表高度
        showRefresh: true, //会出现刷新按钮
        clickToSelect: true,
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        undefinedText:'',
        searchAlign: "left", //指定搜索框水平方向的位置
        /*操作按钮在水平方向的位置*/
        paginationHAlign: "left",
        /*分页条在水平方向的位置*/
        paginationDetailHAlign: "right",
        /*分页详细信息在水平方向的位置*/
        paginationPreText: "上一页",
        /*指定上一页的内容*/
        paginationNextText: "下一页",
        responseHandler:function (res) {sysException(res);return res;},
        //RowStyle:addClass,//设置自定义行属性 参数为：row: 行数据index: 行下标 返回值可以为class或者css 支持所有自定义属性
        sidePagination: 'server',
        columns: [
            {
                align: 'center',
                valign: 'middle',
                checkbox:true,
                titleTooltip:'全选',
                formatter:function(value,row,index) {
                    return {
                        checked : $.inArray(row.stoNo, stoNo) != -1
                    };
                },
                width: 50,
            },
            {
                title: '订单号',
                field: 'refrenceNo',
                align: 'center',
                width: 220
            },
            {
                title: '海外快递单号',
                field: 'trackingNo',
                align: 'left',
                halign:'center',
                width: 250,
                formatter:function(value,row,index){
                    var html = '';
                    if(row.flag == 2){
                        var trackingNoArr = '';
                        html += '<span';
                        if(row.listIntlOrderTrackingNo.length >1){
                            $.each(row.listIntlOrderTrackingNo,function(k,v){
                                trackingNoArr += v.trackingNo+',';
                            });
                            html +=' onmouseenter  ="showTrackingNo(\''+trackingNoArr+'\',this)"';
                        }
                        html += '>'+row.listIntlOrderTrackingNo[0]['trackingNo'];
                        if(row.listIntlOrderTrackingNo.length >1){
                            html += '<i class="icon iconfont icon-gengduo8 ml-5"></i>';
                        }
                        html += '</span>';
                    }else{
                        if(row.trackingNo != null){
                            html += row.trackingNo;
                        }
                    }
                    return html;
                }
            },
            {
                title: '申通单号',
                field: 'stoNo',
                align: 'center',
                width: 150
            },
            {
                title: '海外收货仓库',
                field: 'consolidatorName',
                align: 'center',
                width: 150
            },
            {
                title: '下单时间',
                field: 'strCreateTime',
                align: 'center',
                formatter:'BJToLocal',
                width: 170
            },
            {
                title: '收货城市',
                field: 'receiverCity',
                align: 'center',
                width: 150
            },
            {
                title: '收货人',
                field: 'receiverName',
                align: 'center'
            },
            {
                title: '包裹状态',
                field: 'strState',
                align: 'center',
                width: 100
            },
            {
                title: '支付状态',
                field: 'strPayState',
                align: 'center',
                width: 80
            },
            {
                title: '操作',
                field: 'do',
                align: 'center',
                width: 190,
                formatter:function(value,row,index){
                    var html = '';
                    html += '<a title="包裹详情" href="'+orderDetail+'?stoNo='+row.stoNo+'" class="btn btn-success size-MINI radius"><i class="Hui-iconfont Hui-iconfont-yanjing"></i></a>';

                    html += '&nbsp;&nbsp;<a title="打印" onclick="CreatePrintPage(\''+row.stoNo+'\')" href="#" class="btn btn-primary size-MINI radius"><i class="Hui-iconfont Hui-iconfont-dayinji"></i></a>';
                    if(row.payState == 1){
                        html += '&nbsp;&nbsp;<a title="付款确认" href="javascript:void(0);" onclick="payOrderCost(\''+row.stoNo+'\')" class="btn btn-secondary size-MINI radius"><i class="Hui-iconfont Hui-iconfont-hongbao"></i></a>';
                    }else if(row.payState == 0){
                        html += '&nbsp;&nbsp;<a title="未入库不可扣款" href="javascript:void(0);"  class="btn btn-default size-MINI disabled radius"><i class="Hui-iconfont Hui-iconfont-hongbao"></i></a>';
                    }else{
                        html += '&nbsp;&nbsp;<a title="已付款" href="javascript:void(0);"  class="btn btn-default size-MINI disabled radius"><i class="Hui-iconfont Hui-iconfont-hongbao"></i></a>';
                    }
                    if(row.state ==1){
                        html += '&nbsp;&nbsp;<a title="修改" href="'+orderUpdate+'?stoNo='+row.stoNo+'&&companyNo='+row.companyNo+'" class="btn btn-secondary size-MINI radius c-white"><i class="Hui-iconfont Hui-iconfont-edit"></i></a>';
                        html += '&nbsp;&nbsp;<a title="删除包裹" href="javascript:void(0)" onclick="delOne(\''+row.stoNo+'\')" class="btn btn-warning size-MINI radius "><i class="Hui-iconfont Hui-iconfont-del3"></i></a>';
                    }else{
                        html += '&nbsp;&nbsp;<a title="修改" href="javascript:void(0);" class="btn btn-default size-MINI disabled radius"><i class="Hui-iconfont Hui-iconfont-edit"></i></a>';
                        html += '&nbsp;&nbsp;<a title="删除包裹" href="javascript:void(0)" class="btn btn-default size-MINI disabled radius"><i class="Hui-iconfont Hui-iconfont-del3"></i></a>';
                    }
                    return html;
                }
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                trackingNo:$("#trackingNo").val(),
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
                stoNo:$("#stoNo").val(),
                consolidatorNo:$("#consolidatorNo").val(),
                state:$("#state").val(),
                payState:$("#payState").val()
            };
        }
    });


    //选中事件操作数组
    var union = function(array,ids){
        $.each(ids, function (i, id) {
            if($.inArray(id,array)==-1){
                array.push(id);
            }
        });
        return array;
    };
    //取消选中事件操作数组
    var difference = function(array,ids){
        $.each(ids, function (i, id) {
            var index = $.inArray(id,array);
            if(index!=-1){
                array.splice(index, 1);
            }
        });
        return array;
    };
    var _ = {"union":union,"difference":difference};
    //绑定选中事件、取消事件、全部选中、全部取消
    $table.on('check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table', function (e, rows) {
        var ids = $.map(!$.isArray(rows) ? [rows] : rows, function (row) {
            return row.stoNo;
        });
        func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference';
        stoNo = _[func](stoNo, ids);
    });

    //日期选择
    $('#date-range16').dateRangePicker(
        {
            ranges: {
                '清空': [null, null]
            },
            showShortcuts: false,/*是否需要点击确定按钮*/
            format: 'YYYY-MM-DD',
            separator :' 至 ',
            language:'cn',
            startDate: '2017-01-01',/*日期选择的初始值*/
            autoClose:true,
        }).bind('datepicker-change', function(evt, obj) {
        /*绑定事件变化*/
        $("input[name='beginDate']").val(formatDate(obj.date1));
        $("input[name='endDate']").val(formatDate(obj.date2));

        /*获取事件的开始和结束值*/
    });

    /* 清除日期时间 */
    $('#date-range16').bind("click",function () {
        $(this).val('');
        $("#beginDate").val('');
        $("#endDate").val('');
    });

    /*点选*/
    $('.print_box ul li').on('click',function(){
        $(this).addClass('active').siblings().removeClass('active');
    });

    /* 支付提交*/
    $("#submitPay").on("click",function(){
        var payVal = $("#payPassword_rsainput").val();
        var sto = $("#paySto").val();
        if(payVal == '' || sto == ''){
            return false;
        }
        $('#cardwrap').css('left','0px');
        $("#payPassword_rsainput").val('');
        $(".sixDigitPassword-box i").find("b").css("visibility","hidden");
        $.ajax({
            url:pay,
            type:'post',
            data:{stoNo:sto,payVal:payVal},
            dataType:'json',
            success:function (data) {
                if(data.state){
                    layer.msg(data.message,{end:function(){
                        $('#table').bootstrapTable('refresh');
                    }});
                    layer.close(payIndex);
                }else {
                    layer.msg(data.message);
                }
            }
        });
    });

    /*面单提交*/
    $("#submit").on("click",function(){
        var id = $("ul .active").children("#expressBillId").val();
        $.ajax({
            url:expressBill,
            type:'post',
            data:{id:id},
            dataType:'json',
            success:function (data) {
                if(data.state){
                    layer.msg(data.message,{end:function(){
                        layer.close(index); //关闭窗体
                    }});
                }else{
                    layer.msg(data.message);
                }
            }
        });
    });

    /*关闭*/
    $("#close").on("click",function(){
        layer.close(index); //关闭窗体
    });

});
/*显示所有海外快递单号*/
function showTrackingNo(arr,obj){
    var trackingArr = arr.split(',');
    var html = '';
    var count = trackingArr.length-2;
    $.each(trackingArr,function(k,v){
        if(v != ''){
            html += v;
            if(count>k){
                html += "<br/>";
            }
        }
    });
    layer.tips(html, $(obj));
}

/*包裹删除*/
function delOne(stoNo){
    layer.confirm('确定删除包裹？', function(){
        $.ajax({
            url:del_url,
            type:'post',
            data:{stoNo:stoNo},
            dataType:'json',
            success:function (data) {
                if(data.state){
                    layer.msg(data.message,{end:function(){
                        $('#table').bootstrapTable('refresh');
                    }});
                }else {
                    layer.msg(data.message);
                }
            },
            error:function () {
                layer.msg('系统错误，请稍后重试');
            }
        })
    });
}

/*包裹付款*/
function payOrderCost(sto){
    payIndex =layer.open({
        type:1,
        title:'付款页面',
        area:['360px;','220px;'],
        content:$('#form_paypsw')
    });
    $("#paySto").val(sto);
}

var LODOP; //声明为全局变量
function CreatePrintPage(waybillNo) {
    LODOP=getLodop();
    if(!LODOP){return false;}
    $.ajax({
        url:batchPrints,
        type:'post',
        data:{waybillNo:waybillNo},
        dataType:'json',
        beforeSend:function(){
            loading();
        },
        success:function(data){
            sysException(data);
            if(data.state){
                var code = data.message;
                eval(code);
                LODOP.PREVIEW();
            }else{
                layer.msg(data.message);
            }
        }
    });
}

/*面单列表*/
function printAllPath(){
   index =layer.open({
        type:1,
        title:'面单格式选择',
        area:['450px','340px'],
        content:$('#printHtml')
    });
}

/*批量打印面单*/
function printAll(){
    if(stoNo.length <= 0){
        layer.msg("请选择一条需要需要打印的包裹");
        return false;
    }
    LODOP=getLodop();
    if(!LODOP){return false;}
    $.ajax({
        url: batchPrints,
        type:'post',
        data:{waybillNo:stoNo.join()},
        dataType:'json',
        beforeSend:function(){
            loading();
        },
        success:function(data){
            sysException(data);
            if(data.state){
                var code = data.message;
                eval(code);
                LODOP.PREVIEW();
            }else{
                layer.msg(data.message);
            }
        }
    });
}

/*全部导出*/
function batchExport(){
    var data = $("#form-article-add").serialize();
    window.location.href = (batchExportUrl+"?"+data);
}

