<script>
	function getEconomyRankController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/rank',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getEconomyRankController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/economy/rank" method="get" role="form"
				ng-submit="processFrom('/slave-api/economy/rank')"
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
				<div class="form-group" style="padding-top: 15px; width: 200px;">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('slave.yuanbao')?></option>
						<option value="1"><?php echo Lang::get('slave.tongqian')?></option>
						<option value="2"><?php echo Lang::get('slave.gongxun')?></option>
					</select>
				</div>
				<input type="submit" class="btn btn-default" style=""
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

	<div class="col-xs-12">
		<table class="table table-striped table-hover">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.consumption_statics');?></b></td>
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td>{{t.spend}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>