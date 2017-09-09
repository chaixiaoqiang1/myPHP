<script>
	function PlayerEquipmentController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/change/language',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PlayerEquipmentController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group">
					<select class="form-control" name="choice" id="language"
						ng-model="formData.language" ng-init="formData.language='cn'">
						<option value="cn"><?php echo Lang::get('basic.chinese') ?></option>
						<option value="en"><?php echo Lang::get('basic.english') ?></option>
					</select>
				</div>
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" 
						ng-click="process()"/>
					</div>
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>