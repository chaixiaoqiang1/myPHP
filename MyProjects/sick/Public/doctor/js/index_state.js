/**
 * Created by Administrator on 2016/6/5.
 */
$(function () {
    $(".sort_ct").click(function () {
        $(this).siblings().css("background", "#e9e9e9")
        $(this).siblings().css("color", "#888")
        $(this).css("background", "#3DBB28")
        $(this).css("color", "#fff")
    })
    $(".left").click(function(){
        $(".more_content").css("background","#e9e9e9");
        $(".more_content").css("color","#888")
    })

    if ($(window).height() == "480") {
        //  alert("Xxx")
        $(" .classfiy_ct").css("height", "444px");
        $(" .classfiy_ct").css("overflow-y", "auto");
        $(" .classfiy_right").css("height", "430px");
        $(" .classfiy_right").css("overflow-y", "auto")
    }
    if ($(window).height() == "567") {
        $(" .classfiy_ct").css("height", "530px");
        $(" .classfiy_ct").css("overflow-y", "auto");
        $(" .classfiy_right").css("height", "520px");
        $(" .classfiy_right").css("overflow-y", "auto")
    }


    var height = document.body.clientHeight;
    var width = document.body.clientWidth;
    $('.window').css('width', width);
    $('.window').css('height', height);
    $(".sort_content3").click(function () {
        $(".window").show();
        $(".more").show()
        $(".more").animate({left: "29%"}, "slow")
    })
    $(".window").click(function () {
        $(this).hide()
        $(".more").animate({left: "100%"})
        $(".more").hide()
    })
    $(".init").click(function () {
        $(this).siblings().css("background", "#ebeceb")
        $(this).siblings().css("color", "#666")
        $(this).css("background", "#3cbb28")
        $(this).css("color", "#fff")
    })
    $(".a:last").css("padding-bottom", "30px")
/*    $(".classfiy_ct li").click(function () {
        $(this).siblings().css("background-image", "url()")
        $(this).siblings().css("background", "#e9e9e9")
        $(this).siblings().css("color", "#666")
        $(this).css("background-image", "url(../img/bj.png)")
        $(this).css("background-size", "100% 100%")
        $(this).css("color", "#fff")
    })*/
   /* $(".names").click(function () {
        var id=$(this).attr('id');
        alert(id);

      //  $(".names").css("background", "url()")
      /!*  $(this).siblings().css("background", "#e9e9e9")
        $(this).siblings().css("color", "#666")*!/
        $('#'+id).css("background-image", "url(../img/bj.png)")
        $('#'+id).css("background-size", "100% 100%")
        $('#'+id).css("color", "#fff")
    })*/
})
