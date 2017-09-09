<script>
	function PlayerEquipmentController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.show = 0;
		$scope.process = function() {
			$scope.get = {};
			$scope.powerup = {};
			$scope.equip = {};
			$scope.sell = {};
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/log/equipment',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
				$scope.get = data.get;
				$scope.powerup = data.powerup;
				$scope.equip = data.equip;
				$scope.sell = data.sell;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PlayerEquipmentController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=0">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="<?php echo Lang::get('slave.id_or_name'); ?>"
						required ng-trim="false" ng-model="formData.id_or_name" name="id_or_name" />
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
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" 
						ng-click="process()"/>
					</div>
			</div>
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
					<td colspan="8"><b><?php echo Lang::get('slave.equipment_get'); ?></b></td>
				</tr>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
					<td><b>MID</b></td>
					<td><b><?php echo Lang::get('slave.rune_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.equipment'); ?></b></td>
					<td><b><?php echo Lang::get('slave.star'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rarity'); ?></b></td>
					<td><b><?php echo Lang::get('slave.attr'); ?></b></td>
					<td><b><?php echo Lang::get('slave.create_at'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in get">
					<td>{{t.player_id}}</td>
					<td>{{t.mid}}</td>
					<td>{{t.rune_id}}</td>
					<td>{{t.table_id}}</td>
					<td>{{t.star}}</td>
					<td>{{t.rarity}}</td>
					<td>{{t.attr}}</td>
					<td>{{t.created_at}}</td>
				</tr>
			</tbody>
		</table>

		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td colspan="9"><b><?php echo Lang::get('slave.equipment_powerup'); ?></b></td>
				</tr>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rune_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.equipment'); ?></b></td>
					<td><b><?php echo Lang::get('slave.lev'); ?></b></td>
					<td><b><?php echo Lang::get('slave.star'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rarity'); ?></b></td>
					<td><b><?php echo Lang::get('slave.attr_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.attr_value'); ?></b></td>
					<td><b><?php echo Lang::get('slave.create_at'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in powerup">
					<td>{{t.player_id}}</td>
					<td>{{t.rune_id}}</td>
					<td>{{t.table_id}}</td>
					<td>{{t.lev}}</td>
					<td>{{t.star}}</td>
					<td>{{t.rarity}}</td>
					<td>{{t.attr_id}}</td>
					<td>{{t.attr_value}}</td>
					<td>{{t.created_at}}</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td colspan="12"><b><?php echo Lang::get('slave.equipment_equip'); ?></b></td>
				</tr>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.partner'); ?></b></td>
					<td><b><?php echo Lang::get('slave.slot'); ?></b></td>
					<td><b><?php echo Lang::get('slave.on'); ?></b></td>
					<td><b><?php echo Lang::get('slave.star'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rarity'); ?></b></td>
					<td><b><?php echo Lang::get('slave.attr'); ?></b></td>
					<td><b><?php echo Lang::get('slave.off').Lang::get('slave.on_off'); ?></b></td>
					<td><b><?php echo Lang::get('slave.star'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rarity'); ?></b></td>
					<td><b><?php echo Lang::get('slave.attr'); ?></b></td>
					<td><b><?php echo Lang::get('slave.create_at'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in equip">
					<td>{{t.player_id}}</td>
					<td>{{t.partner_id}}</td>
					<td>{{t.slot}}</td>
					<td>{{t.on_id}}</td>
					<td>{{t.on_star}}</td>
					<td>{{t.on_rarity}}</td>
					<td>{{t.on_attr}}</td>
					<td>{{t.off_id}}</td>
					<td>{{t.off_star}}</td>
					<td>{{t.off_rarity}}</td>
					<td>{{t.off_attr}}</td>
					<td>{{t.created_at}}</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td colspan="7"><b><?php echo Lang::get('slave.equipment_sell'); ?></b></td>
				</tr>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rune_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.sell_equipment'); ?></b></td>
					<td><b><?php echo Lang::get('slave.star'); ?></b></td>
					<td><b><?php echo Lang::get('slave.rarity'); ?></b></td>
					<td><b><?php echo Lang::get('slave.attr'); ?></b></td>
					<td><b><?php echo Lang::get('slave.create_at'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in sell">
					<td>{{t.player_id}}</td>
					<td>{{t.rune_id}}</td>
					<td>{{t.rune_table_id}}</td>
					<td>{{t.star}}</td>
					<td>{{t.rarity}}</td>
					<td>{{t.attr}}</td>
					<td>{{t.created_at}}</td>
				</tr>
			</tbody>
		</table>		
	</div>
</div>