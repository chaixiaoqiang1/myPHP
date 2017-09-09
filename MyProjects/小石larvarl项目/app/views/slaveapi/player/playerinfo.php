<script>
	function getCreatePlayerInfoController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/player/playerinfo',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.user = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getCreatePlayerInfoController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/player/playerinfo" method="get" role="form"
				ng-submit="processFrom('/slave-api/player/playerinfo')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" >
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
				<div class="col-md-4">
						 <input type="text" class="form-control"
								ng-model="formData.uid" name="uid"
								placeholder="<?php echo Lang::get('serverapi.enter_uid') ?>" />
					</div>
					
					<div class="col-md-4">
						 <input type="text" class="form-control"
								ng-model="formData.player_id" name="player_id"
								placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
					</div>
					<div class="col-md-4">
						 <input type="text" class="form-control"
								ng-model="formData.player_name" name="player_name"
								placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>" />
					</div>
					</div>
				<div class='clearfix'><br /></div>
				<input type="submit" class="btn btn-default" style="margin-top-10" value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="row margin-top-10 col-xs-6">
		<div class="panel panel-success">
			<div class="panel-heading"><?php echo Lang::get('slave.create_player_info') ?></div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt><?php echo Lang::get('slave.create_player_uid')?></dt>
					<dd>{{user.user_id}}</dd>
					<dt><?php echo Lang::get('slave.create_player_player_id')?></dt>
					<dd>{{user.player_id}}</dd>
					<dt><?php echo Lang::get('slave.create_player_player_name')?></dt>
					<dd>{{user.player_name}}</dd>
				</dl>
			</div>
		</div>
	</div>
</div>