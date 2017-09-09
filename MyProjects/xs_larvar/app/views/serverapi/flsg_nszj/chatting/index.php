<script>
	function gameServerChattingController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/chatting',
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
<div class="col-xs-12" ng-controller="gameServerChattingController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/chatting" method="get" role="form"
				ng-submit="processFrom('/game-server-api/chatting')"
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
				<div class="form-group">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<br /><br/>
				</div>
				<div class="form-group">
					<input type="radio" name="type" ng-model="formData.type" value="1"
						ng-value="1" ng-init="formData.type=1" />
					<?php echo Lang::get('serverapi.search_chatting_friend');?>
					<input type="radio" name="type" ng-model="formData.type" value="2"
						ng-value="2" />
					<?php echo Lang::get('serverapi.search_chatting_record');?>
				</div>

				<div class="form-group" ng-if="formData.type==1">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control"
							ng-model="formData.player_name" name="player_name" required
							placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>" />
					</div>
					<div class="clearfix">
						<br />
					</div>
				</div>
				<div class="form-group" ng-if="formData.type==2">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control"
							ng-model="formData.player_name" name="player_name" required
							placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>" />
					</div>
					<div class="col-md-6" style="padding: 2">
						<input type="text" class="form-control"
							ng-model="formData.to_player_name" name="to_player_name" required
							placeholder="<?php echo Lang::get('serverapi.enter_chatting_player_name') ?>" />
					</div>
					<div class="clearfix"></div>
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
		
		</div>
	</div>
</div>
<div class="row margin-top-10">
	<div class="eb-content">
		<alert ng-repeat="alert in alerts" type="alert.type"
			close="alert.close()">{{alert.msg}}</alert>
	</div>
</div>

<div class="col-xs-12">
	<table class="table table-striped" ng-if="formData.type==1">
		<thead>
			<tr class="info">
				<td><b><?php echo Lang::get('serverapi.chatting_player_name');?></b></td>
				<td><b><?php echo Lang::get('serverapi.chatting_player_id');?></b></td>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="t in total">
				<td>{{t.name}}</td>
				<td>{{t.player_id}}</td>
			</tr>
		</tbody>
	</table>
	<div class="list-group" ng-if="formData.type==2">
		<a href="#" class="list-group-item list-group-item-success">Dapibus ac
			facilisis in</a> <a href="#"
			class="list-group-item list-group-item-info">Cras sit amet nibh
			libero</a> <a href="#"
			class="list-group-item list-group-item-warning">Porta ac consectetur
			ac</a> <a href="#" class="list-group-item list-group-item-danger">Vestibulum
			at eros</a>
	</div>
</div>
</div>