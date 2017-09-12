/**
 * BMS订单列表
 */
var $table,idList = [];  //保存选中ids
$(function () {
    //日期选择
    $('#date-range16').dateRangePicker(
        {
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

    //批量导入
    $("#bulk_import").on('click',function () {
        layer.open({
            type: 2,
            title:"BMS订单批量导入",
            content: order_input_url,
            area: ['600px', '300px'],
        });
    });

    //生成运单
    $("#order_build").on('click',function () {
        if(idList.length<=0){layer.msg('请至少选择一个包裹！');return false;}
        $.ajax({
            url:is_address_url,
            success:function (data) {
                if(data.state){
                    var index = layer.open({
                        type: 2,
                        title:"生成运单",
                        content: order_build_url+'?idList='+idList.join(','),
                        area: ['900px', '100%'],
                        maxmin: true
                    });
                    layer.full(index);
                }else {
                    layer.msg("请先设置默认发货地址")
                }
            }
        });

    });

    $table = $('#table').bootstrapTable({
        url: table_url,
        striped: true,
        toolbar: '#toolbar', //工具按钮用哪个容器
        showRefresh: true, //会出现刷新按钮
        clickToSelect: true,
        pagination:true,
        undefinedText:'',
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        buttonsAlign: "right",
        /*操作按钮在水平方向的位置*/
        paginationHAlign: "left",
        /*分页条在水平方向的位置*/
        paginationDetailHAlign: "right",
        /*分页详细信息在水平方向的位置*/
        paginationPreText: "上一页",
        /*指定上一页的内容*/
        paginationNextText: "下一页",
        responseHandler:function (res) {sysException(res);return res;},
        sidePagination: 'server',
        columns: [
            {
                checkbox: true,
                align: 'center',
                valign: 'middle',
                titleTooltip:'全选',
                formatter:function(value,row,index) {
                    return {
                        disabled : $.inArray(row.platformStatus, [103,104]) != -1,//设置是否可用
                        checked : $.inArray(row.apiOrdersId, idList) != -1//设置选中
                    };
                },
                width: 50,
            },
            {
                field: 'tradeId',
                title: '订单号',
                width: 200,
                align: 'center'
            },
            {
                field: 'tradeCreateTimeText',
                title: '创建时间',
                width: 150,
                align: 'center',
                formatter:'BJToLocal'
            },
            {
                field: 'platformName',
                title: '订单涞源',
                width: 100,
                align: 'center'
            },
            {
                field: 'createTimeText',
                title: '导入时间',
                width: 150,
                align: 'center',
                formatter:'BJToLocal'
            },
            {
                field: 'platformStatusTest',
                title: '状态',
                width: 100,
                align: 'center'
            },
            {
                field: 'receiverName',
                title: '收件人',
                width: 100,
                align: 'center'
            },
            {
                title: '收件人电话',
                width: 150,
                align: 'center',
                formatter:function (value,row,index) {
                    return row.receiverMobile==''?row.receiverPhone:row.receiverMobile;
                },
            },
            {
                field: 'receiverProvince',
                title: '收件人省份',
                width: 100,
                align: 'center'
            },
            {
                field: 'receiverAddress',
                title: '收件人地址',
                align: 'center'
            },
            /*{
                field: 'selladd',
                title: '卖家备注',
                align: 'center'
            },
            {
                field: 'shopmessage',
                title: '买家留言',
                align: 'center'
            },*/
            {
                field: 'do',
                title: '操作',
                width: 100,
                align: 'center',
                formatter:function (value,row,index) {
                    return '<button class="btn btn-danger radius size-MINI" onclick="delOrder(\''+row.apiOrdersId+'\')">删除</button>' ;
                },
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                platformCode:$("select[name='platformCode']").val(),
                platformStatus:$("select[name='platformStatus']").val(),
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
            };
        },
    });


    //选中事件操作数组
    var union = function(array,ids){
        $.each(ids, function (i, id) {
            if($.inArray(id,array)==-1){
                array[array.length] = id;
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
            return row.apiOrdersId;
        });
        func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference';
        idList = _[func](idList, ids);
    });

});

/**
 * 删除一个导入的订单
 * @param apiOrdersId
 */
function delOrder(apiOrdersId) {
    layer.confirm("确认删除订单？",function () {
        loading();
        $.ajax({
            url:del_order_url,
            data:{apiOrdersId:apiOrdersId},
            type:'post',
            success:function (data) {
                sysException(data);
                layer.msg(data.message,{end:function () {
                    if(data.state){$("#table").bootstrapTable('refresh');}
                }});
            }
        })
    })
}