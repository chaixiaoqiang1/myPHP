<script >
	function updateBosslivesController($scope, $http, alertService, $filter)
	{
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
				if (data.error == "没有数据") {
					alertService.add('danger', data.error);
				}else{
					$scope.items = data;
				}
				//alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});

		};
	}
</script>
<div class="col-xs-12" ng-controller="updateBosslivesController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="processFrom('/game-server-api/boss/lives')" onsubmit="return false;">
				<div class="well">
					<label><?php echo Lang::get('serverapi.select_game_server') ?></label>
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.servers_id"
						ng-init="formData.servers_id=0" multiple="multiple"
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
					<input type="radio" name="boss_type"
							ng-model="formData.boss_type" value="1" ng-value="1"
							ng-init="formData.boss_type=1" />
					<?php echo Lang::get('serverapi.noon');?>
					<input type="radio" name="boss_type" style="margin-left:20px"
							ng-model="formData.boss_type" value="2" ng-value="2" />
					<?php echo Lang::get('serverapi.ning');?>
					<input type="radio" name="boss_type" style="margin-left:20px"
							ng-model="formData.boss_type" value="3" ng-value="3" />
					<?php echo Lang::get('serverapi.xianjie');?>
					<input type="number" style="margin-left:20px"
							ng-model="formData.times" ng-init="formData.times=3" />
					<input type="submit" class="btn btn-success" style="margin-left:50px; width:100px; font-weight:bold;"
						value=" <?php echo Lang::get('basic.btn_change') ?> " />
				</div>
				
			</form>
			<p><?php echo Lang::get('serverapi.boss_advice') ?></p>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10 col-xs-6">
		<div class="panel panel-info" ng-repeat="t in items">
				<div class="panel-heading">
					<span >Server Name: {{t['server_name']}}</span>
					<span style="position: absolute; left: 50%;">BOSS: {{t['boss']}}</span>
					<span style="position: absolute; right: 10%">Lives: {{t['times']}}</span>
				</div>
				<div class="panel-body">
					<span >Check from {{t['server_name']}} result: </span>
					<span style="position: absolute; left: 50%;">BOSS: {{t['boss']}}</span>
					<span style="position: absolute; right: 10%">Lives: {{t['times']}}</span>
				</div>
		</div>
	</div>
</div>