<script>
	function PlayerLoginController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time,'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.items = data;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PlayerLoginController">
	<div class="row">
	<div class="eb-content">
		<form class="form-group" ng-submit="process('/game-server-api/player/login')" onsubmit="return false">
			<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=<?php echo $server_init==0?0:$server_init ?>">
						<option value="0"><?php echo Lang::get('slave.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=<?php echo $server_init==''?0:1 ?>">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
			<div class="form-group">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="<?php echo Lang::get('player.enter_id_or_name') ?>"
						required ng-model="formData.id_or_name" name="id_or_name" 
						ng-init="formData.id_or_name=<?php echo $player_id==''?'':$player_id ?>" />
				</div>
			<div class="form-group" style="height: 30px;">
			<div class="col-md-6" style="padding-left: 0px ;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding-left:15px;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
			<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
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
		<table class="table table-striped" style="width:50%;">
			<thead>
				<tr class="info">
					<td><b>时间</b></td>
					<td><b>状态</b></td>
					<td><b>玩家等级</b></td>
					<td><b>登录IP</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.time}}</td>
					<td>{{t.statu}}</td>
					<td>{{t.lev}}</td>
					<td>{{t.last_ip}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>