<script>
	function SXDSendGiftController($scope, $http, alertService)
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
				console.log(data.result);
				var result = data.result;
					if (result.status == 'ok') {
						alertService.add('success', result.msg);
					} else if (result.status == 'error') {
						alertService.add('danger', result.msg);
					}
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="SXDSendGiftController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/sxd/send-gift" method="post"
				role="form"
				ng-submit="processFrom('/game-server-api/sxd/send-gift')"
				onsubmit="return false;">

				<div class="form-group">
					<select class="form-control" name="server_id" id="server_id"
						ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="gift_bag_id" id="gift_bag_id"
						ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
				<div class="col-md-6" style="padding: 0">
					<input type="text" class="form-control" ng-model="formData.player_name"
						name="player_name"
						placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>" />
					</div>
					<div class="col-md-6" style="padding: 2">
					<input type="text" class="form-control" ng-model="formData.player_id"
						name="player_id"
						placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
					</div>
				</div>
				<div class = 'clearfix'>
				<br />
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