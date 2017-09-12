//上传身份证弹窗页面
$(function () {

    var frontOfId=$("input[name='frontOfId']",parent.document).val(),
        backOfId=$("input[name='backOfId']",parent.document).val();
    if(frontOfId!=''){
        $(".Positive img").prop('src',frontOfId);
    }
    if(backOfId!=''){
        $(".opposite img").prop('src',backOfId);
    }

    /**
     * 上传文件输入框改变事件
     */
    $("input[name='cardFile']").on('change',function (e) {

        var filemaxsize = 1024,//1M
            target = $(e.target),
            Size = target[0].files[0].size / 1024;
        if(Size > filemaxsize) {
            layer.msg('图片大于1M，请重新选择!');
            return false;
        }
        if(!this.files[0].type.match(/image.*/)) {
            layer.msg('请选择正确的图片!')
        } else {
            var cardType=$(this).attr('cardType');
            var name=cardType=='frontOfId'?"上传身份证正面":"上传身份证反面";
            runImg(this,function (data) {
                $("input[name='"+cardType+"']",parent.document).val(data);
            });

            /*var file=$("input[name='"+cardType+"']",parent.document).attr('files');
            console.log(file);*/
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
            parent.layer.open({
                type: 2,
                title:name,
                shadeClose: true,
                resize:false,
                shade: 0.5,
                area: ['800px', '500px'], //宽高
                content: reSizeImage_url+'?cardType='+cardType,
            });
        }


    });

    /**
     * 身份证上传提交
     */
    $(".btn-warning").on('click',function () {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    });

    /**
     * 身份证上传取消
     */
    $(".btn-default").on('click',function () {
        frontOfId=$("input[name='frontOfId']",parent.document).val('');
        backOfId=$("input[name='backOfId']",parent.document).val('');
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    })
});