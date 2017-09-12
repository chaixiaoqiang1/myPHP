/**
 * 交易明细
 * Created by Srako on 2017/05/15.
 */
$(function () {
    $('#table').bootstrapTable({
        url: table_url,
        method: 'get',
        striped: true,
        // height: 500,
        showRefresh: true, //会出现刷新按钮
        clickToSelect: true,
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        undefinedText:'',
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
        detailView: false,
        sidePagination: 'server',
        columns: [
            {
                title: '交易流水号',
                align: 'center',
                field:'transactionNo',
                width: 200,
            },
            {
                title: '类型',
                field: 'lgTypeView',
                align: 'center',
                width: 100,
            },
            {
                title: '时间',
                field: 'createTimeView',
                align: 'center',
                formatter:'BJToLocal',
                width: 160,
            },
            {
                title: '交易金额('+currencySymbol+')',
                field: 'lgAmount',
                align: 'right',
                halign:'center',
                width: 100,
            },
            {
                title: '当前余额('+currencySymbol+')',
                field: 'currentAmount',
                align: 'right',
                halign:'center',
                width: 100,
            },
            {
                title: '备注',
                field: 'lgDesc',
                halign: 'center'
            }
        ],
        queryParamsType : "",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
                lgType:$("select[name='lgType']").val(),
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
            autoClose:true,
            startDate: '2017-01-01',/*日期选择的初始值*/
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