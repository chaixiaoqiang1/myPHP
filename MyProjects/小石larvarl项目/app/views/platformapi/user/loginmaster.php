<script>
	function loginMasterController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.uid +" "+data.error_description);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="loginMasterController">
	<div class="row">
		<div class="eb-content">
			<form action="/platform-api/user/login-master" method="post"
				role="form"
				ng-submit="processFrom('/platform-api/user/login-master')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="operate_type" id="operate_type"
						ng-model="formData.operate_type" ng-init="formData.operate_type=0">
						<option value="0"><?php echo Lang::get('serverapi.open_login_master') ?></option>
						<option value="1"><?php echo Lang::get('serverapi.close_login_master') ?></option>
					</select>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"
								ng-model="formData.choice" name="choice" value="0"
								ng-checked="true">
							</span> <input type="text" class="form-control"
								ng-model="formData.login_email" name="login_email"
								placeholder="<?php echo Lang::get('serverapi.enter_login_email') ?>" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"
								ng-model="formData.choice" name="choice" value="1">
							</span> <input type="text" class="form-control"
								ng-model="formData.uid" name="uid"
								placeholder="<?php echo Lang::get('serverapi.enter_uid') ?>" />
						</div>
					</div>
				</div>

				<div class="clearfix">
					<br />
				</div>
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>