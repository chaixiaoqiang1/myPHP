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
                field:'rechargeSn',
            },
            {
                title: '类型',
                field: 'paymentName',
                align: 'center',
            },
            {
                title: '时间',
                field: 'createTime',
                align: 'center',
                formatter:'BJToLocal',
            },
            {
                title: '金额('+currencySymbol+')',
                field: 'amount',
                align: 'right',
                halign:'center',
            },
            {
                title: '充值状态',
                field: 'status',
                align: 'center',
            },

            {
                title: '操作',
                field: 'remarks',
                align: 'center',
                formatter:function(value,row,index){
                    var html = '<button class="btn size-MINI ';
                    html+=row.succeed?'btn-default disabled':' btn-danger';
                    html += '" onclick="reRecharge(\''+row.rechargeSn+'\',\''+row.paymentCode+'\')" >重新充值</button>';
                    return html;
                }
            }
        ],
        queryParamsType : "",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                payCode:$("select[name='payCode']").val(),
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
                status:$("select[name='status']").val()
            };
        }
    });


});



/**
 * 重新充值
 * @param rechargeSn
 * @param paymemntCode
 */
function reRecharge(rechargeSn,paymemntCode) {
    loading();
    $.ajax({
        url:reRecharge_url,
        data:{rechargeSn:rechargeSn},
        success:function (data) {
            sysException(data);
            if(typeof (data)=='object'&&!data.state){layer.msg(data.message);return false;}
            if($.inArray(paymemntCode,['wxpay'])!=-1){
                layer.open({
                    type: 1,
                    title: '请使用微信扫描二维码完成支付',
                    skin: 'layui-layer-rim', //加上边框
                    area: ['300px', '360px'], //宽高
                    content: '<img style="width: 90%" src="'+data.code_img_url+'"><br/><a class="btn btn-default" href="'+data.over_url+'" style="width: 150px;" type="button">已完成支付</a>'
                });
            }else {
                layer.open({
                    type: 1,
                    title:"充值",
                    content: data,
                    end:function () {
                        $('#table').bootstrapTable('refresh');
                    }
                });
            }
        }
    });

}