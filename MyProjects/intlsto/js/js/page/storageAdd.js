/**
 * 仓储预报
 * Created by Srako on 2017/05/11.
 */
$(function () {
    $('#forecast_add').validate({
        rules:{
            expressCode:{
                required:true
            },
            trackingNo:{
                required:true,
                minlength:11
            },
            inventory:{
                required:true,
            }
        },
        messages: {
            expressCode:{
                required:'请选择海外快递公司'
            },
            trackingNo:{
                required:'请输入正确的快递单号',
                minlength:'请输入大于10位的快递单号'
            },
            inventory:{
                required:'请输入商品的名称、规格、单位等基本信息'
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
            var forms    =   $("#forecast_add"),
                button  =   forms.find("button"),
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
                        layer.msg(data.message,{end:function () {
                            document.getElementById('forecast_add').reset();
                            //关闭当前frame
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        }});
                    }else {
                        layer.msg(data.message);
                    }
                }
            });

        }
    });
});