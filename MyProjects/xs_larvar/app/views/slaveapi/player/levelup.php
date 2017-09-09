<script>
	function getPlayerLevelUpController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];

		$scope.processFrom = function() {
			$scope.items = [];
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/player/levelup',
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
<div class="col-xs-12" ng-controller="getPlayerLevelUpController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="" role="form"
				ng-submit="processFrom('/slave-api/player/levelup')"
				onsubmit="return false;">
				<div class="form-group col-md-12">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-5">
					<input type="text" name="player_name" ng-model="formData.player_name" class="form-control" placeholder="<?php echo Lang::get('slave.player_name')?>" />
				</div>
				<div class="form-group col-md-5">
					<input type="number" name="player_id" ng-model="formData.player_id" class="form-control col-md-4" placeholder="<?php echo Lang::get('slave.player_id')?>" />
				</div>
				<input type="submit" class="btn btn-primary" style="margin-top-10" value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10 col-xs-12">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_old_level');?></b></td>
					<td><b><?php echo Lang::get('slave.player_new_level');?></b></td>
					<td><b><?php echo Lang::get('slave.player_levelup_time');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in items">
				    <td>{{item.old_level}}</td>
				    <td>{{item.new_level}}</td>
				    <td>{{item.levelup_time}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>