<script type="text/javascript">
function qqLoginMasterController($scope, $http, alertService)
{
	$scope.alerts = [];
	$scope.formData = {};
	$scope.players = [];
// 	$scope.redirect = function(){
// 		window.open('http://s4.app100730579.qqopenapp.com/login_master');
// 	};
	$scope.processFrom = function(url) {
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : url,
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alertService.add('success', data.tp_user_id);
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
}
</script>
<div class="col-xs-12" ng-controller="qqLoginMasterController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/player" method="post" role="form"
				ng-submit="processFrom('/game-server-api/player/qq-loginmaster')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>

				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=0">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="<?php echo Lang::get('player.enter_id_or_name') ?>"
						required ng-model="formData.id_or_name" name="id_or_name" />
				</div>

				<div class="col-md-4" style="padding: 0">
				<input type="submit" class="btn btn-primary"
					value="<?php echo Lang::get('serverapi.get_openid') ?>" />
				</div>
				<div class="col-md-4" style="padding: 30">
					<input type='button' class="btn btn-warning"
						value="<?php echo Lang::get('serverapi.enter_qq_login_master') ?>"
						onclick="window.open('http://s4.app100730579.qqopenapp.com/login_master')" />
				</div>
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