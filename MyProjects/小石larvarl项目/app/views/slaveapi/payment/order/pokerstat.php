<script> 
function getPaymentStatController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.total = {};
    $scope.processFrom = function() {
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/slave-api/poker/payment',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.total = data;
			/*if (!$scope.total['0']) {
				return;
			}
            var choice = 'old_user' in ($scope.total)['0'];
            var list = $('.choice');
            if (choice == false) {
				list.hide();
            } else {
				list.show();
            }*/
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getPaymentStatController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/poker/payment/order" method="get" role="form"
				ng-submit="processFrom('/slave-api/poker/payment/order')"
				onsubmit="return false;">
				
				<div class="form-group" style="height:35px;">
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
				<div class="clearfix">
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
		<table class="table table-striped">
			<thead>
				<tr class="info" id="server">
					<td><?php echo Lang::get("slave.statistics_date");?></td>
					<td><?php echo Lang::get("slave.recharge_money");?></td>
					<td><?php echo Lang::get("slave.recharge_dollar");?></td>
					<td><?php echo Lang::get("slave.recharge_yuanbao");?></td>
					<td><?php echo Lang::get("slave.recharge_number");?></td>
					<td><?php echo Lang::get("slave.recharge_count");?></td>
					<td><?php echo Lang::get("slave.apru_value");?></td>
					<td class="choice"><?php echo Lang::get("slave.day_avtivate");?></td>
					<td class="choice"><?php echo Lang::get("slave.pay_rate");?></td>
					
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td ng-if="t.date">{{t.date}}</td>
					<td ng-if="!t.date">Total</td>
					<td>{{t.total_amount}}</td>
					<td>{{t.total_dollar_amount}}</td>
					<td>{{t.total_yuanbao_amount}}</td>
					<td>{{t.total_user_count}}</td>
					<td>{{t.total_count}}</td>
					<td>{{t.total_amount/t.total_user_count | number:2}}</td>
					<td >{{t.nums}}</td>
					<td>{{t.pay_rate}}</td>
					
				</tr>

			</tbody>
		</table>
	</div> 
</div>