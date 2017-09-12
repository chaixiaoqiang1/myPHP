/**
 * 账户充值
 * Created by POCO on 2017/06/02.
 */

$(function () {
    // form-article-add
    $('button').on('click', function (e) {

        if($('#payStyle img').length<1){
            layer.msg('暂未开通线上充值，敬请关注');
            return false;
        }
        //充值金额不能为空
        var pdr_amount = $('input[name="amount"]');
        if (!pdr_amount.val()||pdr_amount.val()<=0) {
            layer.msg('充值金额不正确');
            return false;
        }

        var paymemntCode=$("#payment_id").val();
        if(paymemntCode===''||!paymemntCode){
            layer.msg('请选择支付方式');
            return false;
        }
        $(e).addClass('disabled');
        loading();
        $.ajax({
            type:'post',
            data:$('#recharge').serialize(),
            success:function (data) {
                sysException(data);
                $(e).removeClass('disabled');
                if(typeof (data)=='object'&&!data.state){layer.msg(data.message);return false;}
                if($.inArray(paymemntCode,['wxpay'])!=-1){
                    layer.open({
                        type: 1,
                        title: '请使用微信扫描二维码完成支付',
                        skin: 'layui-layer-rim', //加上边框
                        area: ['300px', '360px'], //宽高
                        content: '<img style="width: 90%" src="'+data.code_img_url+'"><br/><a class="btn btn-default" href="'+data.over_url+'" style="width: 150px;" type="button">已完成支付</a>'
                    });
                }else {
                    layer.open({
                        type: 1,
                        title:"充值",
                        content: data,
                        end:function () {
                            window.location.href=location.href;
                        }
                    });
                }
            },
        });
    });


    //判断字符串是否在数组之中
    function contains(arr, obj) {
        var i = arr.length;
        while (i--) {
            if (arr[i] === obj) {
                return true;
            }
        }
        return false;
    }


    /**
     * 打开充值明细
     */
    $("#detail").click(function () {
        var index=layer.open({
            type: 2,
            title:"充值明细",
            maxmin:true,
            area: ['70%', '70%'], //宽高
            content: recharge_url
        });
        layer.full(index);
        $('.tab').css('table-layout','fixed');
    });



    /**
     * 选中充值方式
     */
    $('#payStyle img').on('click',function(){
        $(this).addClass('payChose').siblings().removeClass('payChose');
        $("#payment_id").val($(this).attr('payment_code'));
        $("#payment_name").val($(this).attr('payment_name'));

    })

});