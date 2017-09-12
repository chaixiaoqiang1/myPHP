/**
 * 用户资料修改
 * Created by Srako on 2017/05/11.
 */

/**
 * 判断是否为email
 * @param email
 * @returns {boolean}
 */

$(function () {
    /**
     * 头像上传
     */
    $('#avatar').on('click',function () {
        layer.open({
            type: 2,
            title:"修改头像",
            resize:false,
            area: ['800px', '550px'], //宽高
            content: avatar_url,
        });

    });

    // 只能输入英文
    jQuery.validator.addMethod("english", function(value, element) {
        return this.optional(element) || (/^([a-zA-Z]+)$/.test(value));
    }, "仅限英文和拼音");

    $("#upInfo").validate({
        rules:{
            firstName:{
                required:true,
                english:true,
            },
            lastName:{
                required:true,
                english:true,
            },
            memberEmail: {
                required:true,
                isEmail:true,
                remote:{
                    url:check_url,
                    data:{
                        type:'memberEmail',
                        val:function(){
                            return $('#memberEmail').val();
                        }
                    },
                    type:'post',
                    dataType:'json',
                    dataFilter: function (data) {//判断控制器返回的内容
                        data=JSON.parse(data);
                        if(!data.success){
                            layerMsg(data.message,$('#memberEmail'));
                            return false;
                        }else{
                            return true;
                        }
                    }
                }
            },
            certificateCode:{
                remote:{
                    url:check_url,
                    data:{
                        type:'certificateCode',
                        val:function(){
                            return $('#certificateCode').val();
                        },
                        id:function () {
                            return $("select[name='certificateType']").find(':selected').text()
                        }
                    },
                    type:'post',
                    dataType:'json',
                    dataFilter: function (data) {//判断控制器返回的内容
                        data=JSON.parse(data);
                        if(!data.success){
                            layerMsg(data.message,$('#certificateCode'));
                            return false;
                        }else {
                            return true;
                        }
                    }
                }
            },
            mobileCode:{
                required:true,
            },
            mobile:{
                required:true,
                remote:{
                    url:check_url,
                    data:{
                        type:'memberMobile',
                        val:function(){
                            return $('#memberMobile').val();
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
        },
        messages: {
            firstName:{
                required:'名不能为空'
            },
            lastName:{
                required:'姓不能为空'
            },
            memberEmail:{
                required:'E-mail不能为空',
                email:'E-mail格式不正确',
            },
            certificateCode:{
                remote:'证件号码不正确或已绑定其他会员账号'
            },
            mobileCode:{
              required:'请选择国家区号'
            },
            mobile:{
                required:'联系电话不能为空',
                remote:'联系电话不正确'
            },
        },
        success:"valid",
        errorElement:'div',
        onkeyup:false,
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(form){
            //防止重复提交
            var forms    =   $("#upInfo"),
                button  =   forms.find("button[type='submit']"),
                param   =   forms.serialize();
            button.addClass('disabled');
            loading();
            $.ajax({
                dateType:'json',
                data:param,
                type: 'post', // 提交方式 get/post
                success:function(data) {
                    sysException(data);
                    if(data.state===true){
                        layer.msg(data.message,{end:function () {
                            window.location.href=location.href;
                        }});
                    }else {
                        button.removeClass('disabled');
                        layer.msg(data.message);
                    }
                }
            });

        }
    });


    /**
     *  地区N级联动
     */
    $("select.link").change(function () {
        var curr = parseInt($(this).attr('link')),
            child=parseInt($(this).attr('link'))+1,
            parentId=$(this).val();
        if(!parentId){return false;}

        if(curr=='1'){
            //第一次带出手机短号
            var val=$(this).find(":selected").val().split('_'),
                mobile_first=$("select[name='mobileCode']");
            if(val[2]&&val[2]!=''&&mobile_first.val()==''){
                mobile_first.val(val[2]);
            }
        }
        $.ajax({
            url:city_url,
            data:{parentId:$(this).val()},
            dataType:'json',
            success:function (data) {
                sysException(data);
                if(!data.length){
                    $("#link"+curr).parent().nextAll().css('display','none');
                }else{
                    $("#link"+curr).parent().next().css('display','');
                }
                var html='<option value="">请选择</option>';
                $.each(data,function (k,v){
                    html += '<option value="'+ v.country_id+'">'+v.country_name+'</option>';
                });
                $("#link"+child).html(html);
            },
            error:function (data) {
                console.log(data);
            }
        });
        //清除级列表
        for(var i=1;i<=4;i++){
            if(child<i){
                $('#link'+i).html('<option value="">请选择</option>').parent().css('display','none');
            }
        }
    });

});