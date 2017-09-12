/**
 *发货
 **/
$(function(){
    /*表单提交*/
    $("#submit").click(function(){
        var bool = false;
        $(".pa").each(function(){
            var cate_name = $(this).children('td').eq(1).find("input").eq(0);
            var brand_name = $(this).children('td').eq(2).find("input").eq(0);
            var goods_name = $(this).children('td').eq(3).find("input").eq(0);
            var goods_number = $(this).children('td').eq(5).find("input").eq(0);
            var goods_price = $(this).children('td').eq(6).find("input").eq(0);
            var goodsUnitObj = $(this).children('td').eq(4).children();
            if(goodsUnitObj.find(".searchable-select-holder").text() == '请选择' ) {
                goodsUnitObj.css({
                    "color":"#c00",
                    "border-color":"#c66161",
                    "background-color":"#fbe2e2"}); bool = true; }
            if(cate_name.val() == '' || cate_name.val() == null) {   cate_name.focus().addClass('error bindBlack'); bool = true; }
            if(brand_name.val() == '' || brand_name.val() == null) {  brand_name.focus().addClass('error bindBlack'); bool = true;}
            if(goods_name.val() == '' || goods_name.val() == null) {  goods_name.focus().addClass('error bindBlack'); bool = true; }
            if(goods_number.val() == 0 || goods_number.val() == null) {  goods_number.focus().addClass('error bindBlack');bool = true;}
            if(goods_price.val() == 0 || goods_price.val() == null) {  goods_price.focus().addClass('error bindBlack'); bool = true; }
        })
        if(bool){
            layer.msg("请规范物品栏数据");
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
        var delivery = $("input[name='delivery']").val();
        if(!delivery){
            layer.msg("请选择收货地址");
            return false;
        }
        var sender = $("input[name='sender']").val();
        if(!sender){
            layer.msg("请选择发货地址");
            return false;
        }

        var isTrue = $("input[name='isTrue'] ");
        if(!isTrue.is(":checked")&&isTrue.is(":visible")){
            layer.msg("请仔细阅读保险条款");
            return false;
        }
        var data = $('#form-order-add').serialize();
        $.ajax({
            url:goodsOrderDeliver ,
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
                        btn: ['返回列表','跳转包裹列表'] //按钮
                    }, function(){
                        window.history.back(-1);
                    }, function(){
                        window.location.href = orderList;
                    });
                }else{
                    layer.msg(data.message);
                }
            }
        })

    });

});




