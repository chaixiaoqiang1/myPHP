<script>
	function setGMController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="setGMController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/player/gm" method="post" role="form"
				ng-submit="processFrom('/game-server-api/player/gm')"
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
					<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"
						required ng-model="formData.player_name" name="player_name" />
				</div>
				<div class="form-group">
					<label>
						<input type="checkbox" ng-init="formData.is_gm=0" ng-true-value="1" ng-false-value="0" ng-model="formData.is_gm"/>
						<?php echo Lang::get('player.is_gm') ?>
					</label>
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
</div>