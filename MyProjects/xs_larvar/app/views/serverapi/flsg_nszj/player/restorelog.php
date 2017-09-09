<script>
	function RestoreController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time,'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.items = data;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="RestoreController">
	<div class="row">
	<div class="eb-content">
		<form class="form-group" ng-submit="process('/game-server-api/serach/restorelog')" onsubmit="return false">
			<div class="form-group">
					<select class="form-control" name="game_id"
						id="game_id" ng-model="formData.game_id"
						ng-init="formData.game_id=0">
						<option value="0">选择游戏(不选择则默认为当前游戏)</option>
						<option value="999">所有游戏</option>
						<?php foreach ($games as $k => $v) { ?>
						<option value="<?php echo $k ?>"><?php echo $v;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding-left: 0px ;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding-left:15px;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
			<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
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
		<table class="table table-striped" style="width:75%">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.operator'); ?></b></td>
					<td><b><?php echo Lang::get('slave.order_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.pay_amount'); ?></b></td>
					<td><b><?php echo Lang::get('slave.yuanbao'); ?></b></td>
					<td><b><?php echo Lang::get('slave.giftbag'); ?></b></td>
					<td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.player_name'); ?></b></td>
					<td><b><?php echo Lang::get('slave.server_name'); ?></b></td>
					<td><b><?php echo Lang::get('slave.restore_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.belong_game'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.username}}</td>
					<td><a href="/slave-api/payment/order?order_id={{t.order_id}}" target="_jump">{{t.order_id}}</a></td>
					<td>{{t.pay_amount}}</td>
					<td>{{t.yuanbao_amount}}</td>
					<td>{{t.giftbag_id}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.server_name}}</td>
					<td>{{t.operate_time}}</td>
					<td>{{t.game_name}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>