/*
 *项目名称：国际业务系统框架
 *开发人员：344822559@qq.com
 *开发日期：2016-11-30
 * 
 * */

$(function(){
    //判断当前页是否在iframe之中,在，则从新打开当前页面
    if (self.frameElement&&self.frameElement.tagName === "IFRAME") {
        top.location.href=web_root_url;
    }

	$('#iframe').height($(window).height()-70-53+'px');
	
	/*左侧导航栏高度自适应*/
	$('.left').css('height',$(window.document).height()+'px');
	$('.left_box').css('height',$(window.document).height()-150+'px');
	
	/*框架左侧导航点击事件*/
	$('.left .nav p').on('click',function(){
		var hasActive=$(this).find('a').hasClass('active');
		if(!hasActive){
			$(this).addClass('active');
			//只关闭当前页面下的关闭其他展开项[CoverBySrako]
			$(this).parents('.nav_box').find('.nav>p>a').removeClass('active');
            $(this).parents('.nav_box').find('.nav>ul>a').removeClass('active');
            $(this).parents('.nav_box').find('.nav ul').slideUp();
			var a=$(this).find('a').addClass('active');
			$(this).next('ul').slideDown();
		}else{
            $(this).removeClass('active');
            var a = $(this).find('a').removeClass('active');
            $(this).next('ul').slideUp();
        }
		
	});
	
	/*左侧菜单点击变色*/
	$('.nav ul a').on('click',function(){
		$(this).addClass("active").siblings().removeClass('active');
		var IframeUrl=$(this).attr('_href');
		$('#iframe').attr('src',IframeUrl);
	});
	
	/*iframe高度自适应*/
	$(window).resize(function(){
		 $('#iframe').height($(window).height()-70-53+'px');
		 $('.left').css('height',$(window.document).height()+'px');
		 $('.left_box').css('height',$(window.document).height()-150+'px');

	});
	
	
	/*框架顶部导航点击事件*/
	$('.right_top .top_nav a').on('click',function(){
		var welHref=$(this).attr('title');
		if(welHref!='undefined'){

			var Index=$(this).index();
			$(this).addClass('active').siblings().removeClass('active');
			$('.right_top .top_index').removeClass('active');
			$('.left .nav_box').eq(Index).addClass('nav_box_block').siblings().removeClass('nav_box_block');

			$('#iframe').attr('src',welHref);
			
		}else{
			var Index=$(this).index();
			$(this).addClass('active').siblings().removeClass('active');
			$('.right_top .top_index').removeClass('active');
			$('.left .nav_box').eq(Index).addClass('nav_box_block').siblings().removeClass('nav_box_block');	
		}
		
		return false;
		
	});
	
	/**
	 * 框架首页点击事件*/
	$('.right_top .top_index').on('click',function(){
		$(this).addClass('active');
		$('#iframe').attr('src',$(this).attr('title'));
		$('.right_top .top_nav a').siblings().removeClass('active');
	});

	
	/**
	 * 右侧顶部用户信息点击事件
	 * */
	$('.user_set .user_box').on('click',function(){
		var width=$(this).width()+86+'px';
		$('.user_set .user_box .user_nav').width(width).slideToggle() ;
	});

    $('.user_set .ring').on('click',function(){
        $(this).find('.ring_updown').slideToggle();
        event.stopPropagation();
    });

	//首页编辑资料点击,消息点击进入投诉建议列表
	$("#updateUser,.ring a").click(function () {
        $('#iframe').attr('src',$(this).attr('_href'));
    });


	//首页修改密码点击事件
	$("#changePass").click(function () {
        layer.open({
            type: 2,
            title:"修改密码",
            area: ['750px', '500px'], //宽高
            content: changePass_url
        });
    })
});

/**
 * 刷新消息数量
 */
function refreshNotice() {
    $.ajax({
        url:notice_url,
        success:function (data) {
            if(data.state){
            	if(data.data.allNumber>=1){
                    top.$('.allNumber').html('<div class="ring_num">'+data.data.allNumber+'</div>');
				}else {
                    top.$('.allNumber').html('');
				}
                top.$('.taxNumber').html(data.data.taxNumberT);
                top.$('.taxQueryNumber').html(data.data.taxQueryNumberT);
                top.$('.adviceComplaintNumber').html(data.data.adviceComplaintNumberT);
                top.$('.claimNumber').html(data.data.claimNumberT);
            }
            data=null;
        }
    })
}
