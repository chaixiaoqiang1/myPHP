<script>
	function GetVipPlayers($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.number = [];
		$scope.player = [];
		$scope.show = 0;
		$scope.process = function(url){
			$scope.show = 0;
			$scope.number = [];
			$scope.player = [];
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.show = 1;
				$scope.number = data.number;
				$scope.player = data.player;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="GetVipPlayers">
	<div class="row">
	<div class="col-xs-8">
		<form class="form-group" ng-submit="process('/game-server-api/webgame/vipplayer')" onsubmit="return false">
				<div class="form-group">
					<select class="form-control" name="server_ids"
						id="select_game_server" ng-model="formData.server_ids"
						ng-init="formData.server_ids=0" multiple="multiple" ng-multiple="true" size=10 required>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name.' ['.Lang::get('slave.open_server_time').'] '.date("Y-m-d H:i:s",$v->open_server_time);?></option>
						<?php } ?>		
					</select>
				</div>
				<?php if('flsg' == $game_code){?>
					<div class="form-group">
					    <select class="form-control" name="vip_level"
					            ng-model="formData.vip_level"
					      		 multiple="multiple"
					            ng-multiple="true" size=10>
					        <optgroup
					            label="<?php echo Lang::get('serverapi.select_vip_level') ?>">
					            <?php for ($i=1;$i<13;$i++) { ?>
        							<option value="<?php echo $i?>">VIP<?php echo $i?></option>
        						<?php } ?>
					        </optgroup>
					    </select>
					</div>
					<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
				<?php }else{?>
					<input type="number" name="min_vip_level"  id="min_vip_level" ng-model="formData.min_vip_level" placeholder="最低VIP等级(3-12)" style="width:200px" required />
					<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
				<?php }?>
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
		<?php if('flsg' == $game_code){?>
			<table class="table table-striped" ng-if="show ==1">
				<thead>
					<tr class="info">
						<td><b>vip等级</b></td>
						<td><b>符合条件人数</b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="n in number">
						<td>{{n.vip_level}}</td>
						<td>{{n.vipnum}}</td>
					</tr>
				</tbody>
			</table>
		<?php }else{ ?>
			<table class="table table-striped" ng-if="show ==1">
				<thead>
					<tr class="info">
						<td><b>服务器</b></td>
						<td><b>符合条件人数</b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="n in number">
						<td>{{n.server_name}}</td>
						<td>{{n.vipnum}}</td>
					</tr>
				</tbody>
			</table>
		<?php }?>
			<table class="table table-striped" ng-if="show ==1">
				<thead>
					<tr class="info">
						<td><b>服务器</b></td>
						<td><b>玩家ID</b></td>
						<td><b>玩家昵称</b></td>
						<td><b>vip等级</b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="p in player">
						<td>{{p.server_name}}</td>
						<td>{{p.player_id}}</td>
						<td>{{p.player_name}}</td>
						<td>{{p.vip}}</td>
					</tr>
				</tbody>
			</table>
	</div>
</div>