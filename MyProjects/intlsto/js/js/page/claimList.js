/**
 * 理赔记录列表
 * Created by Srako on 2017/05/26.
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
                width:50,
            },
            {
                title: '申请日期',
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
                title: '理赔描述',
                field: 'content',
                align: 'left',
                halign:'center'
            },
            {
                title: '状态',
                field: 'status',
                align: 'center',
                width: 100
            },
            {
                title: '操作',
                align: 'center',
                width: 150,
                formatter:function(value,row,index){
                    var str='<a href="javascript:void(0)" onclick="detail(\''+row.stoNo+'\')" class="btn btn-primary size-MINI detail">查看</a>';
                    str+='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="cancel(\''+row.stoNo+'\')"  class="btn size-MINI ';
                    str+=row.isCancel!=='1'?'btn-default disabled':' btn-danger';
                    str+='">取消</a>';
                    return str;
                }
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
                stoNo:$("#stoNo").val(),
                beginDate:$("#beginDate").val(),
                endDate:$("#endDate").val(),
            };
        }
    });

    /*tab切换*/
    $('.panel-header span').on('click',function(){
        $(this).addClass('active').siblings().removeClass('active');
        $('.panel .panel-body.box').eq($(this).index()).addClass('panel_body_block').siblings().removeClass('panel_body_block');
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



    /**
     * 理赔提交
     */
    $("#claimAdd").validate({
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
                required:'请输入理赔内容'
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
            //防止重复提交
            var forms    =   $("#claimAdd"),
                button  =   forms.find("button[type='submit']"),
                param   =   forms.serialize();
            button.addClass('disabled');
            loading();
            $.ajax({
                url:claim_url,
                dateType:'json',
                data:param,
                type: 'post', // 提交方式 get/post
                success:function(data) {
                    sysException(data);
                    button.removeClass('disabled');
                    if(data.state===true){
                        layer.msg(data.message,{end:function () {
                            window.location.href=location.href;
                        }});
                    }else {
                        layer.msg(data.message);
                    }
                }
            });

        }
    });

    /**
     * 图片选择展示及修改隐藏域
     */
    $("#upImg").change(function () {
        //判断不大于四张图片
        var imgLength=$("input[name='img[]']").length;
        if(imgLength>=3){
            layer.msg("最多上传3张图片");
            return false;
        }
        runImg(this, function (data) {
            var html='<div class="b_eee upimg"><img src="'+data+'" /><i class="btn_close"></i>';
            html+='<input type="hidden" name="img[]" value="'+data+'"></div>';
            $('#imgList').append(html);
        });

    });

    $('body').on('click',function (e) {
        if (e.target.className==='btn_close'){
            $(e.target).parent('.b_eee').remove();
        }
    });

});


/**
 * 取消理赔
 * @param stoNo
 */
function cancel(stoNo) {
    layer.confirm('确定取消理赔？',function () {
        $.ajax({
            url:cancel_url,
            data:{stoNo:stoNo},
            dataType:'json',
            type:'post',
            success:function (data) {
                sysException(data);
                if(data.state){
                    layer.msg(data.message,{end:function () {
                        $('#table').bootstrapTable('refresh')
                    }});
                }else {
                    layer.msg(data.message);
                }
            },
            error:function () {
                layer.msg('系统错误，请稍后重试');
            }
        });
    });
}

/**
 * 理赔详情查看
 * @param stoNo
 */
function detail(stoNo) {
    layer.open({
        type: 2,
        title: '查看反馈',
        shadeClose: true,
        maxmin: true, //开启最大化最小化按钮
        area: ['600px', '400px'],
        content: detail_url+"?stoNo="+stoNo,
        success:function () {
            parent.refreshNotice();
        }
    });
}