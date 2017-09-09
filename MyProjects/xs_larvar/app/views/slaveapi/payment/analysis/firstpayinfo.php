<script>
	function getAbnormalEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.show = 0;
		$scope.processFrom = function() {
			$scope.alerts = [];
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/payment/firstpayinfo',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getAbnormalEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/economy/parts" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group">
					<input type="number" placeholder="请输入间隔天数" id="interval" name="interval" ng-model="formData.interval" required />
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-default"
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
				</div>
			</form>
		</div>
	</div>
	<div class="row margin-top-10 col-md-6">
		<p><b>注意：数据以天为单位，单日的首次付费用户数就是当天注册并且当天有过充值的玩家数量，而更长时间间隔的数据是以天为单位的数据的和，
		例如以七天为间隔的首次付费用户数并不是在这七天内注册且在七天内有过充值的玩家，而是七天的每一天的当天注册并且充值的玩家数的和，望理解</b></p>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td><b>统计时间段</b></td>
					<td><b>首次付费用户数</b></td>
					<td><b>新增用户付费数</b></td>
					<td><b>新增付费老用户数</b></td>
					<td><b>首次付费率</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="f in items">
					<td>{{f.start_time}}~{{f.end_time}}</td>
					<td>{{f.allnum}}</td>
					<td>{{f.newnum}}</td>
					<td>{{f.oldnum}}</td>
					<td>{{f.rate}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>