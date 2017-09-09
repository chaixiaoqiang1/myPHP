<script>
	function PokerGamesLogController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
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
<div class="col-xs-12" ng-controller="PokerGamesLogController">
	<div class="row">
		<form class="form-group" ng-submit="process('/game-server-api/poker/same_ip')" onsubmit="return false">
			<div class="form-group" style="padding:0px">
					<input type="text" class="form-control" ng-model="formData.ip" name="ip" placeholder="<?php echo Lang::get('serverapi.enter_ip')?>">
				</div>
			<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
		</form>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" style="padding:0">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('serverapi.ip')?></b></td>
					<td><b><?php echo Lang::get('serverapi.playerid')?></b></td>
					<td><b><?php echo Lang::get('serverapi.gm_player_name')?></b></td>
					<td><b><?php echo Lang::get('serverapi.times')?></b></td>
					<td><b><?php echo Lang::get('serverapi.ip_type')?></b></td>
					<td><b><?php echo Lang::get('serverapi.player_tongqian')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.ip}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.time}}</td>
					<td>{{t.type}}</td>
					<td>{{t.tongqian}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>