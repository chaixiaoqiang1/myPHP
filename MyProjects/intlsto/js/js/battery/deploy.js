/**
 * BMS配置
 */
$(function () {
    $('#table').bootstrapTable({
        url: deploy_url,
        columns: [
            {
                field: 'num',
                title: '序号',
                align: 'center',
                width: 50,
                formatter:function (value,row,index) {
                    var pageNumber = $('#table').bootstrapTable('getOptions').pageNumber,
                        pageSize = $('#table').bootstrapTable('getOptions').pageSize;
                    return (pageNumber-1) * pageSize+index+1;
                },
            },
            {
                field: 'configureName',
                title: '订单来源',
                align: 'center',
                width: 100,
            },
            {
                title: 'API-ID',
                align: 'center',
                formatter:function (value,row,index) {
                    var platformConfig=row.platformConfig.split(',');
                    return platformConfig[0];
                },
            },
            {
                title: 'Key',
                align: 'center',
                formatter:function (value,row,index) {
                    var platformConfig=row.platformConfig.split(',');
                    return platformConfig[1];
                },
            },
            {
                field: 'modifyTimeFormat',
                title: '时间',
                align: 'center',
                formatter:'BJToLocal',
                width: 150,
            },
            {
                field: 'statusText',
                title: '状态',
                align: 'center',
                width: 100,
            },
            {
                field: 'do',
                title: '操作',
                align: 'center',
                width: 100,
                formatter: function (value,row,index) {
                    return '<button class="btn btn-success radius size-MINI" onclick="deployUpdate(\''+row.configureId+'\')">修改</button>' ;
                }
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                platformCode:$("select[name='platformCode']").val(),
                status:$("select[name='status']").val()
            };
        },
        striped: true,
        toolbar: '#toolbar', //工具按钮用哪个容器
        showRefresh: true, //会出现刷新按钮
        clickToSelect: true,
        pagination:true,
        method: 'get',
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        buttonsAlign: "right",
        undefinedText:'',
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
    });
});

/**
 * 添加BMS配置
 */
function deployAdd() {
    $('button').blur();
    layer.open({
        title:"添加BMS商家API绑定",
        type:2,
        area:['450px','320px'],
        content:deploy_add_url,
        end:function () {
            $("#table").bootstrapTable('refresh');
        }
    })
}


/**
 * 修改BMS配置
 * @param configureId
 */
function deployUpdate(configureId) {
    $('button').blur();
    layer.open({
        title:"修改BMS商家API绑定",
        type:2,
        area:['450px','320px'],
        content:deploy_update_url+'?configureId='+configureId,
        end:function () {
            $("#table").bootstrapTable('refresh');
        }
    })
}