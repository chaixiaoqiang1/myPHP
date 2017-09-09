/*global $, jQuery, angular, alert, document, window */
var eastblueApp = angular.module('eastblueApp', ['ui.bootstrap', 'chieffancypants.loadingBar', 'ngAnimate', 'ngQuickDate']);

eastblueApp.config(function(ngQuickDateDefaultsProvider) {                           
	return ngQuickDateDefaultsProvider.set({
		'dateFormat' : 'yyyy-MM-dd',
		'labelFormat' : 'yyyy-MM-dd HH:mm:ss',
		'timeFormat' : 'HH:mm:ss',
		'parseDateFunction': function(str) {
			var d = Date.create(str);
			return d.isValid() ? d : null;
		}
	});
});      

eastblueApp.config(['$httpProvider', function($httpProvider) {
	$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

eastblueApp.factory('alertService', ['$rootScope', '$timeout', function ($rootScope, $timeout) {
	var alertService = {};
	alertService = {
		'alerts' : [],
		'add' : function (type, msg, timeout) {
			this.alerts.push({
				'type' : type,
				'msg'  : msg,
				'close': function() {
					return alertService.closeAlert(this);
				}
			});
			if (timeout) {
				$timeout(function(){
					alertService.closeAlert(this);	
				}, timeout);
			}
			$('html, body').animate({
				scrollTop: $(window).height() + $(document).scrollTop() + 100
			}, 1000);	
		},
		'closeAlert' : function(alert) {
			return this.closeAlertIdx(this.alerts.indexOf(alert));
		},
		'closeAlertIdx' : function(index) {
			return this.alerts.splice(index, 1);
		}
	};
	return alertService;
}]);

eastblueApp.directive('autofillable', ['$timeout', function ($timeout) {
	return {
		scope: true,
		require: 'ngModel',
		link: function (scope, elem, attrs, ctrl) {
			scope.check = function () {
				var val = elem[0].value;
				if (ctrl.$viewValue !== val) {
					ctrl.$setViewValue(val);
				}
				$timeout(scope.check, 300);
			};
			scope.check();
		}
	};
}]);

eastblueApp.filter('trustHtml', function ($sce) {
    return function (input) {
        return $sce.trustAsHtml(input);
    }
});
