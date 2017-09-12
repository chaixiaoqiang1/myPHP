/**
 * 仓储列表
 * Created by Srako on 2017/05/15.
 */
var $table,trackingNo=[];
$(function () {
    $table=$('#table').bootstrapTable({
        url: storageList,
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
                align: 'center',valign: 'middle',
                checkbox:true,
                titleTooltip:'全选',
                formatter:function(value,row,index) {
                    return {
                        disabled : row.display !== '1',//设置是否可用
                        checked : $.inArray(row.trackingNo, trackingNo) != -1//设置选中
                    };
                },
                width: 50,
            },
            {
                title: '海外快递单号',
                field: 'trackingNo',
                halign:'center',
                align: 'left',
                width: 250
            },
            {
                title: '下单时间',
                field: 'ordersTime',
                align: 'center',
                formatter:'BJToLocal',
                width: 160
            },
            {
                title: '海外收货仓库',
                field: 'consolidatorName',
                align: 'center',
                width: 150
            },
            {
                title: '重量（'+weightCompany+'）',
                // field: 'forecastWeight',
                align: 'right',
                width: 80,
                formatter:function(value,row,index) {
                    var html = '';
                    html += toDecimal(row.forecastWeight);
                    return html;
                }
            },
            {
                title: '商品明细',
                halign:'center',
                field: 'inventory',
                align: 'left'
            },
            {
                title: '状态',
                field: 'statusTxt',
                align: 'center',
                width: 80
            },
            {
                title: '入库时间',
                field: 'storageTimeFormat',
                align: 'center',
                formatter:'BJToLocal',
                width: 160
            },
            {
                title: '库存天数',
                field: 'inventoryDays',
                align: 'center',
                width: 80
            },

            {
                title: '操作',
                field: 'do',
                align: 'center',
                width: 320,
                formatter:function(value,row,index){
                    //display(1都展示，2只展示增值服务)
                    var html = '';
                    if(row.display==='1'){
                        //没有仓内服务，并且未出库
                        html += '<a href="'+orderAdd+'?NO='+row.trackingNo+'" class="btn btn-primary size-MINI">发货</a>';
                        html += '&nbsp;&nbsp;<a href="'+abandonPackage+'?NO='+row.trackingNo+'" class="btn btn-warning size-MINI">弃货</a>';
                        html += '&nbsp;&nbsp;<a href="'+returnPackage+'?NO='+row.trackingNo+'"  class="btn btn-danger size-MINI">退货</a>';
                        html += '&nbsp;&nbsp;<a href="'+servicePackage+'?NO='+row.trackingNo+'&consolidatorNo='+row.consolidatorNo+'" class="btn btn-secondary size-MINI">增值服务</a>';
                    } else if(row.display==='2'){
                        //没有出库
                        html += '<a href="javascript:void(0);" disabled="disabled" class="btn btn-default size-MINI">发货</a>';
                        html += '&nbsp;&nbsp;<a href="javascript:void(0);"  disabled="disabled"  class="btn btn-default size-MINI">弃货</a>';
                        html += '&nbsp;&nbsp;<a href="javascript:void(0);" disabled="disabled"  class="btn btn-default size-MINI">退货</a>';
                        html += '&nbsp;&nbsp;<a href="'+servicePackage+'?NO='+row.trackingNo+'&consolidatorNo='+row.consolidatorNo+'" class="btn btn-secondary size-MINI">增值服务</a>';
                    }else {
                        html += '<a href="javascript:void(0);" disabled="disabled" class="btn btn-default size-MINI">发货</a>';
                        html += '&nbsp;&nbsp;<a href="javascript:void(0);" disabled="disabled" class="btn btn-default size-MINI">弃货</a>';
                        html += '&nbsp;&nbsp;<a href="javascript:void(0);" disabled="disabled"  class="btn btn-default size-MINI">退货</a>';
                        html += '&nbsp;&nbsp;<a href="javascript:void(0);" disabled="disabled" class="btn btn-default size-MINI">增值服务</a>';
                    }
                    if(row.canDeleteUpdate){
                        html += '&nbsp;&nbsp;<button  onclick="updateStorage(\''+row.trackingNo+'\')"  class="btn btn-success size-MINI">修改</button>';
                        html += '&nbsp;&nbsp;<button  onclick="delStorage(\''+row.forecastId+'\')" class="btn btn-danger size-MINI">删除</button>';
                    }else{
                        html += '&nbsp;&nbsp;<a  href="javascript:void(0);" disabled="disabled" class="btn btn-default size-MINI">修改</a>';
                        html += '&nbsp;&nbsp;<a  href="javascript:void(0);" disabled="disabled" class="btn btn-default size-MINI">删除</a>';
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
                status:$("#status").val(),
                startDate:$("#beginDate").val(),
                endDate:$("#endDate").val()
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
            return row.trackingNo;
        });
        func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference';
        trackingNo = _[func](trackingNo, ids);
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


    /**
     * 仓储预报
     */
    $('#storageAdd').click(function () {
        $(this).blur();
        layer.open({
            type: 2,
            title:"仓储预报",
            area: ['700px', '430px'], //宽高
            content: storageAdd,
            end:function () {
                $("#table").bootstrapTable('refresh');
            }
        });
    });

    /**
     * 合箱发货
     */
    $('#tanksSendGood').click(function () {
        if(trackingNo.length <= 1){
            layer.msg("请选择两个或以上的合箱包裹");
            return false;
        }
        window.location.href=tanksSendGood+'?trackingNo='+trackingNo.join();
    });

    /*分箱发货*/
    $('#separate').on('click',function(){
        if(trackingNo.length <= 0){
            layer.msg("请选择一个需要分箱的包裹");
            return false;
        }
        window.location.href=separateSendPackage+"?no="+trackingNo.join();
    })

});


//仓储修改
function updateStorage(trackNo){
    layer.open({
        type: 2,
        title:"仓储预报",
        area: ['700px', '430px'], //宽高
        content: storageAdd+'?trackNo='+trackNo,
        end:function () {
            $("#table").bootstrapTable('refresh');
        }
    });
}

//删除
function delStorage(forecastId){
    layer.confirm('确定要删除预报吗？', function(){
        $.ajax({
            url:storageDel,
            type:'post',
            data:{forecastId:forecastId},
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