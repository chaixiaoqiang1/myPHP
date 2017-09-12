/**
 * 仓内服务
 * Created by Srako on 2017/06/19.
 */
$(function () {

    $("#return").click(function(){
        window.location.href = history.go(-1);
    });

    //提交仓内服务
    $("#saveService").click(function () {
        var serviceItem=new Array();  //服务项目列表

        //获取选择的服务数量及code
        $("input[name='servicesCode']").each(function (i) {
            if($(this).is(':checked')){
                var a={};
                 a.servicesCode=$(this).val();
                 a.serviceQuantity=$(this).parents('.check-box').find('span>input').val();
                serviceItem.push(a);
            }
        });
        if(serviceItem.length<=0){
            layer.msg("请选择服务项目");return false;
        }
        var servicesRemarks=$("textarea[name='servicesRemarks']").val();
        if(servicesRemarks==''){
            layer.msg("服务详情不能为空");return false;
        }
        $(this).addClass('disabled');
        $.ajax({
            url:location.href,
            dataType:'json',
            data:{serviceItem:serviceItem,servicesRemarks:servicesRemarks},
            type:'post',
            success:function (data) {
                sysException(data);
                if(data.state){
                    layer.msg(data.message,{end:function () {
                        window.location.href=storageList_url;
                    }})
                }else {
                    $(this).removeClass('disabled');
                    layer.msg(data.message);
                }
            }
        })

    });

    //勾选展示价格与数量
    $(".code").on('click',function () {
        var bool = $(this).prop("checked"),//是否勾选
            span =$(this).parent().siblings('span');
        if(bool){  //勾选
            span.removeClass('hide');
            if(span.children('input').val()==''){
                span.children('input').val(1);
            }
        }else {
            span.addClass('hide');
        }
    });


    //点击加号
    $(".numAdd").on('click',function () {
        var num=parseInt($(this).siblings('input').val())+1,
            serviceCost=$(this).parent().siblings('label').attr('serviceCost');
        $(this).siblings('input').val(num);
        $(this).siblings('strong').text(currencySymbol+toDecimal(serviceCost*num));
    });

    //点击减号
    $(".numRed").on('click',function () {
        var num=parseInt($(this).siblings('input').val())-1,
            serviceCost=$(this).parent().siblings('label').attr('serviceCost');
        if(num<=0) num=1;
        $(this).siblings('input').val(num);
        $(this).siblings('strong').text(currencySymbol+toDecimal(serviceCost*num));
    });


    //直接输入数量
    $("input[name='serviceQuantity']").on('input propertychange',function () {
        var num=parseInt($(this).val()),
            serviceCost=$(this).parent().siblings('label').attr('serviceCost');
        if(isNaN(num)||num<=0) num=1;
        $(this).val(num);
        $(this).siblings('strong').text(currencySymbol+toDecimal(serviceCost*num));
    })
});