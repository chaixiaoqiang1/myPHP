<script>
	function getAllserverEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/all-server',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data.body;
				$scope.this_server = data.this_server;
				$scope.this_server_vip = data.this_server_vip;
				$scope.all_server = data.total;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getAllserverEconomyController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/economy/all-server" method="get" role="form"
				ng-submit="processFrom('/slave-api/economy/all-server')"
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
				<div class="form-group" style="height:30px;">
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
				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="padding-left:0;height:30px;">
					<select class="form-control" name="type"
						ng-model="formData.type" ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('slave.yuanbao')?></option>
						<option value="1"><?php echo Lang::get('slave.tongqian')?></option>
						<option value="2"><?php echo Lang::get('slave.gongxun')?></option>
					</select>
				</div>
				<div class="form-group col-md-6" style="padding:0;">
					<input type="text" class="form-control" placeholder="<?php echo Lang::get('slave.enter_player_level') ?>"
						ng-model="formData.player_level" name="player_level" />
				</div>
				
				<div class="form-group">
				<label><input type="checkbox" name="filter_type1" value="1"
					ng-model="formData.filter_type1" /><?php echo Lang::get('slave.filter_neiwan') ?></label>
				</div>
				<div class="form-group">
				    <select class="form-control" name="vip_level"
				            ng-model="formData.vip_level"
				      		 multiple="multiple"
				            ng-multiple="true" size=10>
				        <optgroup
				            label="<?php echo Lang::get('serverapi.select_vip_level') ?>">
				            <?php for ($i=0;$i<=12;$i++) { ?>
    							<option value="<?php echo $i?>">VIP<?php echo $i?></option>
    						<?php } ?>
				        </optgroup>
				    </select>
				</div>
				<p><font color=red>vip<?php echo Lang::get('slave.economy_all_server_tip');?></font></p>
				<div class="clearfix"></div>
				<input type="submit" class="btn btn-default"
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.consumption_statics');?></b></td>
					<td><b><?php echo Lang::get('slave.operation_name');?></b></td>
					<td>Message</td>
					<td><b><?php echo Lang::get('slave.operation_time');?></b></td>
					<td><b><?php echo Lang::get('slave.operation_num');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{all_server.spend}}</td>
					<td>{{all_server.action_type}}</td>
					<td>{{all_server.action_name}}</td>
					<td>{{all_server.action_time}}</td>
					<td>{{all_server.num}}</td>
				</tr>
				<tr>
					<td>{{this_server.spend}}</td>
					<td>{{this_server.action_type}}</td>
					<td>{{this_server.action_name}}</td>
					<td>{{this_server.action_time}}</td>
					<td>{{this_server.num}}</td>
				</tr>
				<tr>
					<td>{{this_server_vip.spend}}</td>
					<td>{{this_server_vip.action_type}}</td>
					<td>{{this_server_vip.action_name}}</td>
					<td>{{this_server_vip.action_time}}</td>
					<td>{{this_server_vip.num}}</td>
				</tr>
				<tr ng-repeat="t in total">
					<td>{{t.spend}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.action_name}}</td>
					<td>{{t.action_time}}</td>
					<td>{{t.num}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>