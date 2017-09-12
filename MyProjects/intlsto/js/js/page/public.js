/**
 * 公共js
 */
var isBool = true;
var prevInputMoney = 0;
$(function () {
    /*单位搜索框初次加载*/

    /*数量默认为0*/
    $("body").on("click","input[name='goods_number[]']",function(){
        var obj = $(this);
        var price = obj.val();
        if(price == '0'){
            obj.val('');
        }
        obj.blur(function(){
            var money = obj.val();
            if(money != '' ){
                obj.val(money);
            }else{
                obj.val('0');
            }
        })
    })
    /*单价值默认为0.00*/
    .on("click","input[name='goods_price[]']",function(){
        var obj = $(this);
        var price = obj.val();
        if(price == '0.00'){
            obj.val('');
        }
        obj.blur(function(){
            var money = obj.val();
            if(money != '' ){
                obj.val(money);
            }else{
                obj.val('0.00');
            }
        })
    })
    /*总金额默认为0.00*/
    .on("click","input[name='goods_total[]']",function(){
        var obj = $(this);
        var price = obj.val();
        if(price == '0.00' || price == null){
            obj.val('');
        }
        obj.blur(function(){
            var money = obj.val();
            if(money != '' ){
                obj.val(money);
            }else{
                obj.val('0.00');
            }
        })
    })
    /*勾选服务*/
    .on("click","input[name='services_code[]']",function(){
        var obj = $(this);
        var bool = $(this).is(":checked");
        var money = 0;
        var serviceCode=$(this).val().split('_');
        var inputEstimateMoney = obj.parents(".services").siblings(".estimate").find("#estimate").val();
        var showMoneyObj = obj.parents(".services").siblings(".estimate").find("strong");
        var estimateMoney = 0,inputMoney = 0,rate = 0,costMoney = 0,number = 0,cost = 0,lastEstimate = 0;
        if(bool){
            switch (serviceCode[1]){
                case '100':
                    obj.parent().nextAll("strong").show();
                    if(inputEstimateMoney > 0){
                        cost = parseFloat(obj.parent().nextAll("#cost").val());
                        estimateMoney = parseFloat(showMoneyObj.html().split(currencySymbol)[1]);
                        lastEstimate = toDecimal(estimateMoney*1+cost*1);
                        showMoneyObj.html(currencySymbol+lastEstimate);
                    }
                    break;
                case '210':
                    obj.parents(".check-box").next().find("input").attr("disabled",false);
                    obj.parents(".check-box").next().find("strong").text(currencySymbol+'0.00');
                    obj.parents(".check-box").next().find("strong").show();
                    obj.parents(".skin-mini").next().show();
                    obj.parents(".skin-minimal").next("div").show();
                    break;
                case '111':
                    obj.parents(".check-box").next().find("input[name='service_note']").attr("disabled",false);
                    costMoney = obj.parents(".check-box").next().find("#costMoney").val();
                    number = obj.parents(".check-box").next().find("select").val();
                    money = toDecimal(costMoney*number*1);
                    obj.parents(".check-box").next().children("strong").text(currencySymbol+money);
                    obj.parents(".check-box").next().children("strong").show();
                    if(inputEstimateMoney > 0 ){
                        estimateMoney = parseFloat(showMoneyObj.html().split(currencySymbol)[1]);
                        lastEstimate = toDecimal(estimateMoney*1+money*1);
                        showMoneyObj.html(currencySymbol+lastEstimate);
                    }
                    break;
            }
        }else{
            switch (serviceCode[1]){
                case '100':
                    obj.parent().nextAll("strong").hide();
                    if(inputEstimateMoney > 0){
                        cost = parseFloat(obj.parent().nextAll("#cost").val());
                        estimateMoney = parseFloat(showMoneyObj.html().split(currencySymbol)[1]);
                        lastEstimate = toDecimal(estimateMoney*1-cost*1);
                        showMoneyObj.html(currencySymbol+lastEstimate);
                    }
                    break;
                case '111':
                    obj.parents(".check-box").next().find("input[name='service_note']").attr("disabled",'disabled');
                    obj.parents(".check-box").next().find("select").val("1");
                    obj.parents(".check-box").next().children("input[name='service_note']").val('');
                    obj.parents(".check-box").next().children("strong").hide();
                    if(inputEstimateMoney > 0){
                        costMoney = obj.parents(".check-box").next().find("#costMoney").val();
                        number = obj.parents(".check-box").next().find("select").val();
                        money = toDecimal(costMoney*number*1)
                        estimateMoney = parseFloat(showMoneyObj.html().split(currencySymbol)[1]);
                        lastEstimate = toDecimal(estimateMoney*1-money*1);
                        showMoneyObj.html(currencySymbol+lastEstimate);
                    }
                    break;
                case '210':
                    var inputMoney = obj.parents(".check-box").next().children("input[name='InsuranceMoney']").val();
                    obj.parents(".check-box").next().find("input").attr("disabled",'disabled');
                    obj.parents(".skin-mini").next("div").hide(); //两种格式
                    obj.parents(".skin-minimal").next("div").hide();
                    obj.parents(".check-box").next().children("input[name='InsuranceMoney']").val('');
                    obj.parents(".check-box").next().find("strong").hide();
                    if(inputEstimateMoney > 0){
                        rate = obj.parent().nextAll("#rate").val();
                        money = toDecimal(inputMoney*rate*1/100);
                        estimateMoney = parseFloat(showMoneyObj.html().split(currencySymbol)[1]);
                        lastEstimate = toDecimal(estimateMoney*1-money*1);
                        showMoneyObj.html(currencySymbol+lastEstimate);
                    }
                    break;
            }
        }
    })
    /*通过保险服务输入框进行计算*/
    .on("change","input[name='InsuranceMoney']",function(){
        var isBool = $(this).parent().prev().find("input[name='services_code[]']").prop("checked");
        if(isBool){
            var inputEstimateMoney = $(this).parents(".services").siblings(".estimate").find("#estimate").val();
            var showMoneyObj = $(this).parents(".services").siblings(".estimate").find("strong");
            var inputMoney = toDecimal($(this).val());
            if(isNaN(inputMoney) || inputMoney == '' || inputMoney <= 0){
                $(this).parent().prev().find("input[name='services_code[]']").iCheck('uncheck');
                $(this).attr("disabled",'disabled').val('').next().hide().parents(".skin-mini,.skin-minimal").next().hide();
                layer.msg("请输入有效的保险金额");
                return false;
            }
            var maxMoney = toDecimal($(this).parent().prev().children("#maxMoney").val());
            if(parseFloat(inputMoney) > parseFloat(maxMoney)){
                $(this).parent().prev().find("input[name='services_code[]']").iCheck('uncheck');
                $(this).attr("disabled",'disabled').val('').next().hide().parents(".skin-mini,.skin-minimal").next().hide();
                layer.msg("保险最大金额不能超过"+maxMoney);
                return false;
            }
            prevInputMoney = parseFloat($(this).next().html().split(currencySymbol)[1]);
            $(this).val(inputMoney)
            var rate = $(this).parent().prev().children("#rate").val();
            var money = toDecimal(inputMoney*rate*1/100);
            $(this).next().text(currencySymbol+money);
            if(inputEstimateMoney > 0 ){
                var estimateMoney = parseFloat(showMoneyObj.html().split(currencySymbol)[1]);
                var lastEstimate = toDecimal(estimateMoney*1-prevInputMoney*1+money*1);
                showMoneyObj.html(currencySymbol+lastEstimate);
            }
        }

    })
    /*通过单件加固的数量计算金额*/
    .on("change","select[name='services_number']",function(){
        var number = $(this).val();
        var bool = $(this).parents(".f-l .pr-10").prev().find("input[name='services_code[]']").prop("checked");
        if(bool){
            var inputEstimateMoney = $(this).parents(".services").siblings(".estimate").find("#estimate").val();
            var showMoneyObj = $(this).parents(".services").siblings(".estimate").find("strong");
            prevInputMoney = parseFloat($(this).parent().siblings("strong").html().split(currencySymbol)[1]);
            var costMoney = $(this).parent().nextAll("#costMoney").val();
            var money = toDecimal(costMoney*number*1);
            $(this).parent().nextAll("strong").text(currencySymbol+money);
            if(inputEstimateMoney > 0 ){
                var estimateMoney = parseFloat(showMoneyObj.html().split(currencySymbol)[1]);
                var lastEstimate = toDecimal(estimateMoney*1-prevInputMoney*1+money*1);
                showMoneyObj.html(currencySymbol+lastEstimate);
            }
        }
    })
    /*收货方式选择*/
    .on("change","input[name='delivery_type']",function(){
        var id = $(this).val();
        if(id == 1){ //快递至仓库
            $(this).parents(".delivery").next().show();
        }else if(id == 2){ //自送至仓库
            $(this).parents(".delivery").next().hide();
        }
    })
    /*根据目的地查询产品线路*/
    .on("change","select[name='destination']",function(){
        var obj = $(this);
        var destination = obj.val();
        var consolidatorNo = $(".consolidator_no").val().split("_");
        $.ajax({
            type:'post',
            url:selectDestinationByLineName,
            data:{destination:destination,consolidatorNo:consolidatorNo[0]},
            dataType:'json',
            success:function (data) {
                sysException(data);
                obj.parents('.f-l').next().find("select[name='line_name']").html(lineHtml(data));
                //如果目的地与收货地址的国家不相同则隐藏地址信息
                var country=obj.parents("table").find("input[name='delivery']").attr('country');
                if(country!=destination){
                    obj.parents("table").find('.delivery').hide();
                }else {
                    obj.parents("table").find('.delivery').show();
                }
            }
        })
    });
    /*金额预估*/
    $("#estimate").change(function(){
        var weight = toDecimal($(this).val()),
            obj = $(this).parent().parent().next(),
            bool = $("input[name='consolidator']").val(),
            consolidator_no = '';
        if(weight == ''){
            obj.find("strong").text(currencySymbol+'0.00');
            return false;
        }
        if(bool){
            consolidator_no = bool.split("_")[0];
        }else{
            consolidator_no = $("select[name='consolidator_no']").val().split("_")[0];
        }
        var destination = $("select[name='destination']").val();
        var line = $("select[name='line_name']").val();
        if(destination == 0 || destination == '' || destination == null){
            $(this).val('');
            layer.msg("目的地不能为空");
            return false;
        }
        if(line == null || line == '' || line == 0){
            $(this).val('');
            layer.msg("产品线路不能为空");
            return false;
        }
        if(isNaN(weight) || weight <= 0 ){
            $(this).val('');
            $(this).parents(".text-l").find("strong").text(currencySymbol+toDecimal('0'));
            layer.msg("包裹重量输入不合法");
            return false;
        }
        if(weight >= 10000){
            obj.find("strong").text(currencySymbol+'0.00');
            layer.msg("请输入小于10000的数值");
            return false;
        }
        $(this).val(weight);
        $.ajax({
            url:weightEstimate,
            data:{weight:weight,consolidator_no:consolidator_no,destination:destination,line_id:line.split("_")[2]},
            type:'post',
            dataType:'json',
            success:function (data) {
                sysException(data);
                if(data.state == 1){
                    var money_total = 0;
                    var cost = 0;
                    obj.parents(".clearFix").siblings(".services").find("input[name='services_code[]']:checked").each(function(){
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
                    money_total = toDecimal(money_total*1+data['message']*1);
                    obj.find("strong").text(currencySymbol+money_total);
                }else{
                    layer.msg(data.message);
                }
            }
        })
    });

});

/*新添加一条数据*/
var order = 1;
function addAGoods(obj){
    var number =obj.parents('tbody').find(".pa").length+1;
    var totalNumber = 0;
    obj.parents(".active").prevAll(".pa").each(function(){
        var current = parseInt($(this).find("input[name='goods_number[]']").val());
        totalNumber = totalNumber+current;
    });
    obj.prev().prev().find(".number").text(totalNumber);//计算物品数量
    obj.parents(".active").before(addAGoodsHtml(number));
}

/**
 * 移除一条物品信息
 * @param obj   当前对象
 */
function delete_goods(obj){
    var totalMoney = 0;
    var totalNumber = 0;
    if(obj.parents("tbody").children(".pa").length > 1 ){
        var i = 1;
        var o = obj.parents(".pa").nextAll(".active");
        obj.parents(".pa").remove();
        o.parents("tbody").children(".pa").each(function(k,v){
            $(this).children("td").eq(0).html(i);
            i++;
            var current = parseInt($(this).find("input[name='goods_number[]']").val());
            var single = parseFloat($(this).find("input[name='goods_total[]']").val());
            totalMoney = single+totalMoney;
            totalNumber = totalNumber+current;
        });
        o.find(".totalMoney").html(currencySymbol+toDecimal(totalMoney));
        o.find(".number").html(totalNumber);
    }else{
        layer.msg("请保留一行信息");
    }
}

/*类别列表*/
var arr, //声明类别缓存
    thisObj; //当前类别对象
function cateNameList(obj) {
    thisObj = obj.parents('.dropDown');
    $('span.open').removeClass('open hover');
    thisObj.blur(function(){  //鼠标移除后将不再显示列表
        $('span.open').removeClass('open hover');
    });
    thisObj.children("ul").remove(); //移除已经存在的列表
    if (typeof(arr) == 'object'){
        cate_html(arr);
    }else{
        $.ajax({
            url:brandTypeUrl,
            type:'post',
            dataType:'json',
            success:function (data) {
                sysException(data);
                arr=data;   //首次查询将把值进行保存
                if(data.state == 1){
                    cate_html(data)
                }else{
                    layer.msg(data.message);
                }
            }
        })
    }
}

/**
 * 物品类型html
 * @param data 对象
 */
function cate_html(data){
    var html = "";
    html += '<ul class="dropDown-menu menu radius box-shadow">';
    $.each(data.message,function(key,val){
        var icon = "";
        if(val['children'] != null){
            icon = '<i class="arrow Hui-iconfont"></i>';
        }
        html += '   <li class="" ><a href="javascript:void(0);" onclick="assignCateName(\''+val.categoryId+'\',\''+val.categoryName+'\')" >'+val.categoryName+icon+'</a>';
        html += '       <ul class="menu">';
        if(val['children'] != null){
            $.each(val.children,function(k,v){
                html += '     <li><a href="javascript:void(0);" onclick="assignCateName(\''+v.categoryId+'\',\''+v.categoryName+'\')">'+v.categoryName+'</a></li>';
            })
        }
        html += '       </ul>';
        html += '   </li>';
    });
    html += '</ul>';
    thisObj.append(html);
}

/**
 * 类别框赋值
 * @param categoryId 类别id
 * @param categoryName 类别名称
 */
function assignCateName(categoryId,categoryName){
    thisObj.children("input").eq(0).val(categoryName)
    thisObj.children("input").eq(1).val(categoryId)
}

/*品牌名称搜索*/
var brandSearchName = '';
function brandNameSearch(obj){
    brandSearchName = '';
    var name = '';
    $('span.open').removeClass('open hover');

    var cateId = obj.parents(".text-c").prev().find("input[name='cate_id[]']").val();
    if(cateId !== '' && cateId !== null&&obj.val()===''){
        obj.parent().children("ul").remove();
        //第一次点击，有分类按分类搜索品牌
        searchBrandByCateId(obj,cateId);
    }
    obj.bind('input propertychange',function(){  //检索
        obj.parent().children("ul").remove();
        name = obj.val();
        if(name != brandSearchName){
            brandSearchName = name;
            var id = obj.next().val();
            if(id == '' || id == null){
                obj.next().val("");
            }
            if(name===''&&cateId !== '' && cateId !== null){
                //如果值为空，并且有分类，按分类搜索品牌
                obj.parent().children("ul").remove();
                searchBrandByCateId(obj,cateId);
            }else if(name===''){
                //如果全部都为空，则不搜索
                obj.parent().children("ul").remove();
            }else {
                $.ajax({
                    url:brandNameUrl,
                    type:'post',
                    data:{name:name},
                    dataType:'json',
                    success:function (data) {//展示列表
                        sysException(data);
                        if(data.state == 1){
                            var html = "";
                            html += '<ul class="dropDown-menu menu radius box-shadow"';
                            if(data.message.length >6){
                                html += 'style = "height:200px;overflow:auto;"';
                            }
                            html += '>';
                            $.each(data.message,function(key,val){
                                html += '<li class=""><a href="javascript:void(0);"  val="'+val.brandName+'" onclick="assignBrandName(\''+val.brandId+'\',$(this))">'+val.brandName+'</a>';
                            });
                            html += '</ul>';
                            obj.parent().append(html);
                        }
                    }
                })
            }
        }
    })
}

/**
 * 单位搜索框
 * @param obj
 */
var searchName = '';
function unitSearch(obj) {
    obj.parent("span").children("ul").remove();
    var unit = $(obj).val();
    publicUnit(unit,obj);
    $('span.open').removeClass('open hover');
    searchName = '';
    obj.bind('input propertychange',function() {  //检索
        unit = $(this).val();
        if(unit != searchName){
            searchName = unit;
            obj.parent("span").children("ul").remove();
            publicUnit(unit,obj);
        }
    });
}

function publicUnit(unit,obj){
    $.ajax({
        url: unitUrl,
        type: 'post',
        data: {unit: unit},
        dataType: 'json',
        success: function (data) {//展示列表
            if(data && data.length >0){
                var html = "";
                html += '<ul class="dropDown-menu menu radius box-shadow"';
                if(data.length >6){
                    html += 'style = "width:50px;height:200px;overflow:auto;"';
                }
                html += '>';
                $.each(data,function(key,val){
                    html += '<li class=""><a href="javascript:void(0);"  val="'+val.unitName+'" onclick="assignBrandName(\''+val.unitName+'\',$(this))">'+val.unitName+'</a>';
                });
                html += '</ul>';
                $(obj).parent().append(html);
            }
        }
    });
}

/**
 * 按分类搜索品牌
 * @param obj  输入框对象
 * @param cateId 分类id
 */
function searchBrandByCateId(obj,cateId) {
    $.ajax({  //初始点击显示五条数据
        url:selectNameByCateIdUrl,
        type:'post',
        data:{cateId:cateId},
        success:function(data){
            sysException(data);
            var html = "";
            if(data.length > 0){
                html += '<ul class="dropDown-menu menu radius box-shadow"';
                if(data.length >6){
                    html += 'style = "height:200px;overflow:auto;"';
                }
                html += '>';
                $.each(data,function(key,val){
                    html += '<li class=""><a href="javascript:void(0);"  val="'+val.brandName+'" onclick="assignBrandName(\''+val.brandId+'\',$(this))">'+val.brandName+'</a>';
                });
                html += '</ul>';
                obj.parent().append(html);
            }
        }
    });
}

/**
 * 品牌框赋值
 * @param brandId 品牌id
 * @param obj 品牌对象
 */
function assignBrandName(brandId,obj){
    var brandName= obj.attr("val");
    obj.parents(".dropDown").children("input").eq(0).val(brandName);
    obj.parents(".dropDown").children("input").eq(1).val(brandId);
}

/**
 *  添加一条数据的html
 * @param i 序号
 * @param unit  单位数据
 * @returns {string}
 */
function addAGoodsHtml(i){
    var html = '';
    html += '<tr class="pa">';
    html += '   <td class="text-c">'+i+'</td>';
    html += '   <td class="text-c"><input type="text" name="goods_name[]" maxlength="80" autocomplete="off"  class="input-text text-c"></td>';
    html += '   <td class="text-c">';
    html += '       <span class="dropDown" style="display: block;" >';
    html += '           <input type="text" name="cate_name[]" readonly  autocomplete="off" onclick="cateNameList($(this))" class="input-text text-c" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
    html += '           <input type="hidden" value="" name="cate_id[]">';
    html += '       </span>';
    html += '   </td>';
    html += '   <td class="text-c">';
    html += '       <span class="dropDown" style="display: block;">';
    html += '           <input type="text" name="brand_name[]"  maxlength="30" autocomplete="off" onclick="brandNameSearch($(this))" class="input-text text-c" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
    html += '           <input type="hidden" value="" name="brand_id[]">';
    html += '       </span>';
    html += '   </td>';
    html += '   <td class="text-c">';
    html += '       <span class="dropDown" style="display: block;">';
    html += '           <input type="text" name="goods_unit[]"  maxlength="10" autocomplete="off" onclick="unitSearch($(this))" class="input-text text-c" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
    html += '       </span>';
    html += '   </td>';
    html += '   <td class="text-c">';
    html += '       <input type="text" name="goods_number[]" maxlength="4" value="0" onchange="goodsNumber($(this))" autocomplete="off" class="input-text text-c">';
    html += '   </td>';
    html += '   <td class="text-c">';
    html += '       <input type="text" name="goods_price[]"  maxlength="8"  value="0.00"  onchange="goodsPrice($(this))" autocomplete="off" class="input-text text-r">';
    html += '   </td>';
    html += '   <td class="text-c">';
    html += '       <input type="text" name="goods_total[]"  value="0.00" onchange="goodsTotal($(this))" autocomplete="off" class="input-text text-r">';
    html += '   </td>';
    html += '   <td class="text-c">';
    html += '       <input type="button" class="btn btn-danger size-MINI radius" onclick="delete_goods($(this))" value="删除"/>';
    html += '   </td>';
    html += '</tr>';
    return html;
}

/*目的地html*/
function destinationHtml(list,isDefault){
    $("select[name='line_name']").children('.reLine').remove(); //移除产品路线
    $("select[name='destination']").children('.des').remove(); //移除目的地
    var html = '';
    if(list != '' && list != null){
        $.each(list,function(k,v){
            html += '<option class="des" value="'+v.destination+'"';
            if(isDefault == v.destination){
                html += 'selected';
            }
            html += '>'+v.destination+'</option>';
        });
    }else{
        html += '<option class="des" value="">暂无数据</option>';
    }
    return html;
}

/*产品路线html*/
function lineHtml(list,isDefault){

    var html = '<option value="0">请选择</option>';
    if(list != '' && list != null){
        $.each(list,function (k,v) {
            html += '<option class="reLine"  value="'+v.line_id+'_'+v.line_name+'_'+v.line_price_id+'"';
            if(isDefault == v.line_id){
                html += 'selected';
            }
            html += '>'+v.line_name+'</option>';
        });
    }else{
        html += '<option value="">暂无数据</option>';
    }
    return html;
}

/*服务项目html*/
function servicesHtml(list,value){
    if(list != '' && list != null){
        if(value.length>0){
            $.each(list,function(key,val){
                $.each(value,function(a,b){
                    if(val.servicesCode == b.servicesCode){
                        list[key]['status'] = 1;
                        if(val.dataType == 1  &&  val.isInput == 1 && val.servcesNumberType == 1){
                            list[key]['servicesNumber'] = b.servicesNumber;
                            list[key]['servicesNote'] = b.servicesNote;
                            list[key]['servicesTotelMoney'] = b.servicesTotelMoney;
                        }
                        if(val.dataType == 2  &&  val.isInput == 1 && val.servcesNumberType == 0){
                            list[key]['servicesInputMoney'] = b.servicesInputMoney;
                            list[key]['servicesNote'] = b.servicesNote;
                            list[key]['servicesTotelMoney'] = b.servicesTotelMoney;
                        }
                    }
                })
            })
        }
        var html = '';
        html += '<div class="panel-title services">服务项目</div>';
        html += '<div class="panel-body panel_body_block mt-10 bg-fff services" style="border: 1px solid #ddd;">';
        html += '   <div class="form form-horizontal pl-10 pb-10 ">';
        html += '        <div class="row cl">';
        $.each(list,function(k,v){
            if(v.dataType == 1 && v.isInput == 0 && v.servcesNumberType == 0){
                html += '<div class="skin-mini f-l ml-10">';
                html += '   <div class="check-box" style="padding-top: 3px;">';
                html += '       <input type="checkbox" id="checkbox-'+k+'" name="services_code[]" value="'+v.servicesCode+'_100"';
                if(v.status == 1){
                    html += 'checked';
                }
                html += '>';
                html += '       <label for="checkbox-'+k+'" style="font-size: 12px;" title="'+v.serviceDescription+'">'+v.servicesName+'</label>';
                html += '       <input type="hidden" value="'+v.servicesCost+'" id="cost">';
                html += '       <strong class="f-1 c-orange" ';
                if(v.status != 1){
                    html += 'hidden';
                }
                html += '>'+currencySymbol+toDecimal(v.servicesCost);
                html += '       </strong>';
                html += '   </div>';
                html += '</div>';
            }else if(v.dataType == 2 && v.isInput == 1 && v.servcesNumberType == 0){
                html += '<div class="skin-mini f-l ml-10">';
                html += '   <div class="check-box f-l" style="padding-top: 3px;padding-right: 10px;">';
                html += '       <input type="checkbox" id="checkbox-77" name="services_code[]" value="'+v.servicesCode+'_210"';
                if(v.status == 1){
                    html += 'checked';
                }
                html += '>';
                html += '       <label for="checkbox-77" style="font-size: 12px;" title="'+v.serviceDescription+'">'+v.servicesName+'('+v.servicesRate+'%)</label>';
                html += '       <input type="hidden" id="maxMoney" value="'+v.servicesCost+'"/>';
                html += '       <input type="hidden" id="rate" value="'+v.servicesRate+'"/>';
                html += '   </div>';
                html += '   <div class="f-l pr-10" style="padding-top: 3px;">';
                html += '       <input class="width_80" style="border: 1px solid #ddd;" value = "';
                if(v.status == 1){
                    html += v.servicesInputMoney;
                }
                html += '"       name="InsuranceMoney"  maxlength="6" type="text" ';
                if(v.status != 1){
                html += 'disabled';
                }
                html += '       placeholder="填写保险金"/>';
                html += '       <strong class="f-1 c-orange"';
                if(v.status != 1){
                    html += 'hidden >';
                }else{
                    html += '>'+currencySymbol+toDecimal(v.servicesTotelMoney);
                }
                html += '       </strong>';
                html += '   </div>';
                html += '</div>';
                html += '<div class="skin-mini f-l ml-10" ';
                if(v.status != 1){
                    html += 'style="display: none;"';
                }
                html += '>';
                html += '   <div class="check-box f-l" style="padding-top: 5px;padding-right: 10px;">';
                html += '       <input type="checkbox" id="checkbox-88" name="isTrue" checked>';
                html += '       <label for="checkbox-88"  style="font-size: 12px;">我已阅读并同意<a class="c-orange" href="#">保险条款</a></label>';
                html += '   </div>';
                html += '</div>';
            }else if(v.dataType == 1 && v.isInput == 1 && v.servcesNumberType == 1){
                html += '<div class="skin-mini f-l ml-10">';
                html += '   <div class="check-box f-l" style="padding-top: 3px;padding-right: 10px;">';
                html += '       <input type="checkbox" id="checkbox-'+k+'" name="services_code[]"  value="'+v.servicesCode+'_111"';
                if(v.status == 1){
                    html += 'checked';
                }
                html += '>';
                html += '       <label for="checkbox-'+k+'" style="font-size: 12px;" title="'+v.serviceDescription+'">'+v.servicesName+'</label>';
                html += '   </div>';
                html += '   <div class="f-l pr-10" style="padding-top: 1px;">';
                html += '       <span class="width_50 bg-fff f-l mr-5">';
                html += '       <select name="services_number" class="select f-l">';
                $.each(v.servcesNumberData,function(key,val){
                    html += '<option value="'+val+'"';
                    if(v.status == 1 && v.servicesNumber == val){
                        html +='selected';
                    }
                    html += '>'+val+'</option>';
                });
                html += '       </select>';
                html += '       </span>';
                html += '       <input class="width_80" name="service_note" maxlength="150"  style="border: 1px solid #ddd;" type="text" value="';
                if(v.status == 1){
                    html += v.servicesNote+'" ';
                }else{
                    html += '" disabled';
                }
                html += '     placeholder="输入产品备注">';
                html += '       <input type="hidden" id="costMoney" value="'+v.servicesCost+'"/>';
                html += '       <strong class="f-1 c-orange" ';
                if(v.status != 1){
                    html += 'hidden>';
                }else{
                    html +='>'+currencySymbol+toDecimal(v.servicesTotelMoney);
                }
                html += '</strong>';
                html += '   </div>';
                html += '</div>';
            }
        });
        html += '       </div>';
        html += '   </div>';
        html += '</div>';
        return html;
    }else{
        return '';
    }

}

var price = 0, //单价
    number = 0, //数量
    total = 0; //总和
/*根据数量计算*/
function goodsNumber(obj) {
    var totalMoney  = 0;
    number = parseInt(obj.val());
    if(isNaN(number) || number <= 0){
        obj.val('0');
        return false;
    }
    obj.val(number);
    price = toDecimal(obj.parent("td").next().children("input").val());
    total = price*parseInt(number);
    if(isNaN(total)||total>99999999){
        obj.val('0');return false;
    }
    obj.parent("td").next().next().children().val(toDecimal(total));
    var totalNumber = 0;
    obj.parents("tbody").children(".pa").each(function(){
        var current = parseInt($(this).find("input[name='goods_number[]']").val());
        total = $(this).find("input[name='goods_total[]']").val();
        totalMoney = total*1+totalMoney*1;
        totalNumber = totalNumber+current;
    });
    obj.parents('table').find(".number").text(totalNumber);
    obj.parents('table').find(".totalMoney").text(currencySymbol+toDecimal(totalMoney));
}

/*根据单价计算*/
function goodsPrice(obj){
    var totalMoney = 0;
    number = parseInt(obj.parent("td").prev().children("input").val());
    price = parseFloat(obj.val());
    if(isNaN(price) || price <= 0){ //验证数字是否合法
        obj.val('0.00');
        return false;
    }
    obj.val(toDecimal(price));
    total =price*parseInt(number);
    if(isNaN(total)||total>99999999){
        obj.val('0.00');return false;
    }
    obj.parent("td").next().children().val(toDecimal(total));
    obj.parents("tbody").children(".pa").each(function(){
        total = $(this).find("input[name='goods_total[]']").val();
        totalMoney = total*1+totalMoney*1;
    });
    obj.parents('table').find(".totalMoney").text(currencySymbol+toDecimal(totalMoney));

}

/*总金额计算*/
function goodsTotal(obj){
    number = obj.parent("td").prev().prev().children("input").val();
    price = obj.parent("td").prev().children("input").val();
    obj.blur(function(){
        if(number != null && number != '' && price != '' && price != null){
            total = toDecimal(price)*parseInt(number);
            obj.val(toDecimal(total))
        }else{
            obj.val("0.00")
        }
    })
}

/**
 * 管理收货地址
 * @param id  包裹id
 */
function administrationDeliveryAddress(id){
    var destination=$('#destination'+id).val();
    if(destination==='0'||!destination){
        layer.msg("请先选择目的地！");
        return false;
    }
    layer.open({
        type: 2,
        title: '收货地址管理',
        area: ['65%', '417px'],
        content: orderReceiverAddress+'?pack='+id+'&destination='+destination,
        cancel: function(){
            $("#status").val("1");
        },
        end:function(){
            var status =$("#status").val();
            if(status ==='1'){
                return false;
            }else if(status == '2'){
                layer.open({
                    type: 2,
                    title: '新增收货地址',
                    area: ['750px', '78%'],
                    content: orderReceiverAddressAdd+'?destination='+destination,
                    cancel: function(){
                        $("#status").val("1");
                    },
                    end:function(){
                        var status =$("#status").val();
                        if(status === '1') {
                            return false;
                        }else{
                            administrationDeliveryAddress(id);
                        }
                    }
                });
            }else if(status=='3'){ //修改地址
                var address =$("#status").attr("addressId").split("_");
                layer.open({
                    type: 2,
                    title: '修改收货地址',
                    area: ['750px', '78%'],
                    content: orderReceiverAddressAdd+'?addressId='+address[0]+'&destination='+address[1],
                    cancel: function(){
                        $("#status").val("1");
                    },
                    end:function(){
                        var status =$("#status").val();
                        if(status === '1') {
                            return false;
                        }else{
                            administrationDeliveryAddress(id);
                        }
                    }
                });
            }
        }
    });
}

/*管理发货地址*/
function administrationSenderAddress(){
    layer.open({
        type: 2,
        title: '发货地址管理',
        area: ['65%', '417px'],
        content: orderDeliveryAddress+'?destination='+countryName,
        cancel: function(){
            $("#status").val("1");
        },
        end:function(){
            var status =$("#status").val();
            if(status == 1){
                return false;
            }else if(status==2){
                layer.open({
                    type: 2,
                    title: '新增发货地址',
                    area: ['750px', '68%'],
                    content: orderDeliveryAddressAdd+'?destination='+countryName,
                    cancel: function(){
                        $("#status").val("1");
                    },
                    end:function(){
                        var status =$("#status").val();
                        if(status == 1) {
                            return false;
                        }else{
                            administrationSenderAddress();
                        }
                    }
                });
            }else if(status == 3){
                var address =$("#status").attr("addressId").split("_");
                layer.open({
                    type: 2,
                    title: '修改发货地址',
                    area: ['750px', '68%'],
                    content: orderDeliveryAddressAdd+'?addressId='+address[0]+'&destination='+address[1],
                    cancel: function(){
                        $("#status").val("1");
                    },
                    end:function(){
                        var status =$("#status").val();
                        if(status == 1) {
                            return false;
                        }else{
                            administrationSenderAddress();
                        }
                    }
                });
            }
        }
    });
}

/**
 * 新增一条收货地址
 * @param no 板块编号
 */
function addDeliveryAddress(no){
    var destination=$('#destination'+no).val();
    if(destination==='0'||!destination){
        layer.msg("请先选择目的地！");
        return false;
    }
    layer.open({
        type: 2,
        title: '新增收货地址',
        area: ['750px', '74%'],
        content: orderReceiverAddressAdd+'?destination='+destination+'&No='+no
    });
}

/**
 * 新增一条发货地址
 */
function addSenderAddress(){
    layer.open({
        type: 2,
        title: '新增发货地址',
        area: ['750px', '65%'],
        content: orderDeliveryAddressAdd+'?destination='+countryName
    });
}

