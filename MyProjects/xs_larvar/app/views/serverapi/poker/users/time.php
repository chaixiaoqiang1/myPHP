<script>
	function LoginTimeController($scope, $http, alertService, $filter){
		$scope.formData = {};
		$scope.alerts = [];
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.items = data;
			}).error(function(){
				alertService.add('error', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller = "LoginTimeController">
	<div class="row">
		<div class="form-group">
			<div class="col-md-2" style="padding: 0">
				<div class="input-group">
					<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
			</div>
			<div class="col-md-2" style="padding: 0">
				<div class="input-group">
					<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
			</div>
		</div>
	</div>
	<br><br>
	<div class="form-group">
		<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.submit-default')?>"
				ng-click="process('/game-server-api/poker/login-time')">
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped" >
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.poker_time')?></b></td>
					<td><b><?php echo Lang::get('slave.num1');?></b></td>
					<td><b><?php echo Lang::get('slave.num2');?></b></td>
					<td><b><?php echo Lang::get('slave.num3');?></b></td>
					<td><b><?php echo Lang::get('slave.num4');?></b></td>
					<td><b><?php echo Lang::get('slave.num5');?></b></td>
					<td><b><?php echo Lang::get('slave.num6');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.date}}</td>
					<td>{{t.num1}}</td>
					<td>{{t.num2}}</td>
					<td>{{t.num3}}</td>
					<td>{{t.num4}}</td>
					<td>{{t.num5}}</td>
					<td>{{t.num6}}</td>
				</tr>
			</tbody>
		</table>	
	</div>
</div>