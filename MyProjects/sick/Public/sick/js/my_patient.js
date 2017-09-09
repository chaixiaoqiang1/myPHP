/**
 * Created by Administrator on 2016/6/15.
 */
$(function () {
    $(".add").click(function () {
        $(".window").show();
        $(".pop").show();
      /*  $(".consult").after(' <div class="parent_container"><div class="panel-title aaa fl"> <div class="down_img lf"> <img src="__PUBLIC__/sick/img/right_img.png" class="img-responsive cut_way" alt=""/> </div> <div class="lf sufferer">咨询</div> <div class="number rt">1</div> </div> <div class="ensconce fl"> <a href="Patient_name_card.html"> <div class="user_name lf"> <img src="__PUBLIC__/sick/img/doctor.jpg" class="img-responsive img-circle" alt=""/> </div> <div class="user_info lf">王大锤</div> </a> </div> </div>')*/
    })
    var height = document.body.clientHeight;
    var width = document.body.clientWidth;
    $('.window').css('width', width);
    $('.window').css('height', height);


    //弹框隐藏显示
    $(".window").click(function () {
        $(this).hide();
        $(".pop").hide()
    })
    $(".confirm").click(function () {
        $(".pop").hide();
        $(".window").hide()
    })
    $(".cancel").click(function () {
        $(".pop").hide();
        $(".window").hide()
    })
    $(".panel-title").click(function () {

        if ($(this).siblings().children().css('display') == 'none') {
            $('.ensconce').hide();
            $(this).siblings().children().css('display', 'block');
        } else {
            $('.ensconce').hide();
        }
    })

    $(".seach_left").click(function () {
        $(".seach_tx").focus();
    })
})

