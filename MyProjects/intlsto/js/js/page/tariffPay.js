/**
 * 关税支付
 * Created by Srako on 2017/05/27.
 */

$(function () {
    //表单加载
    $('#table').bootstrapTable({
        url: table_url,
        method: 'get',
        striped: true, //隔行变色
        // height: 550,   //列表高度
        showRefresh: true, //会出现刷新按钮
        clickToSelect: true,
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        undefinedText:'',
        searchAlign: "left", //指定搜索框水平方向的位置
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
        //RowStyle:addClass,//设置自定义行属性 参数为：row: 行数据index: 行下标 返回值可以为class或者css 支持所有自定义属性
        sidePagination: 'server',
        columns: [
            {
                title: '序号',
                align: 'center',
                formatter:function (value,row,index) {
                    var pageNumber = $('#table').bootstrapTable('getOptions').pageNumber,
                        pageSize = $('#table').bootstrapTable('getOptions').pageSize;
                    return (pageNumber-1) * pageSize+index+1;
                },
                width: 50,
            },
            {
                title: '申通单号',
                field: 'stoNo',
                align: 'center',
                width: 200,
            },
            {
                title: '生成时间',
                field: 'createTimeView',
                align: 'center',
                formatter:'BJToLocal',
                width: 160,
            },
            {
                title: '支付时间',
                field: 'payTimeView',
                align: 'center',
                formatter:'BJToLocal',
                width: 160,
            },
            {
                title: '收货人',
                field: 'receiver',
                align: 'center'
            },
            {
                title: '明细',
                field: 'orderGoodsDetail',
                halign: 'center',
            },

            {
                title: '产品线路',
                field: 'productLine',
                align: 'center',
            },

            {
                title: '关税金额('+currencySymbol+')',
                field: 'taxFee',
                align: 'right',
                halign:'center',
                width: 100,
            },
            {
                title: '操作',
                field: 'do',
                align: 'center',
                width: 200,
                formatter:function(value,row,index){
                    var str='<button class="btn  size-MINI ';
                    str+=!row.ifPay?'btn-default disabled':'btn-success';
                    str+='" onclick="Tax(\''+row.stoNo+'\',\'pay\')" >支付</button>&nbsp;';

                    str+='&nbsp;<button class="btn btn-default size-MINI ';
                    str+=!row.ifApply?'btn-default disabled':'btn-default';
                    str+='" onclick="Tax(\''+row.stoNo+'\',\'apply\')" >我要税单</button>';
                    return str;
                }
            }
        ],
        queryParamsType : "",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                stoNo:$("#stoNo").val(),
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
                receiver:$("#receiver").val()
            };
        }
    });



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

    /**
     * 清除日期时间
     */
    $('#date-range16').bind("click",function () {
        $(this).val('');
        $("#beginDate").val('');
        $("#endDate").val('');
    });

});


/**
 * 支付税单
 * @param stoNo  申通单号
 * @param type 类型pay支付，apply我要水单
 * @constructor
 */
function Tax(stoNo,type) {
    var str=(type==='pay')?"确定支付税单":'确定申请税单';
    layer.confirm(str,
        function () {
        $.ajax({
            url:Tax_url,
            data:{stoNo:stoNo,type:type},
            success:function (data) {
                sysException(data);
                if(data.state){
                    parent.refreshNotice();
                    layer.msg(data.message,{end:function () {
                        layer.closeAll();
                        $('#table').bootstrapTable('refresh')
                    }});
                }else {
                    layer.msg(data.message);
                }
            },
            error:function () {
                layer.msg("系统错误，请稍后重试");
            }
        })
    });


}