<script>
	function PokerGamesLogController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time,'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time,'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.items = data;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerGamesLogController">
	<div class="row">
		<form class="form-group" ng-submit="process('/game-server-api/poker/games')" onsubmit="return false">
			<div class="form-group" style="height: 30px;">
				<div class="col-md-6" style="padding-left: 0px ;width:50%">
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-6" style="padding-left:15px;width:50%">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
			</div>
			<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
		</form>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" style="padding:0">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('serverapi.time')?></b></td>
					<td><b><?php echo Lang::get('serverapi.log1')?></b></td>
					<td><b><?php echo Lang::get('serverapi.log2')?></b></td>
					<td><b><?php echo Lang::get('serverapi.login_out_times')?></b></td>
					<td><b><?php echo Lang::get('serverapi.log5')?></b></td>
					<td><b><?php echo Lang::get('serverapi.log6')?></b></td>
					<td><b><?php echo Lang::get('serverapi.log3')?></b></td>
					<td><b><?php echo Lang::get('serverapi.endOneRound_times')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.date}}</td>
					<td>{{t.num1}}</td>
					<td>{{t.num2}}</td>
					<td>{{t.login_out_times}}</td>
					<td>{{t.num3}}</td>
					<td>{{t.num5}}</td>
					<td>{{t.num6}}</td>
					<td>{{t.endOneRound_times}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>