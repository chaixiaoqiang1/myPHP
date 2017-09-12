/**
 * 用户地址管理
 * Created by Srako on 2017/05/11.
 */
$(function () {
    //如果展示为1，切换投诉为选中状态
    if(active==='1'){
        $(".panel-header span").eq(1).addClass('active').siblings().removeClass('active').attr('see',1);
        $('.panel .panel-body').eq(1).addClass('panel_body_block').siblings().removeClass('panel_body_block');
        loadSend();
    }


    /**
     * tab切换
     * */
    $('.panel-header span').on('click',function(){
        $(this).addClass('active').siblings().removeClass('active');
        $('.panel .panel-body').eq($(this).index()).addClass('panel_body_block').siblings().removeClass('panel_body_block');
        if($(this).attr('see')==='0'){
            loadSend();
            $(this).attr('see',1);
        }
    });


    /**
     * 收货地址N级联动
     */
    $("select.link").change(function () {
        var curr=parseInt($(this).attr('link')),
            child=parseInt($(this).attr('link'))+1,
            parentId=$(this).val();
        if(!parentId){return false;}
        if(curr=='1'){
            //验证是否为中国
            var val=$(this).find(":selected").val().split('_'),
                mobile_first=$(this).parents('form').find("select[name='mobile_first']");
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
            data:{parentId:$(this).val()},
            dataType:'json',
            success:function (data,status) {
                sysException(data);
                if(!data.length){
                    $("#link"+curr).parent().nextAll().addClass('hide');
                }else{
                    $("#link"+curr).parent().next().removeClass('hide');
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
                $('#link'+i).html('<option value="">请选择</option>').parent().addClass('hide');
            }
        }
    });

    /**
     * 发货地址N级联动
     */
    $("select.senderLink").change(function () {
        var curr=parseInt($(this).attr('link')),
            child=parseInt($(this).attr('link'))+1,
            parentId=$(this).val();
        if(!parentId){return false;}
        if(curr=='1'){
            //第一带出手机短号
            var val=$(this).find(":selected").val().split('_'),
                mobile_first=$(this).parents('form').find("select[name='mobile_first']");
            if(val[3]&&val[3]!=''&&mobile_first.val()==''){
                mobile_first.val(val[3]);
            }
        }
        $.ajax({
            url:city_url,
            data:{parentId:$(this).val()},
            dataType:'json',
            success:function (data,status) {
                sysException(data);
                if(!data.length){
                    $("#senderLink"+curr).parent().nextAll().addClass('hide');
                }else{
                    $("#senderLink"+curr).parent().next().removeClass('hide');
                }

                var html='<option value="">请选择</option>';
                $.each(data,function (k,v){
                    html+='<option value="'+v.country_id+'_'+v.country_name+'">'+v.country_name+'</option>';
                });
                $("#senderLink"+child).html(html);
            },
            error:function (data) {
                console.log(data.responseText);
            }
        });
        //清除级列表
        for(var i=1;i<=5;i++){
            if(child<i){
                $('#senderLink'+i).html('<option value="">请选择</option>').parent().addClass('hide');
            }
        }
    });


    //增加验证规则，地址可见必选
    $.validator.addMethod('seeNeed',function(value,element){
        return value.length>0||!$(element).is(":visible");
    },"请选择地址");

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
        if($("select[name='certificate_type']").val().length>0){
            return true;
        }else {
            $(element).val('');
            return false;
        }
    },"请先选择证件类型");

    /**
     * 添加收货地址
     */
    $("#receiveAdd").validate({
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
                maxlength:10
            },
            name:{
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
            name:{
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
        submitHandler:function(){
            //防止重复提交
            var forms    =   $("#receiveAdd"),
                button  =   forms.find("button[type='submit']"),
                param   =   forms.serialize();
            button.addClass('disabled');
            loading();
            $.ajax({
                url:add_receive_url,
                dateType:'json',
                data:param,
                type: 'post',
                success:function(data) {
                    sysException(data);
                    button.removeClass('disabled');
                    if(data.state){
                        layer.msg(data.message,{end:function () {
                            document.getElementById('receiveAdd').reset();
                            $('.cardNotice,.certificate').hide();
                            $('#receiveTable').bootstrapTable('refresh');
                        }});
                    }else{
                        layer.msg(data.message );
                    }
                }
            });

        }
    });


    /**
     * 添加发货地址
     */
    $("#senderAdd").validate({
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
                maxlength:10
            },
            name:{
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
            name:{
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
        submitHandler:function(){
            //防止重复提交
            var forms    =   $("#senderAdd"),
                button  =   forms.find("button[type='submit']"),
                param   =   forms.serialize();
            button.addClass('disabled');
            loading();
            $.ajax({
                url:add_sender_url,
                dateType:'json',
                data:param,
                type: 'post',
                success:function(data) {
                    sysException(data);
                    button.removeClass('disabled');
                    if(data.state){
                        layer.msg(data.message,{end:function () {
                            document.getElementById('senderAdd').reset();
                            $('#senderTable').bootstrapTable('refresh');
                        }});
                    }else{
                        layer.msg(data.message );
                    }
                }
            });

        }
    });




    /**
     * 收货地址列表加载
     */
    $('#receiveTable').bootstrapTable({
        url: table_receive_url,
        method: 'get',
        striped: true,
        // height: 550,
        showRefresh: true, //会出现刷新按钮
        search: true,
        clickToSelect: true,
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        undefinedText:'',
        searchAlign: "left", //指定搜索框水平方向的位置
        buttonsAlign: "right",
        /*操作按钮在水平方向的位置*/
        paginationHAlign: "left",
        /*分页条在水平方向的位置*/
        paginationDetailHAlign: "right",
        /*分页详细信息在水平方向的位置*/
        paginationPreText: "上一页",
        /*指定上一页的内容*/
        paginationNextText: "下一页",
        responseHandler:function (res) {sysException(res);return res;},
        //RowStyle:addClass,//设置自定义行属性 参数为：row: 行数据index: 行下标 返回值可以为class或者css 支持所有自定义属性
        detailView: false,
        sidePagination: 'server',
        columns: [
            {
                title: '收货人',
                align: 'center',
                field: 'deliveryName',
            },
            {
                title: '所在地区',
                align: 'center',
                formatter:function (value,row,index) {
                    var str='';
                    str+=row.country?row.country:'';
                    str+='&nbsp;';
                    str+=row.province?row.province:'';
                    str+='&nbsp;';
                    str+=row.city?row.city:'';
                    str+='&nbsp;';
                    str+=row.area?row.area:'';
                    str+='&nbsp;';
                    str+=row.town?row.town:'';
                    return str;
                },
                width: 200,
            },
            {
                title: '详细地址',
                field: 'address',
                align: 'center',
            },
            {
                title: '邮政编码',
                field: 'postcode',
                align: 'center',
                width: 100,
            },
            {
                title: '联系电话',
                field: 'deliveryMobile',
                align: 'center',
                width: 200
            },
            {
                title: '证件状态',
                field: 'message',
                align: 'center',
                width: 200
            },
            {
                title: '操作',
                field: 'do',
                align: 'center',
                width: 200,
                formatter:function(value,row){
                    var str='<button class="btn btn-danger radius size-MINI" onclick="del_receive(\''+row.deliveryAddressId+'\')" >删除</button>';
                    str+='&nbsp;&nbsp;<a class="btn btn-primary radius size-MINI" href="'+edit_receive_url+'?id='+row.deliveryAddressId+'">修改</a>';
                    if(!row.isDefault){
                        str+='&nbsp;&nbsp;<a class="btn btn-default size-MINI" href="javascript:void(0);" onclick="def_address(\''+row.deliveryAddressId+'\',\'receive\')" >设为默认</a>';
                    }else {
                        str+='&nbsp;&nbsp;<a class="btn btn-success size-MINI" href="javascript:void(0);" >默认地址</a>';
                    }
                    return str;
                }
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                search:params.searchText,
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
            };
        }
    });


});

//加载发货地址列表
function loadSend() {
    /**
     * 发货地址列表加载
     */
    $('#senderTable').bootstrapTable({
        url: table_sender_url,
        method: 'get',
        striped: true,
        // height: 550,
        showRefresh: true, //会出现刷新按钮
        search: true,
        clickToSelect: true,
        pageSize: 10,
        pageList: [5, 10, 20, 25],
        undefinedText:'',
        searchAlign: "left", //指定搜索框水平方向的位置
        buttonsAlign: "right",
        /*操作按钮在水平方向的位置*/
        paginationHAlign: "left",
        /*分页条在水平方向的位置*/
        paginationDetailHAlign: "right",
        /*分页详细信息在水平方向的位置*/
        paginationPreText: "上一页",
        /*指定上一页的内容*/
        paginationNextText: "下一页",
        responseHandler:function (res) {sysException(res);return res;},
        //RowStyle:addClass,//设置自定义行属性 参数为：row: 行数据index: 行下标 返回值可以为class或者css 支持所有自定义属性
        detailView: false,
        sidePagination: 'server',
        columns: [
            {
                title: '发货人',
                align: 'center',
                field: 'senderName',
            },
            {
                title: '所在地区',
                align: 'center',
                formatter:function (value,row) {
                    var str='';
                    str+=row.country?row.country:'';
                    str+='&nbsp;';
                    str+=row.province?row.province:'';
                    str+='&nbsp;';
                    str+=row.city?row.city:'';
                    str+='&nbsp;';
                    str+=row.area?row.area:'';
                    str+='&nbsp;';
                    str+=row.town?row.town:'';
                    return str;
                },
                width: 200,
            },
            {
                title: '详细地址',
                field: 'address',
                align: 'center',
            },
            {
                title: '邮政编码',
                field: 'postcode',
                align: 'center',
                width: 100,
            },
            {
                title: '联系电话',
                field: 'senderMobile',
                align: 'center',
                width: 200
            },
            {
                title: '操作',
                field: 'do',
                align: 'center',
                width: 200,
                formatter:function(value,row,index){
                    var str='<button class="btn btn-danger radius size-MINI" onclick="del_sender(\''+row.senderAddressId+'\')" >删除</button>';
                    str+='&nbsp;&nbsp;<a class="btn btn-primary radius size-MINI" href="'+edit_sender_url+'?id='+row.senderAddressId+'">修改</a>';
                    if(!row.isDefault){
                        str+='&nbsp;&nbsp;<a class="btn btn-default size-MINI" onclick="def_address(\''+row.senderAddressId+'\',\'sender\')" >设为默认</a>';
                    }else {
                        str+='&nbsp;&nbsp;<a class="btn btn-success size-MINI" >默认地址</a>';
                    }
                    return str;
                }
            }
        ],
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数
            return {
                search:params.searchText,
                pageNum: params.pageNumber,
                pageSize: params.pageSize,
            };
        }
    });
}
/**
 * 删除收货地址
 * @param deliveryAddressId
 */
function del_receive(deliveryAddressId) {
    layer.confirm("确认删除收货地址？",function () {
        $.ajax({
            url:del_receive_url,
            type:'post',
            data:{id:deliveryAddressId},
            success:function (data) {
                sysException(data);
                if(data.state){
                    //成功刷新列表
                    layer.msg(data.message,{end:function () {
                        $('#receiveTable').bootstrapTable('refresh');
                    }})
                }else {
                    layer.msg(data.message);
                }
            }
        })
    })
}


/**
 * 删除发货地址
 * @param senderAddressId
 */
function del_sender(senderAddressId) {
    layer.confirm("确认删除发货地址？",function () {
        $.ajax({
            url:del_sender_url,
            type:'post',
            data:{id:senderAddressId},
            success:function (data) {
                sysException(data);
                if(data.state){
                    //成功刷新列表
                    layer.msg(data.message,{end:function () {
                        $('#senderTable').bootstrapTable('refresh');
                    }})
                }else {
                    layer.msg(data.message);
                }
            }
        })
    })
}

/**
 * 设为默认地址
 * @param id  地址id
 * @param type  地址类型 receive收货，sender发货
 */
function def_address(id,type) {
    layer.confirm("是否设为默认地址",function () {
        $.ajax({
            url:defaul_url,
            type:'get',
            data:{id:id,type:type},
            success:function (data) {
                sysException(data);
                if(data.state){
                    layer.msg(data.message,{end:function () {
                        $('#'+type+'Table').bootstrapTable('refresh');
                    }})
                }else {
                    layer.msg(data.message);
                }
            }
        })
    })
}

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