/**
 * 用户登录
 * Created by Srako on 2017/05/11.
 */
$(function () {

    //判断当前页是否在iframe之中,在，则从新打开当前页面
    if (self.frameElement&&self.frameElement.tagName === "IFRAME") {
        var href=location.href.split('?');
        top.location.href=href[0];
    }

    //添加验证码验证不能为空
    $.validator.addMethod("imgCode",function(value){
        if($("#imgCode").is(":visible")===false){
            return true;
        }
        return value.length===5;
    },"验证码不能为空");

    $("#login").validate({
        rules:{
            userName:{
                required:true
            },
            password:{
                required:true

            },
            verify:{
                imgCode:true
            }
        },
        messages: {
            userName:{
                required:'会员账号不能为空'
            },
            password:{
                required:'密码不能为空'
            }
        },
        errorElement:"div",   //错误展示的标签
        onkeyup:false,
        errorPlacement:function(error,element) {
            //错误展示位置
            if ($(element).parent().hasClass('member-login')) {
                //验证码错误展示在父级后面
                error.insertAfter(element.parents(".formControls"));
            }else {
                error.appendTo(element.parents(".formControls"));
            }
        },
        success:function (label) {$(label).remove();},
        submitHandler:function(){
            //防止重复提交
            var forms    =   $("#login"),
                button  =   forms.find("button"),
                param   =   forms.serialize();
            button.addClass('disabled');
            $.ajax({
                dateType:'json',
                data:param,
                type: 'post', // 提交方式 get/post
                success:function(data, textStatus, request) {
                    sysException(data);
                    if(data.state){
                        window.location.href=index_url;
                    }else{
                        button.removeClass('disabled');
                        if(data.isCode){
                            $("#imgCode").show();
                            freshCode();
                        }
                        layer.msg(data.message);
                        return false;
                    }
                }
            });

        }

    });

});


/**
 * 刷新验证码
 */
function freshCode() {
    var obj=$(".code_login");
    var src=obj.attr('src').split("?");
    obj.attr('src',src[0]+'?tm='+Math.random());
}
