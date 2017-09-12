/**
 * 账号提现js
 * Created by POCO on 2017/05/11.
 * srakor@163.com
 */
$(function () {

    if(is_first==='1'){
        layer.alert(
            '<center>请先设置支付密码</center>',
            {
                btn: ['确定'],
                btnAlign:'c',
                end:function () {
                    window.location.href=payPass_url;
                }
            });
    }

    $("select[name='paymentCode']").on('change',function () {
        if($(this).val()!==''){
            $("input[name='account']").attr('placeholder','请输入'+$(this).find('option:selected').text()+'账号');
        }
    });

    // 提现金额不能大于账户余额
    jQuery.validator.addMethod("CheckMoney", function(value, element) {
        return this.optional(element) || (parseInt($("#money_num").val())<=parseInt($("#Ymoney").text()));
    }, "提现金额不能大于账户余额");


    // 100整数验证
    jQuery.validator.addMethod("IntegerCheckno", function(value, element) {
        return this.optional(element) || $("#money_num").val() % 100 === 0;
    }, "请输入100的整数倍");



    $("#cash").validate({
        rules:{
            money:{
                required:true,
                number:true,
                IntegerCheckno:true,
                CheckMoney:true,
                min:100,
            },
            account:{
                required:true,
            },
            paymentCode:{
                required:true,
            },
            payPassword_rsainput: {
                required:true,
                remote:{
                    url:checkPass_url,
                    data:{
                        payPassword_rsainput:function(){
                            return $('#payPassword_rsainput').val();
                        }
                    },
                    type:'post',
                    dataType:'json'
                }
            },
        },
        messages: {
            money:{
                required:'可提现金额不能为空',
                number:'必须输入合法的数字（负数，小数）',
                min:'提现金额不能小于100',
            },
            paymentCode:{
                required:'账号类型不能为空'
            },
            account:{
                required:'提现账号不能为空'
            },
            payPassword_rsainput:{
                required:'支付密码不能为空',
                remote:'支付密码错误',
            },
        },
        success:"valid",
        errorElement:'div',
        onkeyup:false,
        focusCleanup:true,
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(){
            //防止重复提交
            var forms    =   $("#cash"),
                button  =   forms.find("button[type='submit']"),
                param   =   forms.serialize(),
                paymentName=$("select[name='paymentCode']").find(':selected').text();//添加提现方式名称
            button.addClass('disabled');
            param+='&paymentName='+paymentName;
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
     * 打开提现明细
     */
    $("#detail").click(function () {
        var index=layer.open({
            type: 2,
            title:"提现明细",
            maxmin:true,
            area: ['70%', '70%'], //宽高
            content: cashList_url
        });
        layer.full(index);
    })

});