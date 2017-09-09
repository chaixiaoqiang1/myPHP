<script type="text/javascript">
function getPokerUserController($scope, $http, alertService) {
	$scope.alerts = [];
	$scope.formData = {};
	$scope.user = {};
	$scope.created_players = [];
	$scope.processFrom = function(url) {
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : url,
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			$scope.user = data.user;
			$scope.operations = data.operations;
			$scope.created_players = data.user.created_players;
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
}
</script>
<div class="col-xs-12" ng-controller="getPokerUserController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/poker/user" method="post" role="form"
				ng-submit="processFrom('/game-server-api/poker/user')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=0">
						<option value="0"><?php echo Lang::get('player.select_by_usernickname') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_uid') ?></option>
						<option value="2"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="3"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="select_value"
						placeholder="<?php echo Lang::get('platformapi.enter') ?>"
						required ng-model="formData.value" name="value" />
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
	<div class="row margin-top-10 col-xs-6" >
		<div class="panel panel-success">
			<div class="panel-heading"><?php echo Lang::get('platformapi.show_user') ?></div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt><?php echo Lang::get('platformapi.user_email')?></dt>
					<dd>{{user.login_email}}</dd>
					<dt><?php echo Lang::get('platformapi.user_uid')?></dt>
					<dd>{{user.uid}}</dd>
					<dt><?php echo Lang::get('platformapi.user_name')?></dt>
					<dd>{{user.name}}</dd>
					<dt><?php echo Lang::get('platformapi.user_nickname')?></dt>
					<dd>{{user.nickname}}</dd>
					<dt><?php echo Lang::get('platformapi.user_player_name')?></dt>
					<dd>{{user.player_name}}</dd>
					<dt><?php echo Lang::get('platformapi.player_id')?></dt>
					<dd>{{user.player_id}}</dd>
					<dt><?php echo Lang::get('platformapi.user_contact_email')?></dt>
					<dd>{{user.contact_email}}</dd>
					<dt><?php echo Lang::get('platformapi.user_created_time')?></dt>
					<dd>{{user.created_time}}</dd>
					<dt><?php echo Lang::get('platformapi.user_last_visit_time')?></dt>
					<dd>{{user.last_visit_time}}</dd>
					<dt><?php echo Lang::get('platformapi.user_created_ip')?></dt>
					<dd>{{user.created_ip}}</dd>
					<dt><?php echo Lang::get('platformapi.user_last_visit_ip')?></dt>
					<dd>{{user.last_visit_ip}}</dd>
					<dt><?php echo Lang::get('platformapi.all_pay_amount')?></dt>
					<dd>{{user.all_pay_amount}}</dd>
					<dt><?php echo Lang::get('platformapi.all_pay_times')?></dt>
					<dd>{{user.all_pay_times}}</dd>
					<dt><?php echo Lang::get('platformapi.avg_pay_amount')?></dt>
					<dd>{{user.avg_pay_amount}}</dd>
					<dt><?php echo Lang::get('platformapi.first_lev')?></dt>
					<dd>{{user.first_lev}}</dd>
					<?php if (Auth::user()->is_admin || Auth::user()->department_id == Department::ID_SHICHANG) { ?>
					<dt>u1:
					<dt>
					<dd>{{user.u}}</dd>
					<dt>u2:</dt>
					<dd>{{user.u2}}</dd>
					<dt>source:</dt>
					<dd>{{user.source}}</dd>
					<dt>is_anonymous:</dt>
					<dd>{{user.is_anonymous}}</dd>
					<?php }?>
				</dl>
			</div>
		</div>
	</div>
	<div class="row margin-top-10 col-xs-6" >
		<div class="panel panel-success">
			<div class="panel-heading"><?php echo Lang::get('platformapi.show_game') ?></div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt><?php echo Lang::get('platformapi.lev')?></dt>
					<dd>{{user.lev}}</dd>
					<dt><?php echo Lang::get('platformapi.chips')?></dt>
					<dd>{{user.chips}}</dd>
					<dt><?php echo Lang::get('platformapi.gold')?></dt>
					<dd>{{user.gold}}</dd>
					<dt><?php echo Lang::get('platformapi.online')?></dt>
					<dd>{{user.online}}</dd>
					<dt><?php echo Lang::get('platformapi.room_id')?></dt>
					<dd>{{user.room_id}}</dd>
					<dt><?php echo Lang::get('platformapi.firend_num')?></dt>
					<dd>{{user.firend_num}}</dd>
					<dt><?php echo Lang::get('platformapi.max_chips')?></dt>
					<dd>{{user.max_chips}}</dd>
					<!--<dt><?php echo Lang::get('platformapi.win_times')?></dt>
					<dd>{{user.win_times}}</dd>-->
					<dt><?php echo Lang::get('platformapi.play_times')?></dt>
					<dd>{{user.play_times}}</dd>
					<dt><?php echo Lang::get('platformapi.vip')?></dt>
					<dd>{{user.vip}}</dd>
					<dt><?php echo Lang::get('platformapi.vip_lev')?></dt>
					<dd>{{user.vip_lev}}</dd>
					<dt><?php echo Lang::get('platformapi.vip_exp')?></dt>
					<dd>{{user.vip_exp}}</dd>
					<!--<dt><?php echo Lang::get('platformapi.lock_chips')?></dt>
					<dd>{{user.lock_chips}}</dd>-->
					<dt><?php echo Lang::get('platformapi.is_recharge')?></dt>
					<dd>{{user.is_recharge}}</dd>
					<dt><?php echo Lang::get('platformapi.strongbox_chips')?></dt>
					<dd>{{user.strongbox_chips}}</dd>
					<dt><?php echo Lang::get('platformapi.strongbox_password')?></dt>
					<dd>{{user.strongbox_password}}</dd>
					<dt><?php echo Lang::get('platformapi.freeze_state')?></dt>
					<dd>{{user.is_ban}}</dd>
					<dt><?php echo Lang::get('platformapi.allin_rate')?></dt>
					<dd>{{user.allin_rate}}</dd>
					<!--<dt><?php echo Lang::get('platformapi.is_double_exp')?></dt>
					<dd>{{user.is_double_exp}}</dd>
					<dt><?php echo Lang::get('platformapi.double_exp_end_time')?></dt>
					<dd>{{user.double_exp_end_time}}</dd>-->
				</dl>
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-8" style="max-height:600px;overflow:auto">
			<table class="table table-striped">
				<tbody>
					<tr ng-repeat="o in operations">
						<td>{{o.username}}</td>
						<td>{{o.time}}</td>
						<td>{{o.msg}}</td>
					</tbody>
			</table>
		</div>
	</div>
</div>