<script>
	function LogSearchController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.process = function() {
			$scope.items = [];
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/mg/avgonlinetime',
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
				<div class="form-group col-xs-12">
					<select class="form-control" name="server_ids" required multiple="true"
						id="select_game_server" ng-model="formData.server_ids" size="10">
						<optgroup label="<?php echo Lang::get('serverapi.select_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div class="form-group">
					<div class="form-group col-xs-4">
						<select class="form-control" name="limit_pay_user"
							id="select_game_server" ng-model="formData.limit_pay_user"
							ng-init="formData.limit_pay_user=0">
							<option value="0"><?php echo Lang::get('slave.all_player'); ?></option>
							<option value="1"><?php echo Lang::get('slave.pay_player'); ?></option>
							<option value="-1"><?php echo Lang::get('slave.no_pay_player'); ?></option>
						</select>
					</div>
					<div class="form-group col-xs-4">
						<input type="number" name="lev_low" ng-model="formData.lev_low" class="form-control" placeholder="等级不低于(>=)" />
					</div>
					<div class="form-group col-xs-4">
						<input type="number" name="lev_up" ng-model="formData.lev_up" class="form-control" placeholder="等级不高于(<=)" />
					</div>	
				</div>
				<div class="form-group" style="height:35px;">
					<div class="col-md-6">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="input-group">
						<input type="submit" class="btn btn-primary" value="<?php echo Lang::get('basic.btn_check') ?>" />
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
					<td><b><?php echo Lang::get('slave.server_name'); ?></b></td>
					<td><b><?php echo Lang::get('slave.player_num'); ?></b></td>
					<td><b><?php echo Lang::get('slave.sum_online_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.avg_login_times'); ?></b></td>
					<td><b><?php echo Lang::get('slave.avg_online_time_min'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.online_num}}</td>
					<td>{{t.all_online_time/60 | number:2}}</td>
					<td>{{t.all_login_times/t.online_num | number:2}}</td>
					<td>{{t.all_online_time/t.online_num/60 | number:2}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>