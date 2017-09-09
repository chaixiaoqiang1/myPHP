<script>
	function DressWingController($scope, $http, alertService) {
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
<div class="col-xs-12" ng-controller="DressWingController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/dress/wing" method="post" role="form"
				ng-submit="processFrom('/game-server-api/dress/wing')"
				onsubmit="return false;">
				<div class="form-group" style="height:30px;">
						<select class="form-control" name="server_id"
							id="select_game_server" ng-model="formData.server_id"
							ng-init="formData.server_id=0">
							<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
					</div>
					<div class="form-group" style="height:30px;">
						<select class="form-control" name="choice"
							id="choice" ng-model="formData.choice"
							ng-init="formData.choice=0">
							<option value="0"><?php echo Lang::get('serverapi.dress_add') ?></option>
							<option value="1"><?php echo Lang::get('serverapi.dress_remove') ?></option>
					</select>
				</div>
				<div class="form-group" style="height:30px;">
				<div class="col-md-6" style="padding: 0">
				<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"
						 ng-model="formData.player_name" name="player_name" />
				</div>
				<div class="col-md-6" style="padding: 0">
					<input type="text" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" />
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
</div>