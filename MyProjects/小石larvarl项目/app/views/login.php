<!DOCTYPE html>
<html class="bg-light-blue" ng-app="eastblueApp" >
    <head>
        <meta charset="UTF-8">
        <title>East Blue System | Log In</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="/css/bootstrap-and-other.css?t=1" rel="stylesheet" type="text/css" />
        <link href="/css/eastblue.css" rel="stylesheet" type="text/css" />
		<script src="/js/angular.bootstrap.jquery.js?t=1"></script>
		<script src="/js/services.js?t=2"></script>
		<script src="/js/controller.js?t=2"></script>
		<script src="/js/eastblue.js"></script>
		<script>
			function loginFormController($scope, $http) {
				$scope.alerts = [];
				$scope.formData = {};	
 				$scope.closeAlert = function(index) {
    				$scope.alerts.splice(index, 1);
  				};
				$scope.processFrom = function() {
					$http({
						'method' : 'post',
						'url' 	 : '/login',
						'data' 	 : $.param($scope.formData),
						'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
					}).success(function(data){
						location.href = '/';	
					}).error(function(data){
						$scope.alerts.splice(0, 1);
						var msg = {
							'type' : 'danger',
							'msg'  : data['error']
							};
						$scope.alerts.push(msg);
					});
					return false;
				};
			}
		</script>
    </head>
    <body class="bg-light-blue" ng-controller="loginFormController">
        <div class="form-box" id="login-box">
			<div class="header bg-white text-blue"><?php echo Lang::get('user.login') ?></div>
            <form action="/login" method="post" name="signin_form" ng-submit="processFrom();" onsubmit="return false;">
                <div class="body bg-gray">
                    <div class="form-group">
                        <input type="text" name="username" class="form-control" placeholder="Username" required ng-model="formData.username" autofocus="autofocus" autofillable/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required ng-model="formData.password" autofillable/>
                    </div>          
                </div>
                <div class="footer">                                                               
                    <button type="submit" class="btn bg-blue btn-block"><?php echo Lang::get('user.btn_login') ?></button>  
                </div>
				<div class="error-box">
					<alert ng-repeat="alert in alerts" type="alert.type" close="closeAlert($index)">{{alert.msg}}</alert>
				</div>
            </form>
        </div>


    </body>
</html>