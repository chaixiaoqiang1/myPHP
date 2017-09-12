/**
 * 编辑收货地址
 * Created by Srako on 2017/06/15.
 */
$(function () {

    /**
     * 收货地址N级联动
     */
    $("select.link").change(function () {
        var curr=parseInt($(this).attr('link')),
            child=parseInt($(this).attr('link'))+1,
            parentId=$(this).val();
        if(!parentId){return false;}

        //判断是否为中国
        if(curr=='1'){
            //验证是否为中国
            var val=$(this).find(":selected").val().split('_');
            if(val[2]=='CHN'){
                $(".certificate").show();
            }else {
                $(".certificate").hide();
            }

        }

        $.ajax({
            url:city_url,
            data:{parentId:$(this).val()},
            dataType:'json',
            success:function (data,status) {
                sysException(data);
                if(!data.length){
                    $("#link"+curr).parent().nextAll().hide();
                }else{
                    $("#link"+curr).parent().next().show();
                }

                var html='<option value="">请选择</option>';
                $.each(data,function (k,v){
                    html+='<option value="'+v.country_id+'_'+v.country_name+'">'+v.country_name+'</option>';
                });
                $("#link"+child).html(html);
            },
            error:function (data) {
                console.log(data.responseText);
            }
        });

        //清除级列表
        for(var i=1;i<=5;i++){
            if(child<i){
                $('#link'+i).html('<option value="">请选择</option>').parent().hide();
            }
        }
    });


    //增加验证规则，地址可见必选
    $.validator.addMethod('seeNeed',function(value,element){
        return value.length>0||!$(element).is(":visible");
    },"请选择地址");

    $.validator.addMethod('needName',function(value,element){
        var name=$("input[name='deliveryName']");
        if(name.val().length>0){
            return true;
        }else {
            name.focus();
            return false;
        }
    },"请先输入收货人名称");

    $.validator.addMethod('needType',function(value,element){
        return $("select[name='certificate_type']").val().length>0;
    },"请先选择证件类型");

    //收货地址修改
    $("#receiveEdit").validate({
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
            postcode:{
                maxlength:10,
            },
            deliveryName:{
                required:true,
                maxlength:15,
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
                            return $("input[name='deliveryName']").val();
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
            mobile_first:{
                required:true,
            },
            mobile:{
                required:true,
                remote:{
                    url:check_url,
                    data:{
                        type:'memberMobile',
                        val:function(){
                            return $('#receiverMobile').val();
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
                email:true,
            },
        },
        messages:{
            country:{
                required:"请选择所在国家",
            },
            address:{
                required:"请输入详细地址",
            },
            deliveryName:{
                required:"请输入真实姓名",
            },
            certificate_type:{
                seeNeed:"请选择证件类型",
            },
            certificate_num:{
                seeNeed:"请输入证件号码",
            },
            mobile_first:{
                required:"请选择国家区号",
            },
            mobile:{
                required:"请输入联系电话",
                remote:"联系电话不正确",
            },
            email:{
                required:"请输入邮箱",
                email:"请输入正确的邮箱",
            }
        },
        success:"valid",
        errorElement:'div',
        onkeyup:false,
        focusCleanup:true,
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(form){
            //防止重复提交
            var forms    =   $("#receiveEdit"),
                button  =   forms.find("button[type='submit']"),
                param   =   forms.serialize();
            button.addClass('disabled');
            loading();
            $.ajax({
                dateType:'json',
                data:param,
                type: 'post',
                success:function(data) {
                    sysException(data);
                    button.removeClass('disabled');
                    if(data.state){
                        layer.msg(data.message,{end:function () {
                            window.location.href=address_url;
                        }});
                    }else{
                        layer.msg(data.message );
                    }
                }
            });

        }
    });
});

/**
 * 打开身份证上传窗口
 */
function openCard() {
    layer.open({
        type: 2,
        title: '上传身份证照片',
        resize:false,
        shade: 0.5,
        area: ['600px', '620px'],
        content: upCertificate_url
    })
}