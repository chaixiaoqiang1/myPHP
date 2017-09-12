/**
 * 退货服务
 */
$(function(){
    //删除图片
    $('body').on('click',function (e) {
        if (e.target.className==='btn_close'){
            $(e.target).parent('.b_eee').remove();
        }
    });

    $("#form-return").validate({
        rules:{
            remark:{
                required:true
            },
            uploadFile:{
                required:true
            }
        },
        messages: {
            remark:{
                required:'请输入退货原因'
            },
            uploadFile: {
                required: '请上传单据图片'
            }
        },
        success:"valid",
        errorElement:'div',
        onkeyup:false,
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(form){
            $(form).ajaxSubmit({
                url:returnPackage,
                type: 'post',
                dataType:'json',
                beforeSend:function(){
                    loading();
                },
                success:function(re){
                    sysException(re);
                    if(re.state == 1){
                        layer.msg(re.message,{end:function () {
                            window.location.href=history.go('-1');
                        }});
                    }else{
                        layer.msg(re.message);
                    }
                }
            });
        }
    });

    /**
     * 图片选择展示及修改隐藏域
     */
    $("#upImg").change(function () {
        //判断不大于四张图片
        var imgLength=$("input[name='img[]']").length;
        if(imgLength>=3){
            layer.msg("最多上传3张图片");
            return false;
        }
        runImg(this, function (data) {
            var html='<div class="b_eee upimg"><img src="'+data+'" /><i class="btn_close"></i>';
            html+='<input type="hidden" name="img[]" value="'+data+'"></div>';
            $('#imgList').append(html);
        });
    });

    /*返回页面*/
    $("#return").click(function(){
        window.location.href = history.go(-1);
    })
})