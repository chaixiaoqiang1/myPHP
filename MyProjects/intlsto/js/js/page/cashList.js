/**
 * 充值明细
 * Created by Srako on 2017/06/14.
 */
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
                title: '交易流水号',
                align: 'center',
                field:'cashSn',
                width: 200
            },
            {
                title: '时间',
                field: 'createTime',
                align: 'center',
                formatter:'BJToLocal',
                width: 160,
            },
            {
                title: '账号类型',
                field: 'paymentName',
                align: 'center',
                width: 100,
            },
            {
                title: '提现账号',
                field: 'bankNo',
                halign: 'center',
                align:'left',
                width: 200,
            },
            {
                title: '提现金额('+currencySymbol+')',
                field: 'amount',
                halign: 'center',
                align: 'right',
                width: 100,
            },
            {
                title: '备注',
                field: 'remarks',
                align: 'left',
                halign:'center',
            },
            {
                title: '提现状态',
                field: 'status',
                align: 'center',
                width: 100,
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
                status:$("select[name='status']").val()
            };
        }
    });


});