/**
 * 用户注册
 * Created by Srako on 2017/05/11.
 */
$(function () {

    $('.layer_reg_tips input').on('focus',function(e){
        var html='<div class="layui-layer-tips" style="position: absolute;z-index:8000;margin-top: -40px;">';
            html+='<div id="" class="layui-layer-content" style="background-color: rgb(255, 135, 70);">';
            html+=$(this).attr('placeholder');
            html+='<i class="layui-layer-TipsG layui-layer-TipsT" style="border-right-color: rgb(255, 135, 70);"></i>';
            html+='</div></div>';
        $(this).before(html);
        e.stopPropagation();
    }).on('blur',function (e) {
        $(this).parent().children('.layui-layer-tips').remove();
        e.stopPropagation();
    });

    var can_pass=true;  //定义新密码是否通过
    //实时密码强度判断
    var ele_pass = document.getElementsByTagName("input")[2],
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


    $('#regForm').validate({
        rules:{
            countryCode:{
                required:true
            },
            memberName:{
                required:true,
                rangelength:[4,20],
                isRightfulString:true,
                remote:{
                    url:check_url,
                    data:{
                        type:'memberName',
                        val:function(){
                            return $('#memberName').val();
                        }
                    },
                    type:'post',
                    dataType:'json',
                    dataFilter: function (data) {//判断控制器返回的内容
                        data=JSON.parse(data);
                        return data.success;
                    }
                }
            },
            memberEmail:{
                required:true,
                isEmail:true,
                remote:{
                    url:check_url,
                    data:{
                        type:'memberEmail',
                        val:function(){
                            return $('#memberEmail').val();
                        }
                    },
                    type:'post',
                    dataType:'json',
                    dataFilter: function (data) {//判断控制器返回的内容
                        data=JSON.parse(data);
                        return data.success;
                    }
                }
            },
            memberPassword:{
                required:true,
                rangelength:[8,20],
                password:true,
            },
            confirmPwd: {
                required:true,
                equalTo:'#memberPassword'
            },
            verify:{
                required:true
            },
            email_code:{
                required:true
            },
            IsRead:{
                required:true
            }
        },
        messages: {
            countryCode:{
                required:'请先选择国家'
            },
            memberName:{
                required:'会员账号不能为空',
                rangelength:'请输入4-20位的会员账号',
                isRightfulString:'请输入4-20个字符，可使用字母、数字、下划线',
                remote:'会员账号已存在'
            },
            memberEmail:{
                required:'邮箱不能为空',
                email:"邮箱格式不正确",
                remote:'邮箱已存在'
            },
            memberPassword:{
                required:'密码不能为空',
                rangelength:'请输入8-20位的密码'
            },
            confirmPwd:{
                required:'确认密码不能为空',
                equalTo:'两次输入密码不一致'
            },
            verify:{
                required:'请输入验证码',
            },
            email_code:{
                required:'请输入邮箱验证码'
            },
            IsRead:{
                required:'请先阅读用户协议'
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
            //密码强度不够
            if(!can_pass){
                layer.msg('请输入字母/数字/特殊字符串两种组合以上的密码');
                return false;
            }
            //防止重复提交
            var forms    =   $("#regForm"),
                button  =   forms.find("button"),
                param   =   forms.serialize();
            button.addClass('disabled');
            var country=$("select[name='countryCode']").find(":selected").text();
            param+="&country="+country;
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
                        freshCode();
                        layer.msg(data.message);
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

var countdown=120;//两次验证码之间时差
/**
 * 获取邮箱验证码
 */
function getEmailCode() {
    var memberEmail=$("#memberEmail"),
        verify=$("input[name='verify']");
    if(memberEmail.val()==''){
        layerMsg("请先输入邮箱",memberEmail);return false;
    }else if(memberEmail.hasClass('error')){
        layerMsg("请输入正确的邮箱",memberEmail);return false;
    }else if(verify.val()==''){
        layerMsg("请先输入验证码",verify);return false;
    }
    $('.email_code').attr('disabled',true);
    loading();
    $.ajax({
        url:email_url,
        data:{memberEmail:memberEmail.val(),verify:verify.val()},
        dataType:'json',
        type:'post',
        success:function (data) {
            sysException(data);
            freshCode();
            if(data.state){
                settime($(".email_code"));
                layer.msg('验证码获取成功');
            }else{
                $('.email_code').attr('disabled',false);
                layer.msg(data.message);
            }
        }
    })
}

/**
 * 获取验证码按钮计时
 * @param obj
 */
function settime(obj) { //发送验证码倒计时
    if (countdown == 0) {
        obj.attr('disabled',false);
        obj.text("免费获取验证码");
        countdown = 120;
        return;
    } else {
        obj.attr('disabled',true);
        obj.text("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function() {
            settime(obj) }
        ,1000)
}