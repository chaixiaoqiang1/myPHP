/**
 * BMS订单批量导入
 */
$(function () {
    $("#order_input").on('click',function (e) {
        var configureId=$("select[name='configureId']").val(),
            startTime=$("input[name='startTime']").val(),
            endTime=$("input[name='endTime']").val();
        if(configureId==''){layer.msg("请选择订单来源");return false;}
        if(startTime==''){layer.msg("请选择开始时间");return false;}
        if(endTime==''){layer.msg("请选择结束时间");return false;}

        $(e).addClass('disabled');
        loading();
        $.ajax({
            dateType:'json',
            data:{configureId:configureId,startTime:startTime,endTime:endTime},
            type: 'post', // 提交方式 get/post
            success:function(data) {
                sysException(data);
                $(e).removeClass('disabled');
                if(data.state){
                    layer.msg(data.message,{end:function () {closeThis();$("#table",parent.document).bootstrapTable('refresh');}});
                }else {
                    layer.msg(data.message);
                }
            }
        });
    });

});

function closeThis() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}