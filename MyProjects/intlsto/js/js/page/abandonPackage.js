/**
 * 弃货申请
 */
$(function(){
    $("#form-abandon").validate({
        rules:{
            remark:{
                required:true
            }
        },
        messages:{
            remark:{
                required:"请输入弃货原因",
            }
        },
        success:"valid",
        errorElement:'div',
        onkeyup:false,
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(form){
            loading();
            $(form).ajaxSubmit({
                url:abandonPackage,
                type: 'post',
                dataType:'json',
                success:function(re){
                    sysException(re);
                    if(re.state == 1){
                        layer.msg(re.message);
                        setTimeout(function(){
                            window.location.href=history.go('-1');
                        },500);

                    }else{
                        layer.msg(re.message);
                    }
                }
            });
        }
    });
    $('#go_back').on('click',function(){
        location.href=history.go('-1');
    })
});