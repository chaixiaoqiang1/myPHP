/**
 * 批量导入
 */
$(function () {

    /*tab切换*/
    $('.panel-header span').on('click',function(){
        $(this).addClass('active').siblings().removeClass('active');
        $('.panel .panel-body').eq($(this).index()).addClass('panel_body_block').siblings().removeClass('panel_body_block');
    });

    //根据模板隐藏框及改变下载模板链接
    $("select[name='platform']").on('change',function () {
        var templet=$(this).val(),  //模板代号
            templet_name=$(this).children(':selected').text(),//模板名称
            templet_url=$(this).children(':selected').attr('templet');//模板下载地址

        $(".c-primary").prop('href',templet_url).text("下载"+templet_name+"模板");
        if(templet!=='sto'){
            $(".stoTemplet").hide();
        }else {
            $(".stoTemplet").show();
        }
    });

    //验证文件类型
    jQuery.validator.addMethod("checkExcel", function(value, element) {
        //获得上传文件名
        var fileArr=value.split("\\");
        var fileTArr=fileArr[fileArr.length-1].toLowerCase().split(".");
        var filetype=fileTArr[fileTArr.length-1];
        //切割出后缀文件名
        if(filetype != "xls"&&filetype != "xlsx"){
            return false;
        }else{
            return true;
        }
    }, "请上传xls或者xlsx格式的Excel");

    //增加验证规则，地址可见必选
    $.validator.addMethod('seeNeed',function(value,element){
        return value.length>0||!$(element).is(":visible");
    },"请选择地址");

    $("#form-batch-import").validate({
        rules:{
            templet:{
                required:true,
            },
            consolidator_no:{
                seeNeed:true,
            },
            shipperCountry:{
                seeNeed:true,
            },
            destination:{
                seeNeed:true,
            },
            line:{
                seeNeed:true,
            },
            uploadfile:{
                required:true,
                checkExcel:true
            },
        },
        messages:{
            templet:{
                required:"请选择导入模板"
            },
            consolidator_no:{
                seeNeed:"请选择海外收货仓库",
            },
            shipperCountry:{
                seeNeed:"请选择发货国家",
            },
            destination:{
                seeNeed:"请选择目的地",
            },
            line:{
                seeNeed:"请选择产品线路",
            },
            uploadfile:{
                required:"请选择包裹文件",
            },
        },
        onkeyup:false,
        success:"valid",
        errorElement:'div',
        errorPlacement:function(error,element) {
            error.appendTo(element.parents(".formControls"));
        },
        submitHandler:function(form){
            var button=$('#form-batch-import').find('button');
            button.addClass('disabled');
            $(form).ajaxSubmit({
                type: 'post',
                dataType:'json',
                uploadProgress: showLoad,
                success: function(data) {
                    sysException(data);
                    button.removeClass('disabled');
                    $(".file-item").val('');$('.load').hide();
                    if(data.state == 2){
                        $('.load p').eq(1).removeClass('c-orange').addClass('c-red').text('导入数据错误');
                        var mes_error = '';
                        $.each(data.message,function(i,n){
                            mes_error+= n+"<br/>";
                        });
                        layer.open({
                            title: '导入数据格式错误',
                            type:1,
                            maxmin:true,
                            area:['300px','200px'],
                            content: '<span class="f-14">'+mes_error+'</span>',
                        });
                    }else if(data.state == 1){
                        $('.load p').eq(1).removeClass('c-orange').addClass('c-success').text('系统处理完成');
                        layer.confirm(data.message, {
                            btn: ['继续导入','跳转包裹列表'] //按钮
                        }, function(){
                            layer.closeAll();
                        }, function(){
                            window.location.href = data.url;
                        });
                    }else {
                        $('.load p').eq(1).removeClass('c-orange').addClass('c-red').text('系统处理失败');
                        layer.open({
                            title: '数据处理错误',
                            type:1,
                            area:['300px','200px'],
                            maxmin:true,
                            content: '<span class="f-14">'+data.message+'</span>',
                        });
                    }
                }
            });
        }
    });

    $("select[name='consolidator_no']").on('change',function () {
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

    $("select[name='destination']").on('change',function () {
        var destination=$(this).val(),
            consolidator=$("select[name='consolidator_no']").val().split("_");
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


/**
 * 提交数据进度条展示
 * @param event
 * @param position
 * @param total
 * @param percent
 */
function showLoad(event,position,total,percent) {
    var load=$('.load');
    if(!load.is(':visible'))load.show();
    load.find('p').eq(0).text(bytesToSize(position)+'/'+bytesToSize(total));
    $('.progress-bar>.sr-only').width(percent+'%');
    if(percent===100){
        load.find('p').eq(1).removeClass('c-green').addClass('c-orange').html('系统正在处理中<img src="'+loadImg+'">');
    }
}

/**
 * 大小转换
 * @param bytes
 * @returns {*}
 */
function bytesToSize(bytes) {
    if (bytes === 0) return '0 B';
    var k = 1024, // or 1000
        sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}