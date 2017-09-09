<script>
	function playerPayData($scope, $http, alertService, $filter){
		$scope.formData = {};
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.process = function(){
			alertService = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : '/game-server-api/player/paydata',
				'data' : $.param($scope.formData),
				'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.items = data;
			}).error(function(){
				alertService.add('danger', data.error);
			})
		}
	}
</script>
<div class="col-xs-12" ng-controller="playerPayData">
	<div class="row">
		<div class="eb-content">
			<form action="game-server-api/player/paydata" method="post" ng-submit="process()" onsubmit="return false;">
				 <div class="form-group" style="height:30px;">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div> 
	<br><br>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped" >
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.poker_rounds_date')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_total_dollar')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_user_num')?></b></td>
					<td><b><?php echo Lang::get('slave.avg_pay_dollar');?></b></td>
					<td><b><?php echo Lang::get('slave.avg_pay_num')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_num1')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_num2')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_num3')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_num4')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_num5')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_num6')?></b></td>
					<td><b><?php echo Lang::get('slave.pay_num7')?></b></td>
					<td><b><?php echo Lang::get('slave.lost_num')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.date}}</td>
					<td>{{t.total_dollar}}</td>
					<td>{{t.pay_day}}</td>
					<td>{{t.avg_dollar}}</td>
					<td>{{t.avg_paunums}}</td>
					<td>{{t.num1}}</td>
					<td>{{t.num2}}</td>
					<td>{{t.num3}}</td>
					<td>{{t.num4}}</td>
					<td>{{t.num5}}</td>
					<td>{{t.num6}}</td>
					<td>{{t.num7}}</td>
					<td>{{t.lost_num}}</td>
				</tr>
			</tbody>
		</table>	
	</div>
</div>