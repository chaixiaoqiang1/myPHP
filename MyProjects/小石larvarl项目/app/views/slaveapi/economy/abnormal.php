<script>
	function getAbnormalEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.processFrom = function() {
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/player/abnormal',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getAbnormalEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/economy/player/abnormal" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group col-md-8" style="padding-left:0;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="clearfix"></div>
				<div class="form-group col-md-4" style="padding-left:0;">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('slave.yuanbao')?></option>
						<option value="1"><?php echo Lang::get('slave.tongqian')?></option>
						<option value="2"><?php echo Lang::get('slave.shengwang')?></option>
						<option value="3"><?php echo Lang::get('slave.tili')?></option>
						<option value="4"><?php echo Lang::get('slave.jingjiedian')?></option>
						<option value="5"><?php echo Lang::get('slave.yueli')?></option>
						<option value="6"><?php echo Lang::get('slave.xianling')?></option>
						<option value="7"><?php echo Lang::get('slave.boat_book')?></option>
						<option value="8"><?php echo Lang::get('slave.lingshi')?></option>
						<?php if('flsg' == $game_code) {?>
							<option value="9"><?php echo Lang::get('slave.star_fragment')?></option>
							<option value="10"><?php echo Lang::get('slave.talent_point')?></option>
							<option value="11"><?php echo Lang::get('slave.heaven_token')?></option>
							<option value="12"><?php echo Lang::get('slave.skill_fragment')?></option>
							<option value="13"><?php echo Lang::get('slave.fight_spirit')?></option>
							<option value="14"><?php echo Lang::get('slave.rings_exp')?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group col-md-4">
					<input type="text" class="form-control" required
						placeholder="所查物品下限值"
						 ng-model="formData.min_limit" name="min_limit" />
				</div>
				<div class="clearfix"></div>
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
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px">该功能主要用来查询玩家获得的大额经济数据，用来作为判断经济数据是否有异常的一种依据</span>
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

	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>消费数据</b></td>
					<td><b>玩家ID</b></td>
					<td><b>操作类型</b></td>
					<td><b>第一次操作时间</b></td>
					<td><b>最后一次操作时间</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.spend}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.first_time}}</td>
					<td>{{t.last_time}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>