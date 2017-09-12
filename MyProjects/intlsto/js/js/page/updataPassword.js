/*
 *项目名称：国际业务系统框架
 *调用页面：密码管理-updataPassword.html
 *开发人员：344822559@qq.com
 *开发日期：2017-02-13
 * */

$(function(){
	
	/*tab切换*/
	$('.panel-header span').on('click',function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('.panel .panel-body').eq($(this).index()).addClass('panel_body_block').siblings().removeClass('panel_body_block');
	})
	
});
