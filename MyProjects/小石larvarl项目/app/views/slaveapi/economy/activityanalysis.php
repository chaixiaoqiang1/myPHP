<script>
	function getEconomyRankController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			$scope.total = {};
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/activity/analysis',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getEconomyRankController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/economy/rank" method="get" role="form"
				ng-submit="processFrom('/slave-api/economy/rank')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_ids"
						id="select_game_server" ng-model="formData.server_ids"
						ng-init="formData.server_ids=0" multiple="true" size="10" required >
						<option value="0">全部服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="activity_idnames"
						id="activity_idnames" ng-model="formData.activity_idnames" ng-init="formData.activity_idnames = 'all'" multiple="true" size="10" required>
							<option value="all">不限活动</option>
						<?php foreach ($activities as $k => $v) { ?>
							<option value="<?php echo $v; ?>"><?php echo $v; ?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
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
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
		<table class="table table-striped table-hover" ng-repeat="activity in total">
			<thead>
				<tr class="info">
					<td><b>活动</b></td>
					<td><b>玩家操作</b></td>
					<td><b>参与总人数</b></td>
					<td><b>参与总次数</b></td>
					<td><b>元宝总变化</b></td>
					<td><b>铜钱总变化</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in activity">
					<td>{{t.activity_name}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.player_num}}</td>
					<td>{{t.times}}</td>
					<td>{{t.diff_yuanbao}}</td>
					<td>{{t.diff_tongqian}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>