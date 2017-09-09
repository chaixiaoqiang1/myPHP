<script>
	function PokerMatchRankController($http, $scope, alertService, $filter){
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
<div class="col-xs-12" ng-controller="PokerMatchRankController">
	<div class="row">
	<div class="eb-content">
		<form class="form-group" ng-submit="process('/game-server-api/poker/match_rank')" onsubmit="return false">
			<div class="form-group" style="padding:0px;height: 30px;">
					<input type="text" class="form-control" ng-model="formData.player_id" name="player_id" placeholder="<?php echo Lang::get('serverapi.enter_player_id')?>">
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
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" style="padding:0">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('serverapi.match_type')?></b></td>
					<td><b><?php echo Lang::get('serverapi.match_field')?></b></td>
					<td><b><?php echo Lang::get('serverapi.rank')?></b></td>
					<td><b><?php echo Lang::get('serverapi.get_tongqian')?></b></td>
					<td><b><?php echo Lang::get('serverapi.get_token')?></b></td>
					<td><b><?php echo Lang::get('serverapi.get_fragment')?></b></td>
					<td><b><?php echo Lang::get('serverapi.get_googs')?></b></td>
					<td><b><?php echo Lang::get('serverapi.get_integral')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.match_type}}</td>
					<td>{{t.match_id}}</td>
					<td>{{t.rank}}</td>
					<td>{{t.get_tongqian}}</td>
					<td>{{t.get_token}}</td>
					<td>{{t.get_fragment}}</td>
					<td>{{t.get_googs}}</td>
					<td>{{t.get_integral}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>