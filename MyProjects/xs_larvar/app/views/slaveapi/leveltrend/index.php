<script>
	function getPlayerTrendController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/player/trend',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = {};
				$scope.total = data;
			}).error(function(data) {
				$scope.total = {};
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getPlayerTrendController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/player/trend" method="get" role="form"
				ng-submit="processFrom('/slave-api/player/trend')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<div class="col-md-6" style="padding-left:0">
						<select class="form-control" name="by_create_time"
							id="by_create_time" ng-model="formData.by_create_time"
							ng-init="formData.by_create_time=0">
								<option value="0"><?php echo Lang::get('slave.not_by_create_time') ?></option>	
								<option value="1"><?php echo Lang::get('slave.by_create_time') ?></option>	
						</select>
					</div>
					<div class="col-md-6" style="padding-right:0">
						<select class="form-control" name="is_anonymous"
							ng-model="formData.is_anonymous" ng-init="formData.is_anonymous=2">
								<option value="0"><?php echo Lang::get('slave.formal_player') ?></option>	
								<option value="1"><?php echo Lang::get('slave.anonymous_player') ?></option>
								<option value="2"><?php echo Lang::get('slave.quanbu') ?></option>	
						</select>
					</div>
				</div>
				<div class="form-group" style="height:35px;" ng-show="formData.by_create_time">
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
				<input type="submit" class="btn btn-primary" style="margin-top-10" value="<?php echo Lang::get('basic.btn_show') ?>" />
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
					<td><b><?php echo Lang::get('slave.level');?></b></td>
					<td><b><?php echo Lang::get('slave.player_nums');?></b></td>
					<td><b><?php echo Lang::get('slave.player_percentage');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
				    <td>{{t.level}}</td>
				    <td>{{t.count}}</td>
				    <td>{{t.rate}}%</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>