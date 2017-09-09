<script>
	function getPlayerTrendController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = 0;
		$scope.date_result = {};
		$scope.formData.by_what_time = 'login';
		$scope.processFrom = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.create_start_time = $filter('date')($scope.create_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.create_end_time = $filter('date')($scope.create_end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.login_start_time = $filter('date')($scope.login_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.login_end_time = $filter('date')($scope.login_end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.last_login_time = $filter('date')($scope.last_login_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/calculate/retention',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = 0;
				$scope.date_result = {};
				$scope.total = data.total_create;
				$scope.date_result = data.date_result;
			}).error(function(data) {
				$scope.total = 0;
				$scope.date_result = {};
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
					<select class="form-control" name="by_create_time"
						id="by_create_time" ng-model="formData.by_create_time"
						ng-init="formData.by_create_time=1">
							<option value="0"><?php echo Lang::get('slave.not_by_create_time') ?></option>	
							<option value="1"><?php echo Lang::get('slave.by_create_time') ?></option>	
							<option value="2"><?php echo Lang::get('slave.by_create_time_pay') ?></option>	
					</select>
				</div>
				<div class="form-group" ng-show="formData.by_create_time">
					<b><?php echo Lang::get('slave.by_create_time'); ?></b><br>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="create_start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="create_end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<br ng-show="formData.by_create_time">
				<?php if('poker' == $game_code){ ?>
					<div class="form-group">
						<select class="form-control" name="by_what_time"
							id="by_what_time" ng-model="formData.by_what_time"
							ng-init="formData.by_what_time='login'">
								<option value="login"><?php echo Lang::get('slave.by_login_time') ?></option>	
								<option value="play"><?php echo Lang::get('slave.by_play_time') ?></option>	
						</select>
					</div>
				<?php } ?>
				<div class="form-group">
					<b ng-if="formData.by_what_time == 'login'"><?php echo Lang::get('slave.by_login_time'); ?></b>
					<b ng-if="formData.by_what_time == 'play'"><?php echo Lang::get('slave.by_play_time'); ?></b><br>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="login_start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="login_end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<br>
				<div class="form-group">
					<input type="number" class="form-control"
						placeholder="<?php echo Lang::get('slave.interval_day')?>"
						ng-model="formData.interval" name="interval"?>
				</div>
				<div class="form-group" ng-show="formData.by_what_time == 'login'">
					<select class="form-control" name="by_last_login_time"
						id="by_last_login_time" ng-model="formData.by_last_login_time"
						ng-init="formData.by_last_login_time=0">
							<option value="0"><?php echo Lang::get('slave.not') ?><?php echo Lang::get('slave.by_last_login_time') ?></option>	
							<option value="1"><?php echo Lang::get('slave.by_last_login_time') ?></option>	
					</select>
				</div>
				<div class="form-group" ng-show="formData.by_last_login_time && formData.by_what_time == 'login'">
					<p><b><?php echo Lang::get('slave.by_last_login_time'); ?></b></p>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="last_login_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group col-md-12" style="padding: 0">
					<ul>
						<li><?php echo Lang::get('slave.calculate_retention_note_1'); ?></li>
						<li><?php echo Lang::get('slave.calculate_retention_note_2'); ?></li>
						<li><?php echo Lang::get('slave.calculate_retention_note_3'); ?></li>
					</ul>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" style="margin-top-10" value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
		<div class="panel panel-success">
			<div class="panel-heading">
					<?php echo Lang::get('slave.basic_sign_pay_num') ?>
				</div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt><?php echo Lang::get('slave.basic_sign_pay_num')?>:</dt>
					<dd>{{total}}</dd>
				</dl>
			</div>
		</div>
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.count_start_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.count_end_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.result_num'); ?></b></td>
					<td><b><?php echo Lang::get('slave.result_num_rate'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in date_result">
					<td>{{t.count_start_time}}</td>
					<td>{{t.count_end_time}}</td>
					<td>{{t.result_num}}</td>
					<td>{{t.result_num*100/total | number :2}}%</td>
				</tr>
			</tbody>
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>