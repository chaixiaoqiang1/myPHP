<script>
	function PokerRechargeCount($scope, $http, alertService){
		$scope.alerts = [];
		$scope.formData = {};
		/*$scope.pagination = {};
		//pagination
		$scope.pagination.totalItems = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.perPage= 30;

		$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
			$scope.processFrom(newPage);
		});*/

		$scope.process = function(){
			alertService = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : '/game-server-api/poker/recharge-info',
				'data' :$.param($scope.formData),
				'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(data){
				/*$scope.pagination.currentPage = data.current_page;
				$scope.pagination.perPage= data.per_page;
				$scope.pagination.totalItems = data.count;*/
				$scope.items = data;
				//location.hash = '#top';
			}).error(function(){
				alertService.add('danger', data.error);
			})
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerRechargeCount">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/poker/recharge-info" method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
					
					<input type="text" name="count" ng-model="formData.count" value = "" class="form-control" required  placeholder="<?php echo Lang::get('serverapi.total_pay')?>"/>
				</div>
				
				<div class="form-group">
					<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-default"/> 
				</div>
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
					<td><b><?php echo Lang::get("slave.pay_user_id");?></b></td>
					<td><b><?php echo Lang::get("slave.pay_user_name");?></b></td>
					<td><b><?php echo Lang::get("slave.pay_user_idd");?></b></td>
					<td><b><?php echo Lang::get("slave.total_pay");?></b></td>
					<td><b><?php echo Lang::get("slave.pay_count");?></b></td>
					<td><b><?php echo Lang::get("slave.first_pay");?></b></td>
					<td><b><?php echo Lang::get("slave.last_pay");?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.pay_user_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.total_pay}}</td>
					<td>{{t.count}}</td>
					<td>{{t.first_pay}}</td>
					<td>{{t.last_pay}}</td>
				</tr>
			</tbody>
		</table>
		<!--<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>-->
	</div>
</div>