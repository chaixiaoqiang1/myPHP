/**
 * 税单查询
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
        pageSize: 5,
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
                title: '序号',
                align: 'center',
                width: 50,
                formatter:function (value,row,index) {
                    var pageNumber = $('#table').bootstrapTable('getOptions').pageNumber,
                        pageSize = $('#table').bootstrapTable('getOptions').pageSize;
                    return (pageNumber-1) * pageSize+index+1;
                }
            },
            {
                title: '申请日期',
                field: 'applyTaxTimeView',
                align: 'center',
                formatter:'BJToLocal',
                width: 160,
            },
            {
                title: '申通单号',
                field: 'stoNo',
                align: 'center',
                width: 200,
            },
            {
                title: '收货人',
                field: 'receiver',
                align: 'center',
                width: 150,
            },
            {
                title: '明细',
                field: 'orderGoodsDetail',
                halign:'center',
            },
            {
                title: '税单链接',
                field: 'do',
                align: 'center',
                width: 100,
                formatter:function(value,row,index){
                    var str='<button class="btn  size-MINI ';
                    str+=!row.ifLook?'btn-default disabled':'btn-primary';
                    str+=' " onclick="imgOpen(\''+row.stoNo+'\',\''+row.taxUrl+'\')" > <i class="Hui-iconfont Hui-iconfont-fabu"></i> </button>';
                    return str;
                }
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber, //页数
                pageSize: params.pageSize,  // 长度
                stoNo:$("#stoNo").val(),
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
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
 * 税单图片查看
 * @param stoNo
 * @param img
 */
function imgOpen(stoNo,img) {
    layer.photos({
        photos: {"data": [{"src": img}]},
        shade:0.3,
        shadeClose:true,
        shift: 1,
        anim: 1,
        success:function () {
            $.ajax({
                url:taxSee_url,
                data:{'stoNo':stoNo},
                success:function (data) {
                    if(data.state){
                        //税单查看成功，执行数量更新操作
                        parent.refreshNotice();
                    }
                }
            });
        }
    });
}
