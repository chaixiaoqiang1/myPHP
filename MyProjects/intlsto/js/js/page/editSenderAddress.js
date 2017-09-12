/**
 * 编辑发货地址
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


    //收货地址修改
    $("#senderEdit").validate({
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
            senderName:{
                required:true,
                maxlength:15,
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
                            return $('#senderMobile').val();
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
            senderName:{
                required:"请输入真实姓名",
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
            var forms    =   $("#senderEdit"),
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
                            window.location.href=address_url+'?active=1';
                        }});
                    }else{
                        layer.msg(data.message );
                    }
                }
            });

        }
    });
});