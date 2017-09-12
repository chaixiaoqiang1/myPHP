/**
 * 发送邮件
 * Created by Srako on 2017/07/20.
 */
$(function () {

    //判断当前页是否在iframe之中,在，则从新打开当前页面
    if (self.frameElement&&self.frameElement.tagName === "IFRAME") {
        top.location.href=location.href;
    }

    //失焦验证账号名是否存在
    $("input[name='memberName']").on('blur',function () {
        var obj=$(this);
       if(obj.val()=='') return false;
       $.ajax({
           url:check_url,
           type:'post',
           data:{type:'memberName',val:obj.val()},
           dataType:'json',
           success:function (data) {
               obj.removeClass('error').next().remove();
               if(data.success){
                   //账号不存在
                   obj.addClass('error').after('<div class="error">会员账号不存在</div>');
               }
           }

       })
    });

    //enter出发提交
    $("form").keyup(function(event){
        if(event.keyCode ==13){
            $("#step1").trigger("click");
        }
    });

    //第一步通过账号名获取邮箱
    $("#step1").on('click',function () {
       var button=$(this),
           memberNameObj=$("input[name='memberName']"),
           verifyObj=$("input[name='verify']");
       if(!memberNameObj.val().length>0){
           layerMsg("请输入会员账号",memberNameObj);return false;
       }else if(memberNameObj.hasClass('error')){
           layerMsg("会员账号不存在",memberNameObj);return false;
       }else if(!verifyObj.val().length>0){
           layerMsg("请输入验证码",verifyObj);return false;
       }
       loading();
        button.addClass('disabled');
        $.ajax({
            url:getEmail_url,
            type:'post',
            data:{'memberName':memberNameObj.val(),'verify':verifyObj.val()},
            dataType:'json',
            success:function (data) {
                layer.closeAll();
                if(data.state){
                    $("cite").text(data.email);
                    step(2)
                }else {
                    button.removeClass('disabled');
                    freshCode();
                    layer.msg(data.message);
                }
            }

        })

    });

    //第二部根据邮箱发送修改密码链接
    $("#step2").on('click',function () {
        var button=$(this);
        loading();
        button.addClass('disabled');
        $.ajax({
            url:sendEmail_url,
            type:'post',
            data:{'memberName':$("input[name='memberName']").val(),'type':button.attr('pass')},
            dataType:'json',
            success:function (data) {
                layer.closeAll();
                if(data.state){
                    step(3)
                }else {
                    button.removeClass('disabled');
                    layer.msg(data.message);
                }
            }

        })
    })
});


/**
 * 跳转到对应步骤页面
 * @param step
 */
function step(step) {
    $(".steps_content dl").eq(step-1).addClass('active').siblings().removeClass('active');
    $(".step"+step).show().siblings().hide();
}

/**
 * 刷新验证码
 */
function freshCode() {
    var obj=$(".code_login");
    var src=obj.attr('src').split("?");
    obj.attr('src',src[0]+'?tm='+Math.random());
}