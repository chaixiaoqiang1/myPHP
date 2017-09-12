/**
 * 地址添加
 * Created by Srako on 2017/05/16.
 */

$(function(){

    //增加验证规则，地址可见必选
    $.validator.addMethod('seeNeed',function(value,element){
        if((value<=0||value.length<=0)&&$(element).is(":visible")){
            return false;
        }else {
            return true;
        }
    },"请选择地址");

    //邮箱规则验证
    jQuery.validator.addMethod('email', function(value, element) {
        var tel = /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/;
        return this.optional(element) || (tel.test(value));
    }, '请正确填写您的邮箱');


    $.validator.addMethod('needName',function(value,element){
        var name=$("input[name='name']");
        if(name.val().length>0){
            return true;
        }else {
            name.focus();
            $(element).val('');
            return false;
        }
    },"请先输入收货人名称");


    $.validator.addMethod('needType',function(value,element){
        if($("#status").val()==1){
            return true;
        }
        if($("select[name='certificate_type']").val().length>0){
            return true;
        }else {
            $(element).val('');
            return false;
        }
    },"请先选择证件类型");

    $("#form-article-add").validate({
        rules:{
            country:{
                required:true
            },
            province:{
                seeNeed:true
            },
            city:{
                seeNeed:true
            },
            area:{
                seeNeed:true
            },
            address:{
                required:true,
            },
            name:{
                required:true,
                maxlength:15,
            },
            postcode:{
                maxlength:10
            },
            mobile_first:{
                required:true,
            },
            mobile:{
                required:true,
                maxlength:20,
                remote:{
                    url:check_url,
                    data:{
                        type:'memberMobile',
                        val:function(){
                            return $('#mobile').val();
                        }
                    },
                    type:'post',
                    dataType:'json',
                    dataFilter: function (data) {//判断控制器返回的内容
                        data=JSON.parse(data);
                        if(!data.success){
                            layer.msg(data.message);
                            return false;
                        }else {
                            return true;
                        }
                    }
                }
            },
            email:{
                required:true,
                email:true
            },
            certificate_type:{
                seeNeed:true,
            },
            certificate_num:{
                needName:true,
                needType:true,
                seeNeed:true,
                remote:{
                    url:checkCertificate_url,
                    data:{
                        certificateType:function () {
                            $('#loadImg').show();
                            $('.cardNotice').text('').hide();
                            return $("select[name='certificate_type']").val();
                        },
                        certificateNo:function(){
                            return $("input[name='certificate_num']").val();
                        },
                        cardName:function () {
                            return $("input[name='name']").val();
                        }
                    },
                    type:'post',
                    dataType:'json',
                    dataFilter: function (data) {//判断控制器返回的内容
                        $('#loadImg').hide();
                        data=JSON.parse(data);
                        if(!data.state){
                            $('.cardNotice').text(data.message).show();
                        }else {
                            $('.cardNotice').text('').hide();
                        }
                        return true;

                    }
                }
            },
        },
        messages: {
            name:{
                required:'请输入姓名',
                maxlength:'姓名长度过长'
            },
            mobile_first:{
                required:'',
            },
            mobile:{
                required:'请输入手机号码',
                remote:'手机号不正确',
            },
            email:{
                required:'请输入邮箱'
            },
            certificate_type:{
                seeNeed:"请选择证件类型",
            },
            certificate_num:{
                seeNeed:"请输入证件号码",
            },
        },
        success:"valid",
        errorElement:'div',
        onkeyup:false,
        focusCleanup:true,
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(form){
            $("#submit").attr("disabled",'disabled');
            var param = $("#form-article-add").serialize();
            $.ajax({
                type:'post',
                data:param,
                dataType:'json',
                beforeSend:function(){
                    loading();
                },
                success:function(data){
                    sysException(data);
                    if(data.state == 1){
                        layer.msg(data.message);
                        setTimeout(function(){
                            $("#status",parent.document).val("2");
                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                            parent.layer.close(index);//关闭layer.open窗体
                        },500);
                        var id = $("#status").val();    //区分收发货地址
                        var NO = $("#No").val();    //区分板块
                        var deliveryId = $("#deliveryAddressNo"+NO,parent.document).val(); //验证收货地址是否显示
                        if(deliveryId == 1 && id == 1){ //验证是否是添加收货地址地址
                            $("#deliveryName"+NO,parent.document).text(data.addressInfo.deliveryName);
                            $("#deliveryMobile"+NO,parent.document).text(data.addressInfo.deliveryMobile);
                            $("#deliveryCountry"+NO,parent.document).text(data.addressInfo.country);
                            $("#deliveryProvince"+NO,parent.document).text(data.addressInfo.province);
                            $("#deliveryCity"+NO,parent.document).text(data.addressInfo.city);
                            $("#deliveryArea"+NO,parent.document).text(data.addressInfo.area);
                            $("#deliveryTown"+NO,parent.document).text(data.addressInfo.town);
                            $("#deliveryAddress"+NO,parent.document).text(data.addressInfo.address);
                            $("#deliveryPostCode"+NO,parent.document).text(data.addressInfo.postcode);
                            $("#certificateNum"+NO, parent.document).html('');
                            if(data.addressInfo.countryCode == 'CHN') {
                                $("#certificateNum"+NO, parent.document).html(data.addressInfo.certificateNum + '&nbsp;&nbsp;&nbsp;&nbsp;' + data.verification); //身份证
                            }
                            $("#deliveryAddressId"+NO,parent.document).val(data.addressInfo.deliveryAddressId);
                            $(".delivery"+NO,parent.document).show();
                            $(".falseDelivery"+NO,parent.document).hide();
                        }
                        var senderAddressNo = $("#senderAddressNo"+NO,parent.document).val();
                        if(senderAddressNo == 1 && id == 2){
                            var type = $("#type").val();
                            if(type == 'sender'){
                                var serialNumber = $("#serialNumber",parent.document).attr("serialNumber");
                                for(var i = 1 ; i <= serialNumber ; i++){
                                    $("#senderName"+i,parent.document).text(data.addressInfo.senderName);
                                    $("#senderMobile"+i,parent.document).text(data.addressInfo.senderMobile);
                                    $("#senderCountry"+i,parent.document).text(data.addressInfo.country);
                                    $("#senderProvince"+i,parent.document).text(data.addressInfo.province);
                                    $("#senderCity"+i,parent.document).text(data.addressInfo.city);
                                    $("#senderArea"+i,parent.document).text(data.addressInfo.area);
                                    $("#senderTown"+i,parent.document).text(data.addressInfo.town);
                                    $("#senderAddress"+i,parent.document).text(data.addressInfo.address);
                                    $("#senderPostCode"+i,parent.document).text(data.addressInfo.postcode);
                                    $("#senderAddressId1",parent.document).val(data.addressInfo.senderAddressId);
                                    $(".sender",parent.document).show();
                                    $(".falseSender",parent.document).hide();
                                }
                            }else{
                                $("#senderName"+NO,parent.document).text(data.addressInfo.senderName);
                                $("#senderMobile"+NO,parent.document).text(data.addressInfo.senderMobile);
                                $("#senderCountry"+NO,parent.document).text(data.addressInfo.country);
                                $("#senderProvince"+NO,parent.document).text(data.addressInfo.province);
                                $("#senderCity"+NO,parent.document).text(data.addressInfo.city);
                                $("#senderArea"+NO,parent.document).text(data.addressInfo.area);
                                $("#senderTown"+NO,parent.document).text(data.addressInfo.town);
                                $("#senderAddress"+NO,parent.document).text(data.addressInfo.address);
                                $("#senderPostCode"+NO,parent.document).text(data.addressInfo.postcode);
                                $("#senderAddressId"+NO,parent.document).val(data.addressInfo.senderAddressId);
                                $(".sender"+NO,parent.document).show();
                                $(".falseSender"+NO,parent.document).hide();
                            }
                        }
                    }else{
                        $("#submit").attr("disabled",false);
                        layer.msg(data.message);
                    }
                }
            })

        }
    });

    $("#closeIndex").click(function(){ //返回到管理页面
        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
        parent.layer.close(index);//关闭layer.open窗体
    });

    /**
     * 地址N级联动
     */
    $("body").on("change","select.link",function(){
        var child = parseInt($(this).attr('link')),
             parentId = $(this).val();
        if(!parentId)   return false;
        if(child=='1'&&$("#status").val() == '1'){
            //验证是否为中国
            var val=$(this).find(":selected").val().split('_'),
                mobile_first=$("select[name='mobile_first']");
            if(val[2]=='CHN'){
                $(".certificate").show();
            }else {
                $(".certificate").hide();
            }
            if(val[3]&&val[3]!=''&&mobile_first.val()==''){
                mobile_first.val(val[3]);
            }

        }
        $.ajax({
            url:city_url,
            type:'post',
            data:{parentId:parentId},
            dataType:'json',
            success:function (data) {
                var html = '';
                $.each(data,function (k,v){
                    html +='<option class="re" value="'+v.country_id+'_'+v.country_name+'">'+v.country_name+'</option>';
                });
                for(var i = 1;i <= 5;i++){
                    if(child < i) {
                        $("#link"+i).parent().next().addClass("hide").children("select").children(".re").remove();
                    }
                }
                var obj =  $("#link5");
                if(child == 4 && data.length <= 0 ){
                    obj.children(".re").remove().parents(".row").addClass("hide");
                }else if(child == 4){
                    obj.parents(".row").removeClass("hide");
                    obj.children(".re").remove();
                    obj.append(html);
                }else{
                    if(child != 5){
                        obj.children(".re").remove();
                        obj.parents(".row").addClass("hide");
                    }
                    if(data.length <= 0){
                        $("#link"+child).parent().next().addClass("hide");
                    }else{
                        $("#link"+child).parent().next().removeClass("hide").children("select").children(".re").remove();
                        $("#link"+child).parent().next().children("select").append(html);
                    }
                }
            }
        });
    });

});