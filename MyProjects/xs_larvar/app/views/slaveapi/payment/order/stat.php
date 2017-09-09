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
            'url': '/slave-api/payment/order/stat',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.total = data.order;
            $scope.refund = data.refund;
			if (!$scope.total['0']) {
				return;
			}
            var choice = 'old_user' in ($scope.total)['0'];
            var list = $('.choice');
            if (choice == false) {
				list.hide();
            } else {
				list.show();
            }
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getPaymentStatController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/payment/order/stat" method="get" role="form"
				ng-submit="processFrom('/slave-api/payment/order/stat')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('slave.show_all_servers') ?>区分时间但不区分服务器</option>
						<option value="-1"><?php echo Lang::get('slave.show_all_servers') ?>区分服务器且区分时间(忠告:请勿选择过长时间段)</option>
						<option value="-2"><?php echo Lang::get('slave.show_all_servers') ?>区分服务器但不区分时间</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
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
					<td><?php echo Lang::get("slave.recharge_money");?>(<?php echo $currency_code; ?>)</td>
					<td><?php echo Lang::get("slave.recharge_dollar");?></td> <!----(当日充值美元未统计退款) -->
					<td><?php echo Lang::get("slave.recharge_yuanbao");?></td>
					<td><?php echo Lang::get("slave.yuanbao_per_money");?>(<?php echo Lang::get("slave.yuanbao");?>/<?php echo $currency_code; ?>)</td>
					<td><?php echo Lang::get("slave.recharge_number");?></td>
					<td><?php echo Lang::get("slave.recharge_count");?></td>
					<td><?php echo Lang::get("slave.apru_value");?></td>

					<td class="choice"><?php echo Lang::get("slave.old_user_recharge_money");?></td>
					<td class="choice"><?php echo Lang::get("slave.old_user_recharge_dollar");?></td>
					<td class="choice"><?php echo Lang::get("slave.old_user_recharge_yuanbao");?></td>
					<td class="choice"><?php echo Lang::get("slave.old_user_recharge_number");?></td>
					<td class="choice"><?php echo Lang::get("slave.old_user_recharge_count");?></td>
					<td class="choice"><?php echo Lang::get("slave.old_user_apru_value");?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td ng-if="t.date">{{t.date}}</td>
					<td ng-if="!t.date">Total</td>
					<td>{{t.total_amount}}</td>
					<td>{{t.total_dollar_amount}}</td>
					<td>{{t.total_yuanbao_amount}}</td>
					<td>{{t.total_yuanbao_amount / t.total_amount | number : 3}}</td>
					<td>{{t.total_user_count}}</td>
					<td>{{t.total_count}}</td>
					<td>{{t.total_dollar_amount/t.total_user_count | number:2}}</td>

					<td ng-if="t.old_user.total_amount >= 0">{{t.old_user.total_amount}}</td>
					<td ng-if="t.old_user.total_dollar_amount >= 0">{{t.old_user.total_dollar_amount}}</td>
					<td ng-if="t.old_user.total_yuanbao_amount >= 0">{{t.old_user.total_yuanbao_amount}}</td>
					<td ng-if="t.old_user.total_user_count >= 0">{{t.old_user.total_user_count}}</td>
					<td ng-if="t.old_user.total_user_count >= 0">{{t.old_user.total_count}}</td>
					<td ng-if="t.old_user.total_dollar_amount >= 0">{{t.old_user.total_dollar_amount/t.old_user.total_user_count | number:2}}</td>
				</tr>

			</tbody>
		</table>
	</div>
	<div class="eb-content">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><?php echo Lang::get("slave.statistics_date");?></td>
					<td><?php echo Lang::get("slave.refund_amount_dollar");?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="r in refund">
					<td>{{r.refund_date}}</td>
					<td>{{r.refund_amount}}</td>
				</tr>
			</tbody>
		</table>
	</div> 
</div>