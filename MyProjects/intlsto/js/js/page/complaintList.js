/**
 * 投诉建议列表
 * Created by Srako on 2017/05/26.
 */
$(function () {
    //如果展示为1，切换投诉为选中状态
    if(active==='1'){
        $("#com").addClass('active').siblings().removeClass('active');
        $('.panel .panel-body').eq(1).addClass('panel_body_block').siblings().removeClass('panel_body_block');
    }


    /*tab切换*/
    $('.panel-header span').on('click',function(){
        $(this).addClass('active').siblings().removeClass('active');
        $('.panel .panel-body').eq($(this).index()).addClass('panel_body_block').siblings().removeClass('panel_body_block');
        //判断第二个表格是否加载，未加载则加载
        if($(this).attr('see')==='0'){
            loadComplaint();
            $(this).attr('see','1');
        }
    });

    //建议
   $('#advice').validate({
       rules:{
           stoNo:{
               required:true,
               rangelength:[10,30]
           },
           content:{
               required:true,
           }
       },
       messages:{
           stoNo:{
               required:'请输入申通单号',
               rangelength:'申通单号格式不正确'
           },
           content:{
               required:'请输入建议内容'
           }
       },
       onkeyup:false,
       focusCleanup:true,
       success:"valid",
       errorElement:'div',
       errorPlacement:function(error,element) {
           error.appendTo(element.parents(".formControls"));
       },
       submitHandler:function(form){
           var forms    =   $("#advice"),
               button  =   forms.find('button'),
               param   =   forms.serialize();
           button.addClass('disabled');
           loading();
           $.ajax({
               url:advice_url,
               dateType:'json',
               data:param,
               type: 'post', // 提交方式 get/post
               success:function(data) {
                   sysException(data);
                   button.removeClass('disabled');
                   if(data.state===true){
                       layer.msg(data.message,{end:function () {
                           document.getElementById('advice').reset();
                           $("#adviceTable").bootstrapTable('refresh');
                       }});
                   }else {
                       layer.msg(data.message);
                   }
               }
           });

       }
   });

    //投诉
    $('#complaint').validate({
        rules:{
            stoNo:{
                required:true,
                rangelength:[10,30]
            },
            content:{
                required:true,
            }
        },
        messages:{
            stoNo:{
                required:'请输入申通单号',
                rangelength:'申通单号格式不正确'
            },
            content:{
                required:'请输入投诉内容'
            }
        },
        onkeyup:false,
        focusCleanup:true,
        success:"valid",
        errorElement:'div',
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(form){
            var forms    =   $("#complaint"),
                button  =   forms.find('button'),
                param   =   forms.serialize();
            button.addClass('disabled');
            loading();
            $.ajax({
                url:complaint_url,
                dateType:'json',
                data:param,
                type: 'post', // 提交方式 get/post
                success:function(data) {
                    sysException(data);
                    button.removeClass('disabled');
                    if(data.state===true){
                        layer.msg(data.message,{end:function () {
                            document.getElementById('complaint').reset();
                            $("#complaintTable").bootstrapTable('refresh');
                        }});
                    }else {
                        layer.msg(data.message);
                    }
                },
                error:function () {
                    layer.msg("系统异常，请稍后重试")
                }
            });

        }
    });

    //建议列表加载
    $('#adviceTable').bootstrapTable({
        url: adviceList_url,
        method: 'get',
        striped: true, //隔行变色
        // height: 500,   //列表高度
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
                title: '序号',
                align: 'center',
                formatter:function (value,row,index) {
                    var pageNumber = $('#adviceTable').bootstrapTable('getOptions').pageNumber,
                        pageSize = $('#adviceTable').bootstrapTable('getOptions').pageSize;
                    return (pageNumber-1) * pageSize+index+1;
                },
                width:50,
            },
            {
                title: '日期',
                field: 'createTime',
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
                title: '内容',
                field: 'content',
                align: 'left',
                halign:'center',
            },
            {
                title: '查看反馈',
                align: 'center',
                width: 100,
                formatter:function(value,row,index){
                    return '<a href="javascript:void(0)" onclick="openUrl(\''+adviceDetail_url+'\',\''+row.stoNo+'\')"   class="btn btn-primary size-MINI detail"> <i class="Hui-iconfont Hui-iconfont-fabu"></i> </a>';

                }
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize
            };
        }
    });
});

//投诉列表加载
function loadComplaint() {
    $('#complaintTable').bootstrapTable({
        url: complaintList_url,
        method: 'get',
        striped: true, //隔行变色
        // height: 500,   //列表高度
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
                title: '序号',
                align: 'center',
                formatter:function (value,row,index) {
                    var pageNumber = $('#complaintTable').bootstrapTable('getOptions').pageNumber,
                        pageSize = $('#complaintTable').bootstrapTable('getOptions').pageSize;
                    return (pageNumber-1) * pageSize+index+1;
                },
                width: 50,
            },
            {
                title: '日期',
                field: 'createTime',
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
                title: '内容',
                field: 'content',
                align: 'left',
                halign:'center'
            },
            {
                title: '查看反馈',
                align: 'center',
                width: 100,
                formatter:function(value,row,index){
                    return '<a href="javascript:void(0)" onclick="openUrl(\''+complaintDetail_url+'\',\''+row.stoNo+'\')"   class="btn btn-primary size-MINI detail"> <i class="Hui-iconfont Hui-iconfont-fabu"></i> </a>';

                }
            }
        ],
        queryParamsType : "",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize
            };
        }
    });
}


/**
 * 打开详情页面
 * @param url
 * @param stoNo
 */
function openUrl(url,stoNo) {

    layer.open({
        type: 2,
        title: '查看反馈('+stoNo+')',
        shadeClose: true,
        maxmin: true, //开启最大化最小化按钮
        area: ['600px', '400px'],
        content: url+'?stoNo='+stoNo,
        success:function () {
            parent.refreshNotice();
        }
    });
}

