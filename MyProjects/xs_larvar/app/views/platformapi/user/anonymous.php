<script>
function upgradeAnonymousController($scope, alertService, $http) {
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
				alertService.add('success', data.res);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
}
</script>
<div class="col-xs-12" ng-controller="upgradeAnonymousController">
	<div class="row" >
		<div class="eb-content">
			<form action="/platform-api/user/anonymous" method="post" role="form" ng-submit="processFrom('/platform-api/user/anonymous')" onsubmit="return false;">
				<div class="form-group">
					<input type="text" class="form-control" id="email" placeholder="<?php echo Lang::get('platformapi.enter_email') ?>" required ng-model="formData.email" name="email" /> 
				</div>				
				<div class="form-group">
					<input type="text" class="form-control" id="password" placeholder="<?php echo Lang::get('platformapi.user_new_password') ?>" required ng-model="formData.password" name="password" /> 
				</div>				
				<div class="form-group">
					<input type="text" class="form-control" id="uid" placeholder="<?php echo Lang::get('platformapi.enter_user_id') ?>" required ng-model="formData.uid" name="uid" /> 
				</div>				
				
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>