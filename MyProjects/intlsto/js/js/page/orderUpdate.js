/**
 * 包裹编辑
 * Created by Srako on 2017/05/16.
 */
var sto_no = '';
$(function () {
    getUpdateLoading();
    $("#return").click(function(){
        window.history.go(-1);
    });

    /* 获取列表数据*/
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
        var data = $('#form-article-update').serialize();
        obj.addClass('disabled');
        $.ajax({
            url:orderUpdate,
            data:data,
            type:'post',
            dataType:'json',
            beforeSend:function(){
                loading();
            },
            success:function (data) {
                sysException(data);
                if(data.state == 1){
                    layer.msg(data.message,{end:function () {
                        window.location.href = orderList
                    }});
                }else{
                    obj.removeClass("disabled");
                    layer.msg(data.message);
                }
            }
        })
    });

    /*海外收货仓库选择*/

    var servicesAll = []; //服务
    var destinationAndLine = []; //目的地和线路
    var isBool = true;
    $("body").on("change","select[name='consolidator_no']",function(){
        var consolidatorNo =  $(this).val();
        if(isBool){
            sto_no =  sto_no;
            destinationAndLine['line_name'] = $("#line_name0").val().split("_")[0];
            destinationAndLine['destination'] = $("#destination0").val();
            $("input[name='services_code[]']:checked").each(function(k,v){
                var chk_value = $(this).val().split("_");
                var servicesInfo =[];
                switch (chk_value[1]){
                    case '100':
                        servicesInfo.servicesCode = chk_value[0];
                        break;
                    case '210':
                        var insuranceMoney = $(this).parents(".check-box").next().children("input[name='InsuranceMoney']").val();
                        var rate = $(this).parent().siblings("#rate").val();
                        var money = parseFloat(insuranceMoney)*parseFloat(rate)*1/100;
                        servicesInfo.servicesCode = chk_value[0];
                        servicesInfo.servicesTotelMoney = money;
                        servicesInfo.servicesInputMoney = insuranceMoney;
                        break;
                    case '111':
                        var  servicesNumber = $(this).parents(".check-box").next().find("select[name='services_number']").val();
                        var  serviceNote = $(this).parents(".check-box").next().children("input[name='service_note']").val();
                        var  serviceCost = $(this).parents(".check-box").next().children("#costMoney").val();
                        servicesInfo.servicesCode = chk_value[0];
                        servicesInfo.servicesNumber = servicesNumber;
                        servicesInfo.servicesTotelMoney = parseFloat(serviceCost)*parseFloat(servicesNumber)*1;
                        servicesInfo.servicesNote = serviceNote;
                        break;
                }
                servicesAll.push(servicesInfo);
            });
            isBool = false;
        }
        if(consolidatorNo === '0'){
            $(".services").remove(); //移除服务
            return false;
        }
        var conNo = consolidatorNo.split('_')[0];
        $.ajax({
            type:'post',
            url:consolidatorNoByAll,
            data:{org_on:conNo},
            dataType:'json',
            success:function (data) {
                $(".services").remove(); //移除服务
                if(sto_no == conNo){
                    var html = servicesHtml(data['service'],servicesAll);
                    $("#services0").before(html);
                    $("select[name='destination']").append(destinationHtml(data['destination'],destinationAndLine['destination']));
                    var destination = destinationAndLine['destination'];
                    $.ajax({
                        type:'post',
                        url:selectDestinationByLineName,
                        data:{destination:destination,consolidatorNo:conNo},
                        dataType:'json',
                        success:function (data) {
                            $("select[name='line_name']").html(lineHtml(data,destinationAndLine['line_name']));
                        }
                    });
                }else{
                    var html = servicesHtml(data['service'],'');
                    $("#services0").before(html);
                    $("select[name='destination']").append(destinationHtml(data['destination']));
                }
                $('.skin-mini input').iCheck({
                    checkboxClass: 'icheckbox-blue',
                    radioClass: 'iradio-blue',
                    increaseArea: '20%'
                });
            }
        })
    });
});

function getUpdateLoading(){
    var  parameter= $("#parameter").val();
    $.ajax({
        type:'post',
        url:updateLoading,
        data:{parameter:parameter},
        dataType:'json',
        beforeSend:function(){
            loading();
        },
        success:function(data){
            sysException(data);
            if(data.state == 0){
                layer.msg(data.message);
                return false;
            }
            $("input[name='sto_no']").val(data.orderInfo.orderInfo.stoNo)
            $("input[name='order_id']").val(data.orderInfo.orderInfo.orderId)
            $('input[name="delivery_type"][value="'+data.orderInfo.orderInfo.deliveryType+'"]').iCheck('check');
            var warehouseHTML  = '';
            $.each(data.warehouse,function(k,v){
                warehouseHTML += '<option value="'+v.org_no+'_'+v.org_name+'"';
                if(data.orderInfo.orderInfo.consolidatorNo == v.org_no){
                    warehouseHTML += 'selected';
                }
                warehouseHTML += '>'+v.org_name+'</option>';
            });
            sto_no = data.orderInfo.orderInfo.consolidatorNo; //获取默认海外收货仓库单号
            $(".consolidator_no").append(warehouseHTML);

            var expressHTML = '';
            $.each(data.express,function(k,v){
                expressHTML += '<option value="'+v.expressCode+'"';
                if(data.orderInfo.orderInfo.expressCode == v.expressCode && data.orderInfo.orderInfo.deliveryType == 1){
                    expressHTML += 'selected';
                }
                expressHTML += '>'+v.expressName+'</option>';
            });
            $(".express_code").append(expressHTML);

            if(data.orderInfo.orderInfo.deliveryType == 1){ //快递至仓库
                $(".delivery").next().show();
                $("input[name='tracking_no']").val(data.orderInfo.orderInfo.trackingNo);
                $("input[name='mall_order_no']").val(data.orderInfo.orderInfo.mallOrderNo);
            }

            var destinationHTML = '';
            $.each(data.destinationArr,function(k,v){
                destinationHTML += '<option class="des" value="'+v.destination+'" ';
                if(data.orderInfo.orderInfo.destination == v.destination){
                    destinationHTML += 'selected';
                }
                destinationHTML += '>'+v.destination+'</option>';
            });
            $("#destination0").append(destinationHTML);

            var lineNameHTML = '';
            $.each(data.line_arr,function(k,v){
                lineNameHTML += '<option class="reLine" value="'+v.line_id+'_'+v.line_name+'_'+v.line_price_id+'"';
                if(data.orderInfo.orderInfo.lineId == v.line_id){
                    lineNameHTML += 'selected';
                }
                lineNameHTML += '>'+v.line_name+'</option>';
            });
            $("#line_name0").append(lineNameHTML);

            $('input[name="tax_payer"][value="'+data.orderInfo.orderInfo.taxPayer+'"]').iCheck('check');

            var html = '';
            var number = 0;
            var countMoney = 0;
            $.each(data.orderInfo.listOrderGoodsModel,function(k,v){
                html += '<tr class="pa">';
                html += '   <td class="text-c">'+(k+1)+'</td>';
                html += '   <td class="text-c"><input type="text" name="goods_name[]" maxlength="80" value="'+v.goodsName+'" autocomplete="off"  class="input-text text-c"></td>';
                html += '   <td class="text-c">';
                html += '       <span class="dropDown" style="display: block;" >';
                html += '           <input type="text" name="cate_name[]" readonly  autocomplete="off" value="'+v.cateName+'" onclick="cateNameList($(this))" class="input-text text-c" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
                html += '           <input type="hidden" value="'+v.cateId+'" name="cate_id[]">';
                html += '       </span>';
                html += '   </td>';
                html += '   <td class="text-c">';
                html += '       <span class="dropDown" style="display: block;">';
                html += '           <input type="text" name="brand_name[]"  maxlength="30" autocomplete="off" value="'+v.brandName+'" onclick="brandNameSearch($(this))" class="input-text text-c" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
                html += '           <input type="hidden" value="'+v.brandId+'" name="brand_id[]">';
                html += '       </span>';
                html += '   </td>';
                html += '   <td class="text-c">';
                html += '       <span class="dropDown" style="display: block;">';
                html += '           <input type="text" name="goods_unit[]"  maxlength="10" value="'+v.goodsUnit+'" autocomplete="off" onclick="unitSearch($(this))" class="input-text text-c" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
                html += '       </span>';
                html += '   </td>';
                html += '   <td class="text-c">';
                html += '       <input type="text" name="goods_number[]" maxlength="4" value="'+v.goodsNumber+'" onchange="goodsNumber($(this))" autocomplete="off" class="input-text text-c">';
                html += '   </td>';
                html += '   <td class="text-c">';
                html += '       <input type="text" name="goods_price[]"  maxlength="8"  value="'+toDecimal(v.goodsPrice)+'"  onchange="goodsPrice($(this))" autocomplete="off" class="input-text text-r">';
                html += '   </td>';
                html += '   <td class="text-c">';
                html += '       <input type="text" name="goods_total[]"  value="'+toDecimal(v.goodsTotal)+'" onchange="goodsTotal($(this))" autocomplete="off" class="input-text text-r">';
                html += '   </td>';
                html += '   <td class="text-c">';
                html += '       <input type="button" class="btn btn-danger size-MINI radius" onclick="delete_goods($(this))" value="删除"/>';
                html += '   </td>';
                html += '</tr>';
                number = parseInt(v.goodsNumber)+number*1;
                countMoney = toDecimal(parseFloat(v.goodsTotal)+countMoney*1);
            });
            $(".totalMoney").text(currencySymbol+countMoney);
            $(".orderGoods").before(html).find(".number").text(number);

            $("#services0").before(servicesHtml(data.serviceListArr,data.trueService));
            $('.skin-mini input').iCheck({
                checkboxClass: 'icheckbox-blue',
                radioClass: 'iradio-blue',
                increaseArea: '20%'
            });

            $("#deliveryName0").text(data.orderInfo.orderInfo.receiverName);
            $("#deliveryMobile0").text(data.orderInfo.orderInfo.receiverMobile);
            $("#deliveryCountry0").text(data.orderInfo.orderInfo.receiverCountry);
            $("#deliveryProvince0").text(data.orderInfo.orderInfo.receiverProvince);
            $("#deliveryCity0").text(data.orderInfo.orderInfo.receiverCity);
            $("#deliveryArea0").text(data.orderInfo.orderInfo.receiverArea);
            // $("#deliveryTown0").text(data.orderInfo.orderInfo.receiverTown);
            $("#deliveryAddress0").text(data.orderInfo.orderInfo.receiverAddress);
            if(data.orderInfo.orderInfo.receiverZipcode != '' && data.orderInfo.orderInfo.receiverZipcode !=null){
                $("#deliveryPostCode0").text(data.orderInfo.orderInfo.receiverZipcode);
            }else{
                $("#deliveryPostCode0").text('');
            }
            if(data.orderInfo.orderInfo.certificateNo != '' && data.orderInfo.orderInfo.certificateNo !=null) {
                $("#certificateNum0").html(data.orderInfo.orderInfo.certificateNo+'&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red;">'+data.verification+'<span>');
            }else{
                $("#certificateNum0").html('');
            }
            $(".delivery0").show();

            $("#senderName0").text(data.orderInfo.orderInfo.shipperName);
            $("#senderMobile0").text(data.orderInfo.orderInfo.shipperMobile);
            $("#senderCountry0").text(data.orderInfo.orderInfo.shipperCountry);
            $("#senderProvince0").text(data.orderInfo.orderInfo.shipperProvince);
            $("#senderCity0").text(data.orderInfo.orderInfo.shipperCity);
            $("#senderArea0").text(data.orderInfo.orderInfo.shipperArea);
            $("#senderTown0").text(data.orderInfo.orderInfo.shipperTown);
            $("#senderAddress0").text(data.orderInfo.orderInfo.shipperAddress);
            if(data.orderInfo.orderInfo.shipperZipcode != '' || data.orderInfo.orderInfo.shipperZipcode != null){
                $("#senderPostCode0").text(data.orderInfo.orderInfo.shipperZipcode);
            }else{
                $("#senderPostCode0").text('');
            }
            $(".sender0").show();
        }
    });
}

