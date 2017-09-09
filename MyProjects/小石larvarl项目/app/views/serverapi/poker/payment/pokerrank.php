<script>
	function getPokerEconomyRankController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/user-rank',
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
<div class="col-xs-12" ng-controller="getPokerEconomyRankController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/poker/user-rank" method="get" role="form"
				ng-submit="processFrom('/slave-api/poker/user-rank')"
				onsubmit="return false;">
				<div class="form-group" style="padding-top: 15px; width: 200px;">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('slave.chouma')?></option>
						<option value="1"><?php echo Lang::get('slave.jinbi')?></option>
					</select>
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
		<table class="table table-striped table-hover">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.consumption_statics');?></b></td>
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td>{{t.spend}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>