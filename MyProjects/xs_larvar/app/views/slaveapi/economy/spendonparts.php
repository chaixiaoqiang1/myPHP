<script>
	function getAbnormalEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.each_player = [];
		$scope.sum = 0;
		$scope.processFrom = function() {
			$scope.sum = 0;
			$scope.each_player = [];
			$scope.items = [];
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/parts',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.each_server = data.each_server;
				$scope.each_player = data.each_player;
				$scope.items = data.parts.result;
				$scope.sum = data.parts.sum;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getAbnormalEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group col-md-8" style="padding-left:0;">
					<select class="form-control" name="check_type"
						id="check_type" ng-model="formData.check_type"
						ng-init="formData.check_type=0">
						<option value="0"><?php echo Lang::get('slave.one_server_parts') ?></option>
						<option value="1"><?php echo Lang::get('slave.all_server_changes') ?></option>
						<option value="2"><?php echo Lang::get('slave.one_server_player') ?></option>
					</select>
				</div>
				<div class="form-group col-md-8" style="padding-left:0;" ng-if="1 != formData.check_type">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-8" style="padding-left:0;" ng-if="1 == formData.check_type">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id" required multiple="true" size="10">
						<optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="clearfix"></div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type='yuanbao'">
						<option value="yuanbao"><?php echo Lang::get('serverapi.yuanbao') ?></option>
						<option value="tongqian"><?php echo Lang::get('serverapi.tongqian') ?></option>
						<option value="tili"><?php echo Lang::get('serverapi.promotion_tili') ?></option>
					</select>
				</div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<select class="form-control" name="symbol" ng-model="formData.symbol"
						ng-init="formData.symbol='<'">
						<option value="<">消耗</option>
						<option value=">">增加</option>
					</select>
				</div>
				<div class="form-group col-md-12" style="padding-left:0;" ng-show="2 == formData.check_type">
					<div class="form-group col-md-4" style="padding-left:0;">
						<select class="form-control" name="limit_symbol" ng-model="formData.limit_symbol"
							ng-init="formData.limit_symbol=0">
							<option value="0">选择限制</option>
							<option value="<">玩家变动少于</option>
							<option value=">">玩家变动大于</option>
							<option value="=">玩家变动等于</option>
						</select>
					</div>
					<div class="form-group col-md-4" style="padding-left:0;">
						<input type="number" class="form-control"
							placeholder="<?php echo Lang::get('slave.limit_value')?>"
							ng-model="formData.limit_value" name="limit_value"?>
					</div>
					<div class="form-group col-md-8" style="padding-left:0;">
						<ul>
							<li><b><?php echo Lang::get('slave.spendonparts_note1'); ?></b></li>
							<li><b><?php echo Lang::get('slave.spendonparts_note2'); ?></b></li>
						</ul>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-3;">
					<b style="color:red">敬告：建议勿选过长时间</b>
				</div>
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

	<div class="col-xs-12" ng-if="items.length">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>类型</b></td>
					<td><b>商店物品单价</b></td>
					<td><b><?php echo Lang::get('slave.player_num'); ?></b></td>
					<td><b><?php echo Lang::get('slave.times'); ?></b></td>
					<td><b>总值</b></td>
					<td><b>占比</b></td>
				</tr>
				<tr ng-if="sum != 0">
					<td><b>总计</b></td>
					<td><b>--</b></td>
					<td><b>--</b></td>
					<td><b>--</b></td>
					<td><b>{{sum}}</b></td>
					<td><b>100.00%</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.actionvalue}}</td>
					<td>{{t.singlepirce}}</td>
					<td>{{t.player_num}}</td>
					<td>{{t.times}}</td>
					<td>{{t.sumvalue}}</td>
					<td>{{t.rate}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-8" ng-if="each_player.length">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>Player_ID</b></td>
					<td><b>Player_Name</b></td>
					<td><b><?php echo Lang::get('slave.change_num'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="e in each_player">
					<td>{{e.player_id}}</td>
					<td>{{e.player_name}}</td>
					<td>{{e.sumvalue}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-8" ng-if="each_server.length">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>Server_Name</b></td>
					<td><b><?php echo Lang::get('slave.change_num'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="s in each_server">
					<td>{{s.server_name}}</td>
					<td>{{s.change}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>