/**
 *极速发货
 **/
$(function(){
    getSpeedLoading();
    /*表单提交*/
    $("#submit").click(function(){
        var obj = $(this);
        var bool = false;
        $(".pa").each(function(){
            var goods_name = $(this).children('td').eq(1).find("input").eq(0);
            var cate_name = $(this).children('td').eq(2).find("input").eq(0);
            var brand_name = $(this).children('td').eq(3).find("input").eq(0);
            var goodsUnitObj = $(this).children('td').eq(4).find("input").eq(0);
            var goods_number = $(this).children('td').eq(5).find("input").eq(0);
            var goods_price = $(this).children('td').eq(6).find("input").eq(0);
            if(cate_name.val() == '' || cate_name.val() == null) {   cate_name.focus().addClass('error bindBlack'); bool = true; }
            if(brand_name.val() == '' || brand_name.val() == null) {  brand_name.focus().addClass('error bindBlack'); bool = true;}
            if(goods_name.val() == '' || goods_name.val() == null) {  goods_name.focus().addClass('error bindBlack'); bool = true; }
            if(goodsUnitObj.val() == '' || goodsUnitObj.val() == null) {  goodsUnitObj.focus().addClass('error bindBlack'); bool = true; }
            if(goods_number.val() == 0 || goods_number.val() == null) {  goods_number.focus().addClass('error bindBlack');bool = true;}
            if(goods_price.val() == 0 || goods_price.val() == null) {  goods_price.focus().addClass('error bindBlack'); bool = true; }
        });
        if(bool){
            layer.msg("请规范物品栏数据");
            return false;
        }
        var deliveryType = $("input[name='delivery_type']:checked").val();
        if(deliveryType === '1'){
            var tracking = $("select[name='express_code']").val();
            if (tracking == '' || tracking == null){
                layerMsg("海外快递公司不能为空",$("select[name='express_code']")); return false;
            }
            var tracking = $("input[name='tracking_no']").val();
            if (tracking == '' || tracking == null){
                layerMsg("海外快递单号不能为空",$("input[name='tracking_no']")); return false;
            }
        }

        var consolidator_no = $("select[name='consolidator_no']").val().split("_")[0];
        if(consolidator_no == 0){
            layerMsg("请选择海外收货仓库",$("select[name='consolidator_no']"));
            return false;
        }
        var destination = $("select[name='destination']").val();
        if(destination == 0){
            layerMsg("请选择目的地",$("select[name='destination']"));
            return false;
        }
        var line_name = $("select[name='line_name']").val();
        if(line_name == 0){
            layerMsg("请选择产品线路",$("select[name='line_name']"));
            return false;
        }

        var isTrue = $("input[name='isTrue'] ");
        if(!isTrue.is(":checked")&&isTrue.is(":visible")){
            layer.msg("请仔细阅读保险条款");
            return false;
        }
        var data = $('#form-article-add').serialize();
        obj.addClass('disabled');
        $.ajax({
            url:orderSpeedDeliveryAdd,
            data:data,
            type:'post',
            dataType:'json',
            beforeSend:function(){
                loading();
            },
            success:function (data) {
                sysException(data);
                if(data.state == 1){
                    layer.confirm(data.message, {
                        btn: ['继续添加','跳转包裹列表'] //按钮
                    }, function(){
                        window.location.href=location.href;
                    }, function(){
                        window.location.href = orderList;
                    });
                }else{
                    obj.removeClass("disabled");
                    layer.msg(data.message);
                }
            }
        })

    });

    /*海外收货仓库选择*/
    $("body").on("change","select[name='consolidator_no']",function(){
        var consolidatorNo =  $(this).val().split("_")[0];
        if(consolidatorNo == '0'){
            $(".services").remove(); //移除服务
            return false;
        }
        $.ajax({
            type:'post',
            url:ConsolidatorNoByAll,
            data:{org_on:consolidatorNo},
            dataType:'json',
            success:function (data) {
                sysException(data);
                $("select[name='destination']").append(destinationHtml(data['destination']));
                var html =servicesHtml(data['service'],'');
                $(".services").remove(); //移除服务
                $("#services0").before(html);
                $('.skin-mini input').iCheck({
                    checkboxClass: 'icheckbox-blue',
                    radioClass: 'iradio-blue',
                    increaseArea: '20%'
                });
            }
        })
    });

});

/*初始加载*/
function getSpeedLoading(){
    $.ajax({
        type:'post',
        url:speedLoad,
        dataType:'json',
        beforeSend:function(){
            loading();
        },
        success:function(data){
            sysException(data);
            var wareInfo = express = '';
            $.each(data.wareInfo,function(k,v){
                wareInfo += '<option value="'+v.org_no+'_'+v.org_name+'" >'+v.org_name+'</option>';
            });
            $(".consolidator_no").append(wareInfo);

            $.each(data.express,function(k,v){
                express += '<option value="'+v.expressCode+'" >'+v.expressName+'</option>';
            });
            $(".express_code").append(express);

            if(data.deliveryAddress){
                if(data.deliveryAddress.countryCode == 'CHN'){
                    $("#certificateNum0").html(+data.deliveryAddress.certificateNum+'<span style="color: red;">&nbsp;&nbsp;&nbsp;&nbsp;'+data.deliveryAddress.certificatesStatus+'</span>');
                }
                $("#deliveryName0").text(data.deliveryAddress.deliveryName);
                $("#deliveryMobile0").text(data.deliveryAddress.deliveryMobile);
                $("#deliveryCountry0").text(data.deliveryAddress.country);
                $("#deliveryProvince0").text(data.deliveryAddress.province);
                $("#deliveryCity0").text(data.deliveryAddress.city);
                $("#deliveryArea0").text(data.deliveryAddress.area);
                // $("#deliveryTown0").text(data.deliveryAddress.town);
                $("#deliveryAddress0").text(data.deliveryAddress.address);
                $("#deliveryPostCode0").text(data.deliveryAddress.postcode);
                $("#deliveryAddressId0").val(data.deliveryAddress.deliveryAddressId);
                $("#deliveryAddressNo0").val("0");
                $(".delivery0").show();
                $(".falseDelivery0").hide();
            }else{
                $("#deliveryAddressNo0").val("1");
                $(".delivery0").hide();
                $(".falseDelivery0").show();
            }

            if(data.senderAddress){
                $("#senderName0").text(data.senderAddress.senderName);
                $("#senderMobile0").text(data.senderAddress.senderMobile);
                $("#senderCountry0").text(data.senderAddress.country);
                $("#senderProvince0").text(data.senderAddress.province);
                $("#senderCity0").text(data.senderAddress.city);
                $("#senderArea0").text(data.senderAddress.area);
                // $("#senderTown0").text(data.senderAddress.town);
                $("#senderAddress0").text(data.senderAddress.address);
                $("#senderPostCode0").text(data.senderAddress.postcode);
                $("#senderAddressId0").val(data.senderAddress.senderAddressId);
                $(".sender0").show();
                $(".falseSender0").hide();
                $("#senderAddressNo0").val("0");
            }else{
                $(".sender0").hide();
                $(".falseSender0").show();
                $("#senderAddressNo0").val("1");
            }
        }
    });
}







