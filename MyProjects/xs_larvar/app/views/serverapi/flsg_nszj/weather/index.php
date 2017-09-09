<script>
	function setWeatherController($scope, $http, alertService) {
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
<div class="col-xs-12" ng-controller="setWeatherController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/weather" method="post" role="form"
				ng-submit="processFrom('/game-server-api/weather')"
				onsubmit="return false;">
				<div class="form-group">
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
				</div>
				<div class="form-group">
					<select class="form-control" name="weather_type"
						id="select_weather_type" ng-model="formData.weather_type"
						ng-init="formData.weather_type=0">
						<option value="0"><?php echo Lang::get('serverapi.select_weather_type') ?></option>
						<option value="1"><?php echo Lang::get('serverapi.leaf') ?></option>
						<option value="2"><?php echo Lang::get('serverapi.rain') ?></option>
						<option value="3"><?php echo Lang::get('serverapi.snow') ?></option>
						<option value="99"><?php echo Lang::get('serverapi.noweather') ?></option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>