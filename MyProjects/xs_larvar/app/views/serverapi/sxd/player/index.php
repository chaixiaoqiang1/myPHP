<script type="text/javascript">
function SXDSearchServerPlayerController($scope, $http, alertService)
{
	$scope.alerts = [];
	$scope.formData = {};
	$scope.players = [];
	$scope.processFrom = function(url) {
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : url,
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			$scope.players = data;
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
}
</script>

<div class="col-xs-12" ng-controller="SXDSearchServerPlayerController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/sxd/player" method="post" role="form"
				ng-submit="processFrom('/game-server-api/sxd/player')"
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

				<input type="submit" class="btn btn-default"
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

	<div class="row margin-top-10 col-xs-6">
		<div ng-repeat="player in players">
			<div class="panel panel-success">
				<div class="panel-heading"><?php echo Lang::get('player.show_player') ?></div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('player.player_id')?></dt>
						<dd>{{player.player_id}}</dd>
						<dt><?php echo Lang::get('player.name')?></dt>
						<dd>{{player.player_name}}</dd>
						<dt><?php echo Lang::get('player.which_server')?></dt>
						<dd>{{player.server_name}}</dd>
						
						<dt><?php echo Lang::get('player.nickname')?></td>
						<dd>{{player.nickname}}</dd>
						<dt><?php echo Lang::get('player.login_email')?></dt>
						<dd>{{player.login_email}}</dd>
						<dt><?php echo Lang::get('player.uid')?></dt>
						<dd>{{player.uid}}</dd>

						<dt><?php echo Lang::get('player.facebook_id')?></dt>
						<dd>{{player.tp_user_id}}</dd>
						<dt><?php echo Lang::get('player.amount_recharge')?></dt>
						<dd>{{player.all_pay_amount}}</dd>
						<dt><?php echo Lang::get('player.times_recharge')?></dt>
						<dd>{{player.all_pay_times}}</dd>
						<dt><?php echo Lang::get('player.average_rechage')?></dt>
						<dd>{{player.avg_amount}}</dd>
						<dt><?php echo Lang::get('player.first_recharge_level')?></dt>
						<dd>{{player.first_lev}}</dd>
						<?php if (Auth::user()->is_admin || Auth::user()->department_id == Department::ID_SHICHANG) { ?>
						<dt>u1:<dt>
						<dd>{{player.u}}</dd>
						<dt>u2:</dt>
						<dd>{{player.u2}}</dd>
						<dt>source:</dt>
						<dd>{{player.source}}</dd>
						<dt>is_anonymous:</dt>
						<dd>{{player.is_anonymous}}</dd>
						<?php }?>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>