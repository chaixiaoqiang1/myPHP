/**
 * 地址管理
 * Created by Srako on 2017/05/16.
 */
$(function(){
    var status = $("#status").val();
    var destination = $("#destination").val();
    var url = selectAddressList+"?status="+status+"&destination="+destination;
    $('#table').bootstrapTable({
        url: url,
        striped: true,
        height: 323,
        showRefresh: true, //会出现刷新按钮
        search: true,
        clickToSelect: true,
        pageSize: 5,
        pageList: [5, 10, 20, 25],
        pagination:true,
        showPaginationSwitch:false,
        searchAlign: "left", //指定搜索框水平方向的位置
        buttonsAlign: "right",
        paginationDetail:false,
        /*操作按钮在水平方向的位置*/
        paginationHAlign: "left",
        /*分页条在水平方向的位置*/
        paginationDetailHAlign: "right",
        /*分页详细信息在水平方向的位置*/
        paginationPreText: "上一页",
        /*指定上一页的内容*/
        paginationNextText: "下一页",
        columns: [
            {
                title: '',
                align: 'center',
                width: 50,
                formatter:function (value,row,index) {
                    return '<input type="radio" data-index="'+index+'" value="'+row.id+'" name="btSelectItem" />';
                }
            },
            {
                title: '序号',
                align: 'center',
                width: 20,
                formatter:function (value,row,index) {
                    return index+1;
                }
            },
            {
                title: '收货人',
                field: 'name',
                align: 'center',
                width: 130,
            },
            {
                title: '联系电话',
                field: 'phone',
                align: 'center',
                width: 170,
            },
            {
                title: '收货地址',
                field: 'address',
                align: 'center',
            },
            {
                title: '操作',
                align: 'center',
                width: 80,
                formatter:function(value,row,index){
                    var html = '';
                    html += '<a class="btn btn-primary radius size-MINI" href="javascript:void(0);" onclick="updateAddress(\''+row.id+'\',\''+row.country+'\')">修改</a>';
                    return html;
                }
            }
        ],
        //RowStyle:addClass,//设置自定义行属性 参数为：row: 行数据index: 行下标 返回值可以为class或者css 支持所有自定义属性
        formatShowingRows: function (pageFrom, pageTo, totalRows) {
            return '总共 ' + totalRows + ' 条记录';
        }
    });


    /*根据选中的id查询一条数据*/
    $("#determine").click(function(){

        var status = $("#status").val(), //1为收货地址2为发货地址
            id = $("input[name='btSelectItem']:checked").val();
        if(id == '' || id == null){
            layer.msg("请选择地址");
            return false;
        }
        $.ajax({
            type:'post',
            url:addressUrl,
            data:{id:id,status:status},
            dataType:'json',
            beforeSend:function(){
                loading();
            },
            success:function (data) {
                sysException(data);
                var postcode = data['message']['postcode'] == null ? '' : data['message']['postcode'], //邮编
                    country = data['message']['country'] == null ? '' : data['message']['country'],
                    province = data['message']['province'] == null ? '' : data['message']['province'],
                    city = data['message']['city'] == null ? '' : data['message']['city'],
                    area = data['message']['area'] == null ? '' : data['message']['area'],
                    town = data['message']['town'] == null ? '' : data['message']['town'],
                    address = data['message']['address'] == null ? '' : data['message']['address'];
                var No = $("#pack").val();
                if(data.state == 1){ //收货地址
                    var name = data['message']['deliveryName'] == null ? '': data['message']['deliveryName'],
                        mobile = data['message']['deliveryMobile'] == null ? '': data['message']['deliveryMobile'];
                    $("#deliveryName"+No,parent.document).text(name);
                    $("#deliveryMobile"+No,parent.document).text(mobile);
                    $("#deliveryCountry"+No,parent.document).text(country);
                    $("#deliveryProvince"+No,parent.document).text(province);
                    $("#deliveryCity"+No,parent.document).text(city);
                    $("#deliveryArea"+No,parent.document).text(area);
                    $("#deliveryTown"+No,parent.document).text(town);
                    $("#deliveryAddress"+No,parent.document).text(address);
                    $("#deliveryPostCode"+No,parent.document).text(postcode);
                    $("#deliveryAddressId"+No,parent.document).val(data['message']['deliveryAddressId']);
                    $("#certificateNum0", parent.document).html('');
                    if(data.message.countryCode == 'CHN') {
                        $("#certificateNum0", parent.document).html(data.message.certificateNum + '&nbsp;&nbsp;&nbsp;&nbsp;' + data.verification); //身份证
                    }
                }else if(data.state == 2){ //发货地址
                    var type = $("#type").val();
                    var name = data['message']['senderName'] == null ? '': data['message']['senderName'],//姓名
                        mobile = data['message']['senderMobile'] == null ? '': data['message']['senderMobile'];  //电话
                    if(type == 'sender'){
                        var serialNumber = $("#serialNumber",parent.document).attr("serialNumber");
                        for(var i = 1 ; i <= serialNumber ; i++){
                            $("#senderName"+i,parent.document).text(name);
                            $("#senderMobile"+i,parent.document).text(mobile);
                            $("#senderCountry"+i,parent.document).text(country);
                            $("#senderProvince"+i,parent.document).text(province);
                            $("#senderCity"+i,parent.document).text(city);
                            $("#senderArea"+i,parent.document).text(area);
                            $("#senderTown"+i,parent.document).text(town);
                            $("#senderAddress"+i,parent.document).text(address);
                            $("#senderPostCode"+i,parent.document).text(postcode);
                            $("#senderAddressId1",parent.document).val(data['message']['senderAddressId']);
                        }
                    }else{
                        $("#senderName0",parent.document).text(name);
                        $("#senderMobile0",parent.document).text(mobile);
                        $("#senderCountry0",parent.document).text(country);
                        $("#senderProvince0",parent.document).text(province);
                        $("#senderCity0",parent.document).text(city);
                        $("#senderArea0",parent.document).text(area);
                        $("#senderTown0",parent.document).text(town);
                        $("#senderAddress0",parent.document).text(address);
                        $("#senderPostCode0",parent.document).text(postcode);
                        $("#senderAddressId0",parent.document).val(data['message']['senderAddressId']);
                    }
                }
                $("#status",parent.document).val('1'); //关闭窗体必须为1
                var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                parent.layer.close(index);//关闭layer.open窗体
            }
        })
    });


    $('#manager_address').on('click',function(){
        $("#status",parent.document).val("2");
        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
        parent.layer.close(index);//关闭layer.open窗体
    });


});

/**
 * 修改地址
 * @param id 当前id
 */
function updateAddress(id,country){
    $("#status",parent.document).val("3");
    $("#status",parent.document).attr("addressId",id+'_'+country);
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);//关闭layer.open窗体
}
