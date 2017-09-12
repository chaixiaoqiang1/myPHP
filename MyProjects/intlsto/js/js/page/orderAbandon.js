/**
 * 退弃货记录
 * Created by Srako on 2017/05/16.
 */
$(function () {
    /*退货记录*/
    $('#returnTable').bootstrapTable({
        url: returnTable,
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
                title: '海外快递单号',
                field: 'trackingNo',
                align: 'left',
                halign:'center',
                width: 250
            },
            {
                title: '库存费用('+currencySymbol+')',
                field: 'inventoryCost',
                align: 'right',
                halign:'center',
                width: 100
            },
            {
                title: '操作费('+currencySymbol+')',
                field: 'returnCoststr',
                align: 'right',
                halign:'center',
                width: 100
            },
            {
                title: '退货原因',
                field: 'recordReason',
                align: 'left',
                halign:'center'
            },
            {
                title: '退货状态',
                field: 'status',
                align: 'center',
                width: 100
            },
            {
                title: '操作时间',
                field: 'createTime',
                align: 'center',
                formatter:'BJToLocal',
                width: 150
            }

        ],
        queryParamsType : "undefined"
    });

    /*tab切换*/
    $('.panel-header span').on('click',function(){
        $(this).addClass('active').siblings().removeClass('active');
        $('.panel .panel-body').eq($(this).index()).addClass('panel_body_block').siblings().removeClass('panel_body_block');
        if($(this).attr('see')==='0'){
            loadJettison();
            $(this).attr('see','1');
        }
    });

});

//加载弃货记录
function loadJettison() {
    /*弃货记录*/
    $('#jettisonTable').bootstrapTable({
        url: jettisonTable,
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
                title: '海外快递单号',
                field: 'trackingNo',
                align: 'center',
                width: 150
            },
            {
                title: '操作时间',
                field: 'createTime',
                align: 'center',
                formatter:'BJToLocal',
                width: 160
            },
            {
                title: '弃货原因',
                field: 'recordReason',
                halign:'center',
                align: 'left',
                width: 150
            },
        ],
        queryParamsType : "undefined"
    });
}