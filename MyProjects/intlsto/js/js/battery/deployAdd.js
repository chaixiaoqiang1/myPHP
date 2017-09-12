/**
 * BMS商家API绑定-添加
 */
$(function () {
    $("#deploy_add").validate({
        rules:{
            platform:{
                required:true,
            },
            apiId:{
                required:true,
                isContainsSpecialChar:true,
            },
            key:{
                required:true,
                isContainsSpecialChar:true,
            }
        },
        messages: {
            platform:{
                required:'请选择订单来源',
            },
            apiId:{
                required:'请输入API Id'
            },
            key:{
                required:'请输入Key'
            },
        },
        success:"valid",
        errorElement:'div',
        onkeyup:false,
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(){
            //防止重复提交
            var forms    =   $("#deploy_add"),
                button  =   forms.find("button[type='submit']"),
                param   =   forms.serialize();
            button.addClass('disabled');
            loading();
            $.ajax({
                dateType:'json',
                data:param,
                type: 'post', // 提交方式 get/post
                success:function(data) {
                    sysException(data);
                    button.removeClass('disabled');
                    if(data.state){
                        layer.msg(data.message,{end:function () {closeThis()}});
                    }else {
                        layer.msg(data.message);
                    }
                }
            });
        }
    });
});

function closeThis() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}