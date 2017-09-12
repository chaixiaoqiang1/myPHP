/**
 *仓内服务记录列表
 **/
$(function(){
    $('#table').bootstrapTable({
        url: orderServices,
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
        //RowStyle:addClass,//设置自定义行属性 参数为：row: 行数据index: 行下标 返回值可以为class或者css 支持所有自定义属性
        sidePagination: 'server',
        responseHandler:function (res) {sysException(res);return res;},
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
                title: '海外快递单号',
                field: 'trackingNo',
                align: 'left',
                halign:'center',
                width: 280
            },
            {
                title: '海外收货仓库',
                field: 'orgName',
                align: 'center',
                width: 150
            },
            {
                title: '申请时间',
                field: 'createTime',
                align: 'center',
                formatter:'BJToLocal',
                width: 160
            },
            {
                title: '服务项目',
                field: 'servicesName',
                align: 'center',
                width: 150
            },
            {
                title: '服务费用('+currencySymbol+')',
                // field: 'serviceCost',
                align: 'right',
                width: 80,
                formatter:function(value,row,index) {
                    var html = '';
                    html += toDecimal(row.serviceCost);
                    return html;
                }
            },
            {
                title: '操作',
                field: 'do',
                align: 'center',
                width: 200,
                formatter:function(value,row,index){
                    var html = '';
                    if(row.serviceFeedback){
                        html += '<a class="btn btn-primary size-MINI" onclick="showImg($(this),\''+row.recordId+'\')" aul="'+row.serviceFeedback+'" href="javascript:void(0);"><i class="Hui-iconfont Hui-iconfont-yanjing"></i></a>';
                    }else{
                        html += '<a class="btn btn-default size-MINI" href="javascript:void(0);" disabled="disabled"><i class="Hui-iconfont Hui-iconfont-yanjing"></i></a>';
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
                consolidatorNo:$("#consolidatorNo").val(),
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val()
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

/*显示*/
function showImg(obj,recordId){
    var strURL = obj.attr("aul");
    var UrlArr = strURL.split(',');
    var strJson = "";
    $.each(UrlArr,function(k,v){
        strJson += "{\"id\":"+k+",\"src\":\""+v+"\"},";
    });
    strJson = eval("[" + strJson + "]");

    layer.photos({
        photos: {
            "title": "", //相册标题
            "id": 123, //相册id
            "start": 0, //初始显示的图片序号，默认0
            "data": strJson
        },
        shade:0.3,
        anim: 1
    });
    $.ajax({
        url:orderServicesStatus,
        data:{recordId:recordId},
        type:'post',
        dataType:'json',
        success:function (data) {
        }
    });


}














