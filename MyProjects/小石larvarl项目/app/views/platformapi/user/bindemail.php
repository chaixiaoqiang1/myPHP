<script>
	function bindEmailController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.user = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', 'UID: ' + data.uid + ' OK');
				location.hash = '#top';
			}).error(function(data) {
				alertService.add('danger', data.error_description);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="bindEmailController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/platform-api/user/bind-email" method="post"
				role="form" ng-submit="processFrom('/platform-api/user/bind-email')"
				onsubmit="return false;">
				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_uid')?>"
						ng-model="formData.uid" name="uid" />
				</div>
				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('platformapi.enter_login_email') ?>"
						required ng-model="formData.login_email" name="login_email" />
				</div>
				<div class="form-group">
					<input type="password" class="form-control" id="password" 
					placeholder="<?php echo Lang::get('platformapi.user_new_password') ?>" 
					 ng-model="formData.password" name="password" /> 
				</div>	
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>