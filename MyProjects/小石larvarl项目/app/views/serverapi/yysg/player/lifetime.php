<script>
	function LogSearchController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/player/lifetime',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
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
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0">请选择服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="check_type"
						id="check_type" ng-model="formData.check_type"
						ng-init="formData.check_type=0">
						<option value="0">请选择玩家类型</option>	
						<option value="1">所有玩家</option>	
						<option value="2">付费玩家</option>	
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="check_data_type"
						id="check_data_type" ng-model="formData.check_data_type"
						ng-init="formData.check_data_type=0">
						<option value="0">请选择查询时长</option>	
						<option value="1">仅三天之内</option>	
						<option value="2">三至七天之内</option>	
						<option value="3">七至三十天内</option>
						<option value="4">三十天以上</option>
					</select>
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
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>生命周期时长</b></td>
					<td><b>玩家数量</b></td>
					<td><b>平均生命周期</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.time_stamp}}</td>
					<td>{{t.num}}</td>
					<td>{{t.avgtime}}天</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>