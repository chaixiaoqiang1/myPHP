<script>
	function PokerRoundsRangeController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.start_time = null;
    	$scope.end_time = null;
		$scope.pagination={};
		//分页
		$scope.pagination.totalItems = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.perPage = 1;
		$scope.$watch('pagination.currentPage', function(newPage, oldPage){
			if ($scope.end_time > 0) {
				$scope.process(newPage);
			}
		})
		$scope.process = function(newPage){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time,'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : '/game-server-api/poker/rounds-range?page=' + newPage,
				'data' :$.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.perPage= data.per_page;
				$scope.pagination.totalItems = data.count;
				$scope.items = data.items;
				location.hash = '#top';
			}).error(function(data){
				alertService.add('danger', data.error);
			});		
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerRoundsRangeController">
	<div class="row">
		<div class="eb-content">
			<form class="form-group" ng-submit="process(1)" onsubmit="return false" role="form">
				<div class="form-group" style="padding:0px">
					<input type="text" class="form-control" ng-model="formData.player_name" name="player_name" placeholder="<?php echo Lang::get('serverapi.enter_player_name')?>">
				</div>
				<div class="form-group">
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
	<div class="col-xs-12" style="padding: 0;">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get("slave.room_id");?></b></td>
					<td><b><?php echo Lang::get("slave.rule_id");?></b></td>
					<td><b><?php echo Lang::get("slave.period");?></b></td>
					<td><b><?php echo Lang::get("slave.dealer");?></b></td>
					<td><b><?php echo Lang::get("slave.public_cards");?></b></td>
					<td><b><?php echo Lang::get("slave.bet_pools");?></b></td>
					<td><b><?php echo Lang::get("slave.players");?></b></td>
					<td><b><?php echo Lang::get("slave.seat_chips");?></b></td>
					<td><b><?php echo Lang::get("slave.time");?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.room_id}}</td>
					<td>{{t.rule_id}}</td>
					<td>{{t.period}}</td>
					<td>{{t.dealer}}</td>
					<td style="white-space: nowrap">{{t.public_cards}}</td>
					<td>{{t.bet_pools}}</td>
					<td>{{t.players}}</td>
					<td>{{t.seat_chips}}</td>
					<td>{{t.time}}</td>
				</tr>
				</tbody>
		
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>