/**
 *合箱发货
 **/
$(function(){
    /*表单提交*/
    $("#submit").click(function(){
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

        var destination = $("select[name='destination']");
        if(destination.val() === '0'){
            layerMsg("请选择目的地",destination);
            return false;
        }
        var line_name = $("select[name='line_name']");
        if(line_name.val() === '0'){
            layerMsg("请选择产品线路",line_name);
            return false;
        }

        var isTrue = $("input[name='isTrue'] ");
        if(!isTrue.is(":checked")&&isTrue.is(":visible")){
            layer.msg("请仔细阅读保险条款");
            return false;
        }
        var data = $('#form-box-add').serialize();
        $.ajax({
            url:goodsOrderBox ,
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

/**
 * 移除一条物品信息
 * @param obj   当前对象
 */
function delete_goods(obj){
    if($(".pa").length != 1 ){
        obj.parents(".pa").remove();
        var i = 1;
        $(".pa").each(function(){
            $(this).children("td").eq(0).html(i);
            i++;
        })
    }else{
        layer.msg("请保留一行信息");
    }

}





