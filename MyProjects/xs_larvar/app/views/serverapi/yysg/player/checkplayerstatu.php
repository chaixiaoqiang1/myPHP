<script>
	function bannedTalkController($scope, $http, alertService) {
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
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="bannedTalkController">
	<div class="row">
		<div class="eb-content">
			<form method="post" role="form"
				ng-submit="processFrom('/game-server-api/yysg/checkAccountStatu')"
				onsubmit="return false;">
				<b>根据玩家昵称查询(昵称和ID都输入则以昵称为准)</b>
				<div class="form-group">
					<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"
						 ng-model="formData.player_name" name="player_name" />
				</div>
				<b>或者根据玩家player_id查询</b>
				<div class="form-group">
					<input type="number" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" />
				</div>
				<input type="submit" class="btn btn-default" style="background:#fa5; color:#333"
					value="<?php echo Lang::get('basic.btn_check') ?>" />
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>玩家昵称</b></td>
					<td><b>玩家player_id</b></td>
					<td><b>操作时间</b></td>
					<td><b>封禁操作</b></td>
					<td><b>操作人</b></td>
					<td><b>操作原因</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.time}}</td>
					<td>{{t.ban_type}}</td>
					<td>{{t.operator}}</td>
					<td>{{t.reason}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>