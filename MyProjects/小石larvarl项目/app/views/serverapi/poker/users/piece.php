<script>
	function pokerUserPieceController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url) {
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
<div class="col-xs-12" ng-controller="pokerUserPieceController">
	<div class="row">
		<div class="eb-content">
			<div class="clearfix">
			</div>
			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>"
					ng-click="process('/game-server-api/poker/user-piece')" />
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
	<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><b><?php echo Lang::get('slave.player_id')?></b></td>
				<td><b><?php echo Lang::get('slave.player_name')?></b></td>
				<td><b><?php echo Lang::get('slave.wn');?></b></td>
				<td><b><?php echo Lang::get('slave.up')?></b></td>
				<td><b><?php echo Lang::get('slave.wxjp')?></b></td>
				<td><b><?php echo Lang::get('slave.wj');?></b></td>
				<td><b><?php echo Lang::get('slave.sb');?></b></td>
				<td><b><?php echo Lang::get('slave.dzb');?></b></td>
				<td><b><?php echo Lang::get('slave.Flux');?></b></td>
				<td><b><?php echo Lang::get('slave.PS2');?></b></td>
				<td><b><?php echo Lang::get('slave.ltcj');?></b></td>
				<td><b><?php echo Lang::get('slave.sxpb');?></b></td>
				<td><b><?php echo Lang::get('slave.sxxj');?></b></td>
				<td><b><?php echo Lang::get('slave.sxsj');?></b></td>
				<td><b><?php echo Lang::get('slave.yjds');?></b></td>
			</tr>
		</thead>
		<tbody >
			<tr ng-repeat="t in items">
				<td>{{t.player_id}}</td>	
				<td>{{t.player_name}}</td>
				<td>{{t.wn}}</td>
				<td>{{t.up}}</td>
				<td>{{t.wxjp}}</td>
				<td>{{t.wj}}</td>
				<td>{{t.sb}}</td>
				<td>{{t.dzb}}</td>
				<td>{{t.Flux}}</td>
				<td>{{t.PS2}}</td>
				<td>{{t.ltcj}}</td>
				<td>{{t.sxpb}}</td>
				<td>{{t.sxxj}}</td>
				<td>{{t.sxsj}}</td>
				<td>{{t.yjds}}</td>
			</tr>
		</tbody>
	</table>		
</div>
</div>
