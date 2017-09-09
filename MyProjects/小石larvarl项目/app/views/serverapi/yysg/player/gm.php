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
			<form action="/game-server-api/players/gm" method="post" role="form"
				ng-submit="processFrom('/game-server-api/players/gm')"
				onsubmit="return false;">

				<div class="form-group">
					<?php if($game_code == 'yysg'){ ?>
					<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"
						required ng-model="formData.player_name" name="player_name" />
					<?php } elseif ($game_code == 'mnsg') { ?>
					<input type="text" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>"
						required ng-model="formData.player_id" name="player_id" />
					<?php } ?>
				</div>
				<b>选择新增或删除</b>
				<div class="form-group">
					<select class="form-control" name="is_gm"
							id="is_gm" ng-model="formData.is_gm"
							ng-init="formData.is_gm=1">
							<option value="0">删除GM</option>
							<option value="1">新增GM</option>
					</select>
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