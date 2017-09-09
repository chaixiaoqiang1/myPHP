<script>
	function queryLogEconomy($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
		$scope.lookup = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="queryLogEconomy">
	<div class="row">
		<div class="eb-content">
			<div class="clearfix">
				<br />
			</div>


			<div class="form-group">
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
			</div>

			<br>

			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-primary"
					value="<?php echo '查询' ?>"
					ng-click="process('/game-server-api/poker/queryLogEconomy')" />
			</div>

			<div class="col-xs-12">
				<table class="table table-striped">
					<thead>
						<tr class="info">
						<!-- select (diff_tongqian>0) as is_fafang, from_unixtime(action_time, '%Y%m%d') as d, action_type, SUM( diff_tongqian ) s -->
							<td>是否发放</td>
							<td>时间</td>
							<td>类型</td>
							<td>总数</td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat='t in items.re1'>
							<td>{{t.is_fafang}}	</td>
							<td>{{t.d}}</td>
							<td>{{t.action_type}}</td>
							<td>{{t.s}}</td>
						</tr>
					</tbody>
				</table>
				<table class="table table-striped">
					<thead>
						<tr class="info">
							<td>时间</td>
							<td>筹码</td>
							<td>总和</td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat='t in items.re2'>
							<td>{{t.d}}</td>
							<td>{{t.tongqian}}</td>
							<td>{{t.sum}}</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
	</div>

</div>