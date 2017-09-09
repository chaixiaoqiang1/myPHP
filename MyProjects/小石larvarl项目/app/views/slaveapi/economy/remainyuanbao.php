<script>
	function getAbnormalEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.show = 0;
		$scope.processFrom = function() {
			$scope.formData.upgrade_start_time = $filter('date')($scope.upgrade_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.upgrade_end_time = $filter('date')($scope.upgrade_end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.created_start_time = $filter('date')($scope.created_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.created_end_time = $filter('date')($scope.created_end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.show = 0;
			$scope.alerts = [];
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/remainyuanbao',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
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
			<form action="/slave-api/economy/parts" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group col-md-8" style="padding-left:0;">
					<select class="form-control" name="type"
						id="type" ng-model="formData.type" ng-init="formData.type = 'yuanbao'">
							<option value="yuanbao">查询元宝余量</option>
							<option value="tongqian">查询铜钱余量</option>
							<option value="tili">查询体力余量</option>
					</select>
				</div>
				<div class="form-group col-md-8" style="padding-left:0;">
					<select class="form-control" name="server_id"
						id="server_id" ng-model="formData.server_id" ng-init = "formData.server_id = 0">
							<option value="0">请选择服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="col-xs-10" style="margin-top:5px;padding-left:0px;">
					<div class="form-group col-md-3" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.min_level')?>"
						ng-model="formData.min_level" name="min_level"?>
					</div>
					<div class="form-group col-md-3" style="padding-left:0;">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('slave.max_level')?>"
							ng-model="formData.max_level" name="max_level"?>
					</div>
				</div>
				<div class="form-group col-md-8" style="padding-left:0;">
					<select class="form-control" name="upgrade_time"
						id="upgrade_time" ng-model="formData.upgrade_time" ng-init = "formData.upgrade_time = 0">
							<option value="0"><?php echo Lang::get('slave.not'); ?><?php echo Lang::get('slave.upgrade_time_limit'); ?></option>
							<option value="1"><?php echo Lang::get('slave.upgrade_time_limit'); ?></option>
					</select>
				</div>
				<div class="form-group" ng-show="formData.upgrade_time">
					<div class="form-group" style="height: 30px;">
						<div class="col-md-6" style="padding: 0 0 0 0">
							<div class="input-group">
								<quick-datepicker ng-model="upgrade_start_time" init-value="00:00:00"></quick-datepicker> 
								<i class="glyphicon glyphicon-calendar"></i>
							</div>
						</div>
						<div class="col-md-6" style="padding: 0 0 0 0">
							<div class="input-group">
								<quick-datepicker ng-model="upgrade_end_time" init-value="23:59:59"></quick-datepicker> 
								<i class="glyphicon glyphicon-calendar"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group col-md-8" style="padding-left:0;margin-top:5px;">
					<select class="form-control" name="by_reg_time"
						id="by_reg_time" ng-model="formData.by_reg_time" ng-init = "formData.by_reg_time = 0">
							<option value="0"><?php echo Lang::get('slave.not'); ?><?php echo Lang::get('slave.by_reg_time'); ?></option>
							<option value="1"><?php echo Lang::get('slave.by_reg_time'); ?></option>
					</select>
				</div>
				<div class="form-group" ng-show="formData.by_reg_time">
					<div class="form-group" style="height: 30px;">
						<div class="col-md-6" style="padding: 0 0 0 0">
							<div class="input-group">
								<quick-datepicker ng-model="created_start_time" init-value="00:00:00"></quick-datepicker> 
								<i class="glyphicon glyphicon-calendar"></i>
							</div>
						</div>
						<div class="col-md-6" style="padding: 0 0 0 0">
							<div class="input-group">
								<quick-datepicker ng-model="created_end_time" init-value="23:59:59"></quick-datepicker> 
								<i class="glyphicon glyphicon-calendar"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-10" style="margin-top:5px;">
					<b><?php echo Lang::get('slave.log_time')?></b>
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
				
				

				
				<div class="col-xs-10" style="margin-top:5px;">
					<b>注意：</b><br>
					<b>1.此功能暂时只支持单服查询。</b><br>
					<b>2.此数据只统计时间段内有过任何货币变动的玩家。</b>
				</div>
				<div class="clearfix"></div>
				<br>
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
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td><b>服务器</b></td>
					<td><b>区间</b></td>
					<td><b>区间内总余量</b></td>
					<td><b>区间内玩家数量</b></td>
					<td><b>平均值</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.desc_name}}</td>
					<td>{{t.value}}</td>
					<td>{{t.player_num}}</td>
					<td ng-if="t.value ==0">0</td>
					<td ng-if="t.value !=0">{{t.value/t.player_num | number:2}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>