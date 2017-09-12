/**
 * 重置密码
 * Created by Srako on 2017/07/20.
 */
$(function () {

    var can_pass=true;  //定义新密码是否通过
    //实时密码强度判断
    var ele_pass = document.getElementById("password"),
        showRemind=$(".sr-only");
    ele_pass.onkeyup = function () {
        var val = this.value;
        var len = val.length;
        var sec = 0;
        if (len >= 8) { // 至少八个字符
            sec=checkPasswordStrength(val);
            showRemind.show();
        }else {
            showRemind.hide();
        }
        can_pass=(sec>1);
        switch (sec) {
            case 1:
                showRemind.css({"background":"red",'width': "33%"});
                break;
            case 2:
                showRemind.css({"background":"orange",'width': "66%"});
                break;
            case 3:
            case 4:
                showRemind.css({"background":"",'width': "100%"});
                break;
            default:
                break
        }
    };

    //密码为合法字符串
    $.validator.addMethod('password',function(value,element){
        var c = value.length;
        for (i = 0; i < c; i++) {
            if (0 == charMode(value.charAt(i))) {
                return false
            }
        }
        return true;
    },"密码必需由英文字母、数字或特殊符号组成");

    $('#resetPass').validate({
        rules:{
            password:{
                required:true,
                rangelength:[8,20],
                password:true,
            },
            confirmPwd: {
                required:true,
                equalTo:'#password'
            },
        },
        messages: {
            password:{
                required:'新密码不能为空',
                rangelength:'请输入8-20位的新密码'
            },
            confirmPwd:{
                required:'确认密码不能为空',
                equalTo:'两次输入密码不一致'
            },
        },
        errorElement:"div",   //错误展示的标签
        onkeyup:false,
        errorPlacement:function(error,element) {
            //错误展示位置
            error.appendTo(element.parents(".formControls"));

        },
        success:function (label) {$(label).remove();},
        submitHandler:function(){
            //密码强度不够
            if(!can_pass){
                layer.msg('请输入字母/数字/特殊字符串两种组合以上的密码');
                return false;
            }
            //防止重复提交
            var forms    =   $("#resetPass"),
                button  =   forms.find("button"),
                param   =   forms.serialize();
            button.addClass('disabled');
            $.ajax({
                dateType:'json',
                data:param,
                type: 'post', // 提交方式 get/post
                success:function(data) {
                    if(data.state){
                        layer.msg(data.message,{end:function () {
                            window.location.href=login_url;
                        }});
                    }else {
                        button.removeClass('disabled');
                        layer.msg(data.message);
                    }
                }
            });

        }
    });


    //支付密码验证提交
    $('#resetPay').validate({
        rules:{
            password:{
                required:true,
                digits:true,
                rangelength:[6,6],
            },
            confirmPwd: {
                required:true,
                equalTo:'#password'
            },
        },
        messages: {
            password:{
                required:'新密码不能为空',
                digits:'请输入6位纯数字',
                rangelength:'请输入6位的密码'
            },
            confirmPwd:{
                required:'确认密码不能为空',
                equalTo:'两次输入密码不一致'
            },
        },
        errorElement:"div",   //错误展示的标签
        onkeyup:false,
        errorPlacement:function(error,element) {
            //错误展示位置
            error.appendTo(element.parents(".formControls"));

        },
        success:function (label) {$(label).remove();},
        submitHandler:function(){
            //防止重复提交
            var forms    =   $("#resetPay"),
                button  =   forms.find("button"),
                param   =   forms.serialize();
            button.addClass('disabled');
            $.ajax({
                dateType:'json',
                data:param,
                type: 'post', // 提交方式 get/post
                success:function(data) {
                    if(data.state){
                        layer.msg(data.message,{end:function () {
                            window.location.href=login_url;
                        }});
                    }else {
                        button.removeClass('disabled');
                        layer.msg(data.message);
                    }
                }
            });

        }
    });

});