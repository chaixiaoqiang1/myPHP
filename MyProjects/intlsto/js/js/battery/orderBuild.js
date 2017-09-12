/**
 * 生成运单
 * BySrako
 */
$(function () {
    $('.skin-minimal input').iCheck({
        checkboxClass: 'icheckbox-blue',
        radioClass: 'iradio-blue',
        increaseArea: '20%'
    });
    //收货仓库改变
    $("select[name='consolidator']").on('change',function () {
        var consolidator=$(this).val().split("_");
        if(consolidator[0]==''){ return false;}
        $.ajax({
            url:destination_url,
            data:{consolidator_no:consolidator[0]},
            type:'post',
            dataType:'json',
            success:function (data) {
                sysException(data);
                var html='<option value="">请选择</option>';
                $("select[name='line']").html(html);
                if(data&&data.length!=null){
                    $.each(data,function(k,v){
                        html += '<option value="'+v.destination+'">'+v.destination+'</option>';
                    });
                }else{
                    html += '<option value="">暂无数据</option>';
                }
                $("select[name='destination']").html(html);
                html=null;
            }
        })
    });

    //目的地改变
    $("select[name='destination']").on('change',function () {
        var destination=$(this).val(),
            consolidator=$("select[name='consolidator']").val().split("_");
        if(destination==''){ return false;}

        $.ajax({
            type:'post',
            url:selectDestinationByLineName,
            data:{destination:destination,consolidatorNo:consolidator[0]},
            dataType:'json',
            success:function (data) {
                sysException(data);
                var html = '<option value="">请选择</option>';
                if(data && data.length != null){
                    $.each(data,function (k,v) {
                        html += '<option value="'+v.line_id+'_'+v.line_name+'">'+v.line_name+'</option>';
                    });
                }else{
                    html += '<option value="">暂无数据</option>';
                }
                $("select[name='line']").html(html);
                html=null;
            }
        })
    });

});

//配置参数
var config={};
/**
 * 第一步完成
 */
function overOne() {
    //保存配置
    config = $('form').serializeObject();
    if(config.consolidator===''){
        layer.msg('请选择海外收货仓库');return false;
    }else if(config.sendCountry===''){
        layer.msg('请选择发货国家');return false;
    }else if(config.destination===''){
        layer.msg('请选择目的地');return false;
    }else if(config.line===''){
        layer.msg('请选择产品线路');return false;
    }
    config.taxPayerListForId=[];//定义关税支付人不同数组
    config.idTrackingNoList={};//定义海外快递单号数组

    $('.step').eq(1).addClass('active').removeClass('disabled').siblings().removeClass('active').addClass('disabled');
    setHtml('overTwo()','下一步');//设置页面的内容

    $('#table').bootstrapTable({
        url: getOrderByIds_url+'?idList='+idList,
        columns: [{
            title: '序号',
            align: 'center',
            formatter:function (value,row,index) {
                return index+1;
            },
            width:50
        }, {
            field: 'tradeId',
            title: '订单号',
            align: 'center',
            width:200
        }, {
            field: 'platformName',
            title: '订单涞源',
            align: 'center',
            width:200
        },{
            title: '关税支付',
            align: 'center',
            width:150,
            formatter:function (value,row,index) {
                var name='';
                if($.inArray(row.apiOrdersId,config.taxPayerListForId)>-1){
                    name=config.taxPayer==1?'收货人':'发货人';
                }else {
                    name=config.taxPayer==1?'发货人':'收货人';
                }
                return '<button class="btn btn-default size-MINI" onclick="changePayer(this,'+row.apiOrdersId+')">'+name+'</button>'
            },
        },{
            title: '海外快递单号',
            align: 'center',
            formatter:function (value,row,index) {
                if(config.deliveryType==2){
                    return '';
                }
                var no=config.idTrackingNoList[row.apiOrdersId]===undefined?'':config.idTrackingNoList[row.apiOrdersId]['trackingNo'];
                return '<input type="text" class="input-text trackingNo" id="apiId_'+row.apiOrdersId+'" onchange="saveTrackingNo(this,'+row.apiOrdersId+')" value="'+no+'"/>'
            },
        }],
        striped: true,
        showRefresh: true, //会出现刷新按钮
        pagination: true,
        clickToSelect: true,
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        buttonsAlign: "right",
        undefinedText:'',
        /*操作按钮在水平方向的位置*/
        paginationHAlign: "left",
        /*分页条在水平方向的位置*/
        paginationDetailHAlign: "right",
        /*分页详细信息在水平方向的位置*/
        paginationPreText: "上一页",
        /*指定上一页的内容*/
        paginationNextText: "下一页",
    });
}


/**
 * 第二步完成
 */
function overTwo() {
    //验证是否填完所有单号
    if(!checktrackingNo()){layer.msg("请完善所有海外快递单号");return false;}
    $('.step').eq(2).addClass('active').removeClass('disabled').siblings().removeClass('active').addClass('disabled');
    setHtml('overThree()','生成运单');//设置页面的内容

    $('#table').bootstrapTable({
        url: getOrderByIds_url+'?idList='+idList,
        columns: [{
            title: '序号',
            align: 'center',
            formatter:function (value,row,index) {
                return index+1;
            },
            width:50
        }, {
            field: 'tradeId',
            title: '订单号',
            align: 'center',
            width:200
        }, {
            field: 'platformName',
            title: '订单涞源',
            align: 'center',
            width:200
        },{
            title: '关税支付',
            align: 'center',
            width:150,
            formatter:function (value,row,index) {
                var name='';
                if($.inArray(row.apiOrdersId,config.taxPayerListForId)>-1){
                    name=config.taxPayer==1?'收货人':'发货人';
                }else {
                    name=config.taxPayer==1?'发货人':'收货人';
                }
                return name;
            },
        },{
            title: '海外快递单号',
            align: 'center',
            formatter:function (value,row,index) {
                return config.idTrackingNoList[row.apiOrdersId]===undefined?'':config.idTrackingNoList[row.apiOrdersId]['trackingNo'];
            },
        }],
        striped: true,
        showRefresh: true, //会出现刷新按钮
        clickToSelect: true,
        pagination: true,
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        buttonsAlign: "right",
        undefinedText:'',
        /*操作按钮在水平方向的位置*/
        paginationHAlign: "left",
        /*分页条在水平方向的位置*/
        paginationDetailHAlign: "right",
        /*分页详细信息在水平方向的位置*/
        paginationPreText: "上一页",
        /*指定上一页的内容*/
        paginationNextText: "下一页"
    });
}

/**
 * 提交生成运单
 */
function overThree() {
    loading();$("button").addClass('disabled');
    $.ajax({
        url:order_build_url,
        type:'post',
        data:{config:config,idList:idList},
        dataType:'json',
        success:function (data) {
            sysException(data);
           if(data.state){
               layer.confirm(data.message, {
                   btn: ['继续生成','跳转包裹列表'] //按钮
               }, function(){
                   closeThis();
               }, function(){
                   parent.location.href = data.url;
               });
           }else {
               layer.confirm(data.message, {
                   btn: ['重新生成','跳转订单管理'] //按钮
               }, function(){
                   window.location.href=location.href;
               }, function(){
                   closeThis();
               });
           }
        }
    })
}



/**
 * 获取页面html
 */
function setHtml(fuc,text) {
   var html ='<table style="width: 100%;text-align: center;">' ;
    html+='   <tr>' ;
    html+='     <td style="padding: 20px 0px;font-size: 16px;">海外收货仓库：'+config.consolidator[1]+'</td>' ;
    html+='     <td style="font-size: 16px;">目的地：'+config.destination+'</td>' ;
    html+='     <td style="font-size: 16px;">产品线路：'+config.line[1]+'</td>' ;
    html+='  </tr>' ;
    html+='</table>' ;
    html+='<div class="clearfix" style="border: 1px solid #ddd;">' ;
    html+='   <table id="table"></table>' ;
    html+='</div>';
    html+='<div class="clearfix pt-20 text-c">';
    html+='   <button class="btn btn-warning radius size-L clearfix" onclick="'+fuc+'">'+text+'</button>';
    html+='</div>';
    $('.panel-body').html(html);
}


/**
 * 改变不同的关税支付人
 * @param e 输入框对象
 * @param apiOrdersId  订单id
 */
function changePayer(e,apiOrdersId) {
    var text=$(e).text()=='发货人'?'收货人':'发货人',i=$.inArray(apiOrdersId,config.taxPayerListForId);
    $(e).text(text);
    if(i>-1){
        config.taxPayerListForId.splice(i, 1);
    }else {
        config.taxPayerListForId.push(apiOrdersId);
    }
}


/**
 * 保存海外快递单号
 * @param e  输入框对象
 * @param apiOrdersId  订单id
 */
function saveTrackingNo(e,apiOrdersId) {
    //判断是否为字符与数字的组合
    var val=$(e).val(),unique=true;
    if(!val||!/^[0-9a-zA-Z]*$/g.test(val)){
        $(e).val('');
        config.idTrackingNoList[apiOrdersId]={};
        return false;
    }
    //循环判断是否有海外快递单号重复
    $.each(config.idTrackingNoList,function (k,v) {if(val==v.trackingNo) unique=false;return false;});
    if(!unique){
        layerMsg('此海外快递单号已存在',$(e));
        $(e).val('');
        config.idTrackingNoList[apiOrdersId]={};
        return false;
    }
    config.idTrackingNoList[apiOrdersId]={apiOrdersId:apiOrdersId,trackingNo:val};
}

function checktrackingNo() {
    if(config.deliveryType=='2')return true;
    var state=true;
    $.each(idList.split(','),function (k,v) {
        if(!config.idTrackingNoList[v]||!config.idTrackingNoList[v].apiOrdersId){
            $("#apiId_"+v).addClass('error bindBlack');state=false;
        }
    });
    return state;
}

// 将一个表单的数据返回成JS对象
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [ o[this.name] ];
            }
            o[this.name].push(this.value || '');
        } else {
            if(this.value.split('_').length!==1){
                o[this.name]=this.value.split('_');
            }else {
                o[this.name] = this.value || '';
            }

        }
    });
    return o;
};


/**
 * 关闭当前页面
 */
function closeThis() {
    parent.reloadTable('table');parent.idList=[];
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}