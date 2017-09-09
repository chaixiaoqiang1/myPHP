<script>
	function PokerGameAreaController($http, $scope, alertService, $filter){
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
<div class="col-xs-12" ng-controller="PokerGameAreaController">
	<div class="row">
	<div class="eb-content">
		<form class="form-group" ng-submit="process('/game-server-api/poker/game_area')" onsubmit="return false">
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
	<div class="col-xs-12">
		<div class="panel panel-success">
				<div class="form-group">
					<div class="col-md-6" style="padding:0;width:8%">
						<table class="table table-striped">
							<thead>
								<tr class="info">
									<td>游戏场</td>
								</tr>
							</thead>
							<tbody>
								<tr style="padding: 0">
									<td>玩牌人数</td>
								</tr>
								<tr style="padding: 0">
									<td>玩牌局数</td>
								</tr>
								<tr style="padding: 0">
									<td>回收筹码</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-md-6" style="padding:0;width:92%">
						<table class="table table-striped">
							<thead>
								<tr class="info">
									<td>Total</td>
										<td>10-20</td>
										<td>20-40</td>
										<td>40-50</td>
										<td>200-400</td>
										<td>500-1000</td>
										<td>1000-2000</td>
										<td>2000-4000</td>
										<td>2500-5000</td>
										<td>5000-10000</td>
										<td>10000-20000</td>
										<td>20000-40000</td>
										<td>25000-50000</td>
										<td>50000-100000</td>
										<td>200000-400000</td>
										<td>500000-1000000</td>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="t in items">
									<td>{{t.total}}</td>
									<td>{{t.num1}}</td>
									<td>{{t.num2}}</td>
									<td>{{t.num3}}</td>
									<td>{{t.num4}}</td>
									<td>{{t.num5}}</td>
									<td>{{t.num6}}</td>
									<td>{{t.num7}}</td>
									<td>{{t.num8}}</td>
									<td>{{t.num9}}</td>
									<td>{{t.num10}}</td>
									<td>{{t.num11}}</td>
									<td>{{t.num12}}</td>
									<td>{{t.num13}}</td>
									<td>{{t.num14}}</td>
									<td>{{t.num15}}</td>
				
								</tr>
							</tbody>
						</table>
					</div>
				</div>	
	</div>
