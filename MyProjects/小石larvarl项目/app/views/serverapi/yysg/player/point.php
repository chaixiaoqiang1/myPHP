<script>
	function NewerPointController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.create_num = 0;
		$scope.process = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/yysg/newer/point',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.create_num = data.create_num;
				$scope.point_info = data.point_info;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="NewerPointController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group" style="height:35px;">
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
				<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" />
						</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-5">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.created_player_number'); ?></b></td>
					<td><b>{{create_num}}</b></td>
					<td><b>{{create_num/create_num*100 | number : 2}}%</b></td>
				</tr>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.point_opreation'); ?></b></td>
					<td><b><?php echo Lang::get('slave.player_nums'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rate'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in point_info">
					<td>{{t.point}}</td>
					<td>{{t.num}}</td>
					<td>{{t.num/create_num*100 | number : 2}}%</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>