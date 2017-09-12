/**
 * 密码管理
 */

$(function(){
    //第一次设置提现密码，隐藏原提现密码
    if($("input[name='isFirst']").val()==='1'){
        $('.noFirst').hide();
    }

    if(active){
        $(".panel-header span").eq(1).addClass('active').siblings().removeClass('active');
        $('.panel .panel-body').eq(1).addClass('panel_body_block').siblings().removeClass('panel_body_block');
    }

	var can_pass=true; //定义新密码是否通过

	/*tab切换*/
	$('.panel-header span').on('click',function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('.panel .panel-body').eq($(this).index()).addClass('panel_body_block').siblings().removeClass('panel_body_block');
	});

	//鼠标按下与放开改变输入框属性
    $(".seach_btn_onpage>i").on('mousedown',function () {
        $(this).parent().parent().find(".input-text").prop('type','text');
        $(this).removeClass('Hui-iconfont-niming').addClass('Hui-iconfont-yanjing c-orange');
    }).on('mouseup mouseleave',function () {
        $(this).parent().parent().find(".input-text").prop('type','password');
        $(this).removeClass('Hui-iconfont-yanjing c-orange').addClass('Hui-iconfont-niming');
    });

    //实时密码强度判断
    var ele_pass = document.getElementById('password'),
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

    //增加验证规则，可见必选
    $.validator.addMethod('oldPass',function(value,element){
        return value.length>0||$("input[name='isFirst']").val()==='1';
    },"请输入原支付密码");

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

    //登录密码修改
    $("#loginPass").validate({
        rules:{
            oldPassword:{
                required:true
            },
            password:{
                required:true,
                rangelength:[8,20],
                password:true,

            },
            checkPassword: {
                required:true,
                equalTo:'#password'
            }
        },
        messages: {
            oldPassword:{
                required:'原密码不能为空'
            },
            password:{
                required:'新密码不能为空',
                rangelength:'请输入8-20位的密码'
            },
            checkPassword:{
                required:'确认密码不能为空',
                equalTo:'两次输入密码不一致'
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
            //密码强度不够
            if(!can_pass){
                layer.msg('请输入字母/数字/特殊字符串两种组合以上的密码');
                return false;
            }
            //防止重复提交
            var forms    =   $("#loginPass"),
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
                        layer.msg(data.message,{end:function () {
                            document.getElementById("loginPass").reset();
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


    //支付密码修改
    $("#payPass").validate({
        rules:{
            oldPassword:{
              oldPass:true,
            },
            password:{
                required:true,
                digits:true,
                rangelength:[6,6]

            },
            checkPassword: {
                required:true,
                equalTo:'#payPassword'
            }
        },
        messages: {
            password:{
                required:'新密码不能为空',
                digits:'请输入6位纯数字',
                rangelength:'请输入6位的密码'
            },
            checkPassword:{
                required:'确认密码不能为空',
                equalTo:'两次输入密码不一致'
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
            var forms    =   $("#payPass"),
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
                        layer.msg(data.message,{end:function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            if($("input[name='isFirst']").val()==='1'){
                                //第一次展示原密码
                                $("input[name='isFirst']").val(0);
                                $(".noFirst").show();
                            }
                            document.getElementById("payPass").reset();

                        }});
                    }else {
                        layer.msg(data.message);
                    }
                }
            });

        }
    });
});