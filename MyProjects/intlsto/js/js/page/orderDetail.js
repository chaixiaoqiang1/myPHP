/**
 * 包裹详情
 */
$(function () {
    $.ajax({
        url:getScanList_url,
        data:{stoNo:$("input[name='stoNo']").val()},
        dataType:'json',
        success:function (data) {
            if(data.length>0){
                var html='<tr><td>状态</td><td>时间</td></tr>';
                $.each(data,function (k,v) {
                    html+='<tr><td>'+v.Memo+'</td><td>'+v.ScanDate+'</td></tr>'
                });
                $(".load").after(html).remove();
            }else {
                $(".load>td").text("暂无物流信息");
            }
        }
    })
});