<script> 
function getPlayerInfo($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.items = {};
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/slave-api/player/info/like',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.items = {};
            $scope.items = data;
        }).error(function(data) {
        	$scope.items = {};
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getPlayerInfo">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0">选择服务器(可不选，选择查询更准确)</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="type"
						id="type" ng-model="formData.type"
						ng-init="formData.type=0">
						<option value="0">查询玩家昵称</option>	
						<option value="1">查询玩家player_id</option>	
						<option value="2">查询玩家uid</option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="输入玩家信息的部分即可"
						required ng-trim="false" ng-model="formData.id_or_name" name="id_or_name" />
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td>服务器</td>
					<td>昵称</td>
					<td>player_id</td>
					<td>uid</td>
					<td>创建时间</td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
				<?php if('yysg' == Game::find(Session::get('game_id'))->game_code) {?>
					<td>夜夜三国</td>
				<?php }else{ ?>
					<td>{{t.server_track_name}}</td>
				<?php } ?>
					<td>{{t.player_name}}</td>

				<?php if(( 'yysg' == Game::find(Session::get('game_id'))->game_code || 'mnsg' == Game::find(Session::get('game_id'))->game_code)) {?>
					<td><a href="/game-server-api/yysg/player?server_init={{formData.server_id == 0? t.server_id :formData.server_id }}&player_id={{t.player_id}}" target="view_window">{{t.player_id}}</a></td>
				<?php }else{ ?>
					<td><a href="/game-server-api/player?server_init={{formData.server_id == 0? t.server_id :formData.server_id}}&player_id={{t.player_id}}" target="view_window">{{t.player_id}}</a></td>
				<?php } ?>

					<td>{{t.uid}}</td>
					<td>{{t.created_time}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>