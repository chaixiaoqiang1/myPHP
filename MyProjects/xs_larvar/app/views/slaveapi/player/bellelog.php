<script>
	function BelleLogSearchController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			$scope.items={};
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/flsg/guess/log',
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
<div class="col-xs-12" ng-controller="BelleLogSearchController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group col-md-8" style="padding:0">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
						<?php foreach ($servers as $key => $value) { ?>
							<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
						<?php }?>
					</select>
				</div>
				<div class="form-group col-md-8" style="padding:0">
					<select class="form-control" name="choice" ng-model="formData.choice" ng-init="formData.choice=0">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<div class="form-group col-md-8" style="padding:0">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="输入昵称或ID"
						required ng-model="formData.id_or_name" name="id_or_name" />
				</div>
				<div class="form-group" style="height:35px;">
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
			<br>
			<br>
				
			<div class="col-md-6" style="padding: 0;padding-top: 20px;">
					<div class="input-group">
						<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
	<div class="col-xs-12" style="padding:0px;">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id')?></b></td>
					<td><b><?php echo Lang::get('slave.guess_time');?></b></td>
					<td><b><?php echo Lang::get('slave.active_type');?></b></td>
					<td><b><?php echo Lang::get('slave.get_award_item');?></b></td>
					<td><b><?php echo Lang::get('slave.total_amount');?></b></td>
					<td><b><?php echo Lang::get('slave.blance');?></b></td>
					<td><b><?php echo Lang::get('slave.win_number');?></b></td>
					<td><b><?php echo Lang::get('slave.lose_number');?></b></td>
					<td><b><?php echo Lang::get('slave.store_back');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.time}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.item_id}}</td>
					<td>{{t.total_amount}}</td>
					<td>{{t.blance}}</td>
					<td>{{t.win_number}}</td>
					<td>{{t.lose_number}}</td>
					<td>{{t.store_back}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>