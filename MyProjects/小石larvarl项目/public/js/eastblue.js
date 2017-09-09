	(function($) {
		"use strict";
		/*global jQuery*/
		$.fn.tree = function() {
			return this.each(function() {
					var btn = $(this).children("a").first();
					var menu = $(this).children(".treeview-menu").first();
					var isActive = $(this).hasClass('active');
					//initialize already active menus
					if (isActive) {
						menu.show();
						btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
					}
					//Slide open or close the menu on link click
					btn.click(function(e) {
							e.preventDefault();
							if (isActive) {
								//Slide up to close menu
								menu.slideUp();
								isActive = false;
								btn.children(".fa-angle-down").first().removeClass("fa-angle-down").addClass("fa-angle-left");
								btn.parent("li").removeClass("active");
							} else {
								//Slide down to open menu
								menu.slideDown();
								isActive = true;
								btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
								btn.parent("li").addClass("active");
							}
						});
					/* Add margins to submenu elements to give it a tree look */
					menu.find("li > a").each(function() {
							var pad = parseInt($(this).css("margin-left"), 10) + 10;
							$(this).css({"margin-left": pad + "px"});
						});
				});
		};
	}(jQuery)); 
	
	
	/*global $, alert*/
	$(function() {
		$(".sidebar .treeview").tree();	
		$('.sidebar-toggle').click(function(){
			if (!($('.right-side').hasClass('eb-right-side-offset'))) {
				$('.right-side').addClass('eb-right-side-offset');
				$('.left-side').toggle('fast', function(){
				});	
			}else{
				$('.left-side').toggle('fast', function(){
					if (!($('.right-side').hasClass('eb-right-side-offset'))) {
						$('.right-side').addClass('eb-right-side-offset');
					} else {
						$('.right-side').removeClass('eb-right-side-offset');
					}
				});					
			}
		});

		if (navigator.userAgent.match(/Android/i) || 
			navigator.userAgent.match(/mobile/i) || 
			navigator.userAgent.match(/BlackBerry/i) || 
			navigator.userAgent.match(/iPhone|iPad|iPod/i) ||
			navigator.userAgent.match(/IEMobile/i)) {
			$("#menu-game").click(function(){
				if($('#game-menu-dropdown').hasClass('open')){
					if ($('.right-side').hasClass('eb-right-side-offset')) {
						$('.left-side').toggle('fast', function(){
							if (!($('.right-side').hasClass('eb-right-side-offset'))) {
								$('.right-side').addClass('eb-right-side-offset');
							} else {
								$('.right-side').removeClass('eb-right-side-offset');
							}
						});		
					}
				}

				if(!($('#game-menu-dropdown').hasClass('open'))){
					if (!($('.right-side').hasClass('eb-right-side-offset'))) {
						$('.right-side').addClass('eb-right-side-offset');
						$('.left-side').toggle('fast', function(){
						});	
					}
				}
			});
		}
	});
	

  