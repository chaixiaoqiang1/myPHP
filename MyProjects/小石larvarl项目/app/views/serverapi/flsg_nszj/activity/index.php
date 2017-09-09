<script>
	function activityController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = '';
		$scope.end_time = '';
		$scope.formData = {};
		$scope.total = [];

		$scope.filter = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.send = function(url) {
			if (!confirm('请点击【筛选数据】按钮，确认要发送福利的对象')) {
				return false;
			}
			if (!confirm('危险操作，再确认一次')) {
				return false;
			}
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', "OK");
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="activityController">
	<div class="row">
		<div class="eb-content">
			<div class="well">
				<select class="form-control" name="server_id"
					id="select_game_server" ng-model="formData.server_id"
					ng-init="formData.server_id=0" multiple="multiple"
					ng-multiple="true" size=10>
					<optgroup
						label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
				</select>
				<div class="clearfix">
					<br />
				</div>
				<div class="form-group" style="height: 10px;">
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
				<div class="clearfix">
					<br />
				</div>
				<div class="form-group">
					<input type="radio" name="activity_type"
						ng-model="formData.activity_type" value="1" ng-value="1"
						ng-init="formData.activity_type=1" />
					<?php echo Lang::get('serverapi.recharge');?>
					<!--<input type="radio" name="activity_type"
						ng-model="formData.activity_type" value="2" ng-value="2" />
					<?php echo Lang::get('serverapi.consume');?>-->
					<!--<input type="radio" name="activity_type"
						ng-model="formData.activity_type" value="3" ng-value="3" />
					<?php echo Lang::get('serverapi.lucky_order');?>
					<input type="radio" name="activity_type"
						ng-model="formData.activity_type" value="4" ng-value="4" />
					<?php echo Lang::get('serverapi.lucky_draw');?>-->
				</div>
			</div>
			<!-- 活动类型1——————充值送
--------------------------	--------------------------	--------------------------	--------------------------
			 -->
			<div class="well" ng-if="formData.activity_type == 1">
				<div class="col-md-6" style="padding: 0">
					<input type="text" class="form-control"
						ng-model="formData.recharge_lower_bound" name="recharge_lower_bound"
						placeholder="<?php echo Lang::get('slave.pay_amount_lower_bound') ?>" />
				</div>
				<div class="col-md-6" style="padding: 2">
					<input type="text" class="form-control"
						ng-model="formData.recharge_upper_bound" name="recharge_upper_bound"
						placeholder="<?php echo Lang::get('slave.pay_amount_upper_bound') ?>" />
				</div>
				<div class="clearfix">
					<br /><br/><br/>
				</div>
				<input type='button' class="btn btn-primary"
					value="<?php echo Lang::get('basic.btn_select_data') ?>"
					ng-click="filter('/game-server-api/activity/filter-data')" />
				<div class="clearfix">
					<br />
				</div>
				<!--<input type="radio" name="award_type"
					ng-model="formData.award_type" value="1" ng-value="1"
					ng-init="formData.award_type=1" />
					<?php echo Lang::get('serverapi.recharge_award_gift');?>
					<input type="radio" name="award_type"
					ng-model="formData.award_type" value="2" ng-value="2" />
					<?php echo Lang::get('serverapi.recharge_award_yuanbao');?>
					<input type="radio" name="award_type"
					ng-model="formData.award_type" value="3" ng-value="3" />
					<?php echo Lang::get('serverapi.recharge_award_yuanbao2');?>
					<div class="clearfix">
					<br />
				</div>-->
				<!--<div class="well" ng-if="formData.award_type == 1">
					<select class="form-control" name="gift_types"
						id="select_gift_types" ng-model="formData.gift_types"
						multiple="multiple" ng-multiple="true" size=5>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_gift_bag') ?>">
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>-->
				<div class="well" ng-if="formData.award_type == 2">
					<input type="text" ng-model="formData.award_yuanbao_amount"
						name="award_yuanbao_amount"
						placeholder="<?php echo Lang::get('serverapi.award_yuanbao_amount') ?>" />
					<div class="clearfix">
						<br />
					</div>
				</div>
				<div class="well" ng-if="formData.award_type == 3">
					<input type="text" ng-model="formData.award_yuanbao_amount2"
						name="award_yuanbao_amount2"
						placeholder="<?php echo Lang::get('serverapi.award_yuanbao_amount2') ?>" />
					<div class="clearfix">
						<br /><br/><br/>
					</div>
				</div>
				<!--<input type='button' class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_send_gift') ?>"
					ng-click="send('/game-server-api/activity/send-gift')" />-->
			</div>
			<!-- 
--------------------------	--------------------------	--------------------------	--------------------------
			 -->

			<!-- 活动类型2 消费送
--------------------------	--------------------------	--------------------------	--------------------------
			 -->
			<div class="well" ng-if="formData.activity_type == 2">
				<div class="col-md-6" style="padding: 0">
					<input type="text" class="form-control"
						ng-model="formData.consume_lower_bound" name="consume_lower_bound"
						placeholder="<?php echo Lang::get('slave.consume_yuanbao_lower_bound') ?>" />
				</div>
				<div class="col-md-6" style="padding: 2">
					<input type="text" class="form-control"
						ng-model="formData.consume_upper_bound" name="consume_upper_bound"
						placeholder="<?php echo Lang::get('slave.consume_yuanbao_upper_bound') ?>" />
				</div>
				<div class="clearfix">
					<br /><br/><br/>
				</div>
				<input type='button' class="btn btn-primary"
					value="<?php echo Lang::get('basic.btn_select_data') ?>"
					ng-click="filter('/game-server-api/activity/filter-data')" />
				<div class="clearfix">
					<br />
				</div>
				<!--<input type="radio" name="award_type" ng-model="formData.award_type"
					value="1" ng-value="1" ng-init="formData.award_type=1" />
					<?php echo Lang::get('serverapi.recharge_award_gift');?>
					<input type="radio" name="award_type"
					ng-model="formData.award_type" value="2" ng-value="2" />
					<?php echo Lang::get('serverapi.recharge_award_yuanbao');?>
					<input type="radio" name="award_type"
					ng-model="formData.award_type" value="3" ng-value="3" />
					<?php echo Lang::get('serverapi.recharge_award_yuanbao2');?>
					<div class="clearfix">
					<br />
				</div>
				<div class="well" ng-if="formData.award_type == 1">
					<select class="form-control" name="gift_types"
						id="select_gift_types" ng-model="formData.gift_types"
						multiple="multiple" ng-multiple="true" size=5>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_gift_bag') ?>">
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>-->
				<div class="well" ng-if="formData.award_type == 2">
					<input type="text" ng-model="formData.award_yuanbao_amount"
						name="award_yuanbao_amount"
						placeholder="<?php echo Lang::get('serverapi.award_yuanbao_amount') ?>" />
					<div class="clearfix">
						<br />
					</div>
				</div>
				<div class="well" ng-if="formData.award_type == 3">
					<input type="text" ng-model="formData.award_yuanbao_amount2"
						name="award_yuanbao_amount2"
						placeholder="<?php echo Lang::get('serverapi.award_yuanbao_amount2') ?>" />
					<div class="clearfix">
						<br />
					</div>
				</div>
				<!--<input type='button' class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_send_gift') ?>"
					ng-click="send('/game-server-api/activity/send-gift')" />-->
			</div>
			<!-- 
--------------------------	--------------------------	--------------------------	--------------------------
			 -->
			<!-- 幸运订单号
--------------------------	--------------------------	--------------------------	--------------------------
			 -->
			<!--<div class="well" ng-if="formData.activity_type == 3">
				<input type="text" class="form-control"
					ng-model="formData.lucky_number" name="lucky_number"
					placeholder="<?php echo Lang::get('slave.enter_lucky_number') ?>" />
				<div class="clearfix">
					<br />
				</div>
				<input type='button' class="btn btn-primary"
					value="<?php echo Lang::get('basic.btn_select_data') ?>"
					ng-click="filter('/game-server-api/activity/filter-data')" />
				<div class="clearfix">
					<br />
				</div>
				<input type="radio" name="award_type" ng-model="formData.award_type"
					value="1" ng-value="1" ng-init="formData.award_type=1" />
					<?php echo Lang::get('serverapi.recharge_award_gift');?>
					<input type="radio" name="award_type"
					ng-model="formData.award_type" value="2" ng-value="2" />
					<?php echo Lang::get('serverapi.recharge_award_yuanbao');?>
					<input type="radio" name="award_type"
					ng-model="formData.award_type" value="3" ng-value="3" />
					<?php echo Lang::get('serverapi.recharge_award_yuanbao2');?>
					<div class="clearfix">
					<br />
				</div>
				<div class="well" ng-if="formData.award_type == 1">
					<select class="form-control" name="gift_types"
						id="select_gift_types" ng-model="formData.gift_types"
						multiple="multiple" ng-multiple="true" size=5>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_gift_bag') ?>">
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div class="well" ng-if="formData.award_type == 2">
					<input type="text" ng-model="formData.award_yuanbao_amount"
						name="award_yuanbao_amount"
						placeholder="<?php echo Lang::get('serverapi.award_yuanbao_amount') ?>" />
					<div class="clearfix">
						<br />
					</div>
				</div>
				<div class="well" ng-if="formData.award_type == 3">
					<input type="text" ng-model="formData.award_yuanbao_amount2"
						name="award_yuanbao_amount2"
						placeholder="<?php echo Lang::get('serverapi.award_yuanbao_amount2') ?>" />
					<div class="clearfix">
						<br />
					</div>
				</div>
				<input type='button' class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_send_gift') ?>"
					ng-click="send('/game-server-api/activity/send-gift')" />
			</div>-->
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><b><?php echo Lang::get('serverapi.player_server');?></b></td>
						<td><b>UID</b></td>
						<td><b><?php echo Lang::get('serverapi.player_id');?></b></td>
						<td><b><?php echo Lang::get('serverapi.player_name');?></b></td>
						<td ng-if="formData.activity_type == 1"><b><?php echo Lang::get('slave.order_recharge_amount');?></b></td>
						<td ng-if="formData.activity_type == 1"><b><?php echo Lang::get('slave.order_recharge_dollar');?></b></td>
						<td ng-if="formData.activity_type == 1"><b><?php echo Lang::get('slave.order_recharge_yuanbao');?></b></td>
						<td ng-if="formData.activity_type == 1"><b><?php echo Lang::get('slave.recharge_times');?></b></td>
						<td ng-if="formData.activity_type == 2"><b><?php echo Lang::get('serverapi.consume_yuanbao');?></b></td>
						<td ng-if="formData.activity_type == 3"><b><?php echo Lang::get('slave.order_sn');?></b></td>
						<td ng-if="formData.activity_type == 3"><b><?php echo Lang::get('slave.order_recharge_amount');?></b></td>
						<td ng-if="formData.activity_type == 3"><b><?php echo Lang::get('slave.order_recharge_dollar');?></b></td>
						<td ng-if="formData.activity_type == 3"><b><?php echo Lang::get('slave.order_recharge_yuanbao');?></b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in total">
						<td>{{t.server_name}}</td>
						<td>{{t.uid}}</td>
						<td>{{t.player_id}}</td>
						<td>{{t.player_name}}</td>
						<td ng-if="formData.activity_type == 1">{{t.total_amount|number:2}}</td>
						<td ng-if="formData.activity_type == 1">{{t.total_dollar_amount|number:2}}</td>
						<td ng-if="formData.activity_type == 1">{{t.total_yuanbao_amount|number:2}}</td>
						<td ng-if="formData.activity_type == 1">{{t.count}}</td>
						<td ng-if="formData.activity_type == 2">{{t.spend}}</td>
						<td ng-if="formData.activity_type == 3">{{t.order_sn}}</td>
						<td ng-if="formData.activity_type == 3">{{t.pay_amount|number:2}}</td>
						<td ng-if="formData.activity_type == 3">{{t.dollar_amount|number:2}}</td>
						<td ng-if="formData.activity_type == 3">{{t.yuanbao_amount|number:2}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>