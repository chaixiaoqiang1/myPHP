<script>
	function PokerRefundController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.start_time = null;//没用
    	$scope.end_time = null;//没用
		$scope.pagination={};
		//分页
		//pagination分页插件
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
				'url' : '/game-server-api/poker/refund?page=' + newPage,
				'data' :$.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.perPage= data.per_page;
				$scope.pagination.totalItems = data.count;
				$scope.items = data.items;
				//location.hash = '#top';
			}).error(function(data){
				alertService.add('danger', data.error);
			});		
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerRefundController">
	<div class="row">
		<div class="eb-content">
			<form class="form-group" ng-submit="process(1)" onsubmit="return false" role="form">
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
					<td><b><?php echo Lang::get("slave.order_sn");?></b></td>
					<td><b><?php echo Lang::get("slave.refundable_amount");?></b></td>
					<td><b><?php echo Lang::get("slave.create_time");?></b></td>
					<td><b><?php echo Lang::get("slave.status");?></b></td>
					<td><b><?php echo Lang::get("slave.refund_time");?></b></td>
					<td><b><?php echo Lang::get("slave.currency");?></b></td>
					<td><b><?php echo Lang::get("slave.user_name");?></b></td>
					<td><b><?php echo Lang::get("slave.user_fb_id");?></b></td>
					<td><b><?php echo Lang::get("slave.refund_amount");?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.order_sn}}</td>
					<td>{{t.refundable_amount}}</td>
					<td>{{t.create_time}}</td>
					<td>{{t.status}}</td>
					<td>{{t.refund_time}}</td>
					<td>{{t.currency}}</td>
					<td>{{t.user_name}}</td>
					<td>{{t.user_fb_id}}</td>
					<td>{{t.refund_amount}}</td>
				</tr>
				</body>
		
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>