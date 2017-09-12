/**
 * 分箱发货
 * Created by Srako on 2017/06/22.
 */
$(function () {

    //提交数据
    $("#pointsSendGood").click(function () {

        if(!checkForm()){return false;}


        //声明传输数据
        var base=new Object(),
            obj=$(this);
         base.trackingNoList=new Array();
         //海外收货仓库
         base.consolidator=$("input[name='consolidator']").val();
        //获取快递单号
        $("input[name='trackingAll[]']").each(function () {base.trackingNoList.push($(this).val());});
        base.shipper=$("#senderAddressId0").val();

        base.orderEntity=new Array();
        $("form").each(function () {
            base.orderEntity.push($(this).serialize())
        });

        obj.addClass('disabled');
        $.ajax({
            type:'post',
            data:base,
            beforeSend:function(){
                loading();
            },
            success:function (data) {
                sysException(data);
                if(data.state){
                    layer.confirm(data.message, {
                        btn: ['继续分箱','跳转包裹列表'] //按钮
                    }, function(){
                        window.location.href=storageList_url;
                    }, function(){
                        window.location.href=orderList_url;
                    });
                }else {
                    obj.removeClass('disabled');
                    layer.msg(data.message);
                }
            }
        })

    });

    /*单位搜索框初次加载*/
    var body=$("body");

    //界面改变事件
    body.bind('input propertychange',function (e) {
        var obj=$(e.target),
            name=obj.attr('name');//获取输入框的名称
        switch (name){
            case 'estimate':   //金额预估
                var weight = parseFloat(obj.val());
                if(isNaN(weight) || weight <= 0){
                    obj.parents(".text-l").find("strong").text(currencySymbol+toDecimal('0'));
                    return false;
                }

                var consolidator = $("input[name='consolidator']").val().split("_"),
                    line = obj.parents('form').find("select[name='line_name']").val().split("_"),
                    destination = obj.parents('form').find("select[name='destination']").val();
                var consolidator_no=consolidator[0],
                    lineId=line[2];
                if(destination==='0'){
                    obj.val('');
                    layer.msg("请先选择目的地");return false;
                }else if(lineId==='0'){
                    obj.val('');
                    layer.msg("请先选择产品线路");return false;
                }
                $.ajax({
                    url:weightEstimate,
                    data:{weight:weight,consolidator_no:consolidator_no,destination:destination,line_id:lineId},
                    type:'post',
                    dataType:'json',
                    success:function (data) {
                        sysException(data);
                        if(data.state===1){
                            var money_total = 0;
                            var cost = 0;
                            obj.parents('form').find("input[name='services_code[]']:checked").each(function(){
                                var code = $(this).val().split("_")[1];
                                if(code == 100){
                                    cost = parseFloat($(this).parent().siblings("#cost").val());
                                    money_total = money_total +cost*1;
                                }else if(code == 111){
                                    cost= parseFloat($(this).parents(".check-box").next().find("#costMoney").val());
                                    var number = parseInt($(this).parents(".check-box").next().find("select").val());
                                    money_total = money_total+cost*number*1;
                                }else if(code == 210){
                                    cost = toDecimal($(this).parents(".check-box").next().children("input[name='InsuranceMoney']").val());
                                    if(cost){
                                        var rate = $(this).parent().siblings("#rate").val();
                                        money_total = money_total*1+cost*rate/100*1;
                                    }
                                }
                            });
                            var mo=parseFloat(data.message)+money_total;
                            obj.parents(".f-l").next().find("strong").text(currencySymbol+toDecimal(mo));
                        }else {
                            layer.msg(data.message);
                        }
                    }
                });
                break;
            default:
                break;
        }
    });

});

//定义包裹id，从1开始
var i=2;


function checkForm() {
    //海外收货仓库不能为空
    var consolidator=$("input[name='consolidator']").val();
    if(consolidator===''||!consolidator){
        layer.msg("海外收货仓库不能为空!");
        return false;
    }

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
    //判断所有目的地
    $("select[name='destination']").each(function (k,v) {
        if($(v).find(':selected').val()==='0'){
            bool = true;
            layerMsg("请选择目的地",$(v));return false;
        }
    });
    if(bool){return false;}

    //判断所有产品线路
    $("select[name='line_name']").each(function (k,v) {
        if($(v).find(':selected').val() === '0'){
            bool = true;
            layerMsg("请选择产品线路",$(v));
            return false;
        }
    });
    if(bool){return false;}

    //判断所有保险条款
    $("input[name='isTrue'] ").each(function (k,v) {
        if(!$(v).is(":checked")&&$(v).is(":visible")){
            layer.msg("请仔细阅读保险条款");
            return false;
        }
    });
    return !bool;

}

/**
 * 分箱添加包裹
 * @param obj
 */
function addPackage(obj) {
    isBool=true;
    $.ajax({
        url:split+'?id='+i,
        type:'get',
        success:function (data) {
            sysException(data);
            i++;
            obj.parents('form').after(data);
            $('.skin-mini input').iCheck({
                checkboxClass: 'icheckbox-blue',
                radioClass: 'iradio-blue',
                increaseArea: '20%'
            });
            obj.remove();
        }
    });

}


