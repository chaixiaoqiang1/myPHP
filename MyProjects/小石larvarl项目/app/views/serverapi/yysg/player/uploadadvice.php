<script>
	function LogSearchController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/upload/advice',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="LogSearchController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="advice_to"
						id="advice_to" ng-model="formData.advice_to"
						ng-init="formData.advice_to=0">
						<option value="0">请选择任务分组</option>
						<option value="1">官网</option>
						<option value="2">安卓SDK</option>
						<option value="3">IOS-SDK</option>
						<option value="4">eastblue</option>
					</select>
				</div>
				<div class="form-group">
					<div class="input-group">
						<textarea name="explain" id="explain" ng-model="formData.explain" required placeholder="请简述任务" style="height:120px;width:500px;font-size: 15px"/></textarea>
					</div>	
					<div class="input-group">
						<input type="text" name="username" id="username" ng-model="formData.username" required placeholder="请输入您的花名" />
					</div>				
				</div>
				<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<input type="submit" class="btn btn-default" value="新建任务" />
						</div>
				</div>
			</form>
		</div>
</div>