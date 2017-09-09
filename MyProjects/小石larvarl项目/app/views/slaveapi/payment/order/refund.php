<script> 

function getRefundOrderController($scope, $http, alertService, $modal, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.orders = {};
	$scope.start_time = null;
	$scope.end_time = null;
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/payment/order/refund',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.orders = data;
            
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getRefundOrderController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/payment/order/refund" method="get"
				role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group col-md-4" style="padding:0;margin-right:10px;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_order_number')?>"
						ng-model="formData.order_sn" name="order_sn"?>
				</div>
				<div class="form-group col-md-4" style="padding:0;">
					<select class="form-control" name="pay_type_id" ng-model="formData.pay_type_id" ng-init="formData.pay_type_id=0">
					<option value="0"><?php echo Lang::get('slave.all_pay_types');?></option>
					<?php foreach(PayType::currentPlatform()->get() as $k => $v) { ?>
					<option value="<?php echo $v->pay_type_id?>"><?php echo $v->pay_type_name ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height:30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<input type="submit" class="btn btn-default"
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

	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><b><?php echo Lang::get("slave.order_sn");?></b></td>
						<td><b><?php echo Lang::get("slave.tradeseq");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_type");?></b></td>
						
						<td><b><?php echo Lang::get("slave.pay_amount");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_amount_dollar");?></b></td>
						<td><b><?php echo Lang::get("slave.refund_amount");?></b></td>
						<td><b><?php echo Lang::get("slave.refund_amount_dollar");?></b></td>
						<td><b><?php echo Lang::get("slave.currency_code");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_time");?></b></td>
						<td><b><?php echo Lang::get("slave.refund_time");?></b></td>
						<td><b><?php echo Lang::get("slave.order_status");?></b></td>
						<td><b><?php echo Lang::get("slave.player_name");?></b></td>
						<td><b><?php echo Lang::get("slave.player_id");?></b></td>
						<td><b><?php echo Lang::get('slave.nickname');?></b></td>
						<td><b><?php echo Lang::get("slave.server_name");?></b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="o in orders">
						<td>{{o.order_sn}}</td>
						<td>{{o.tradeseq}}</td>
						<td>{{o.pay_type_name}}</td>
						<td>{{o.pay_amount | number:2}}</td>
						<td>{{o.pay_amount_dollar | number:2}}</td>
						<td>{{o.refund_amount | number:2}}</td>
						<td>{{o.refund_amount_dollar | number:2}}</td>
						<td>{{o.currency_code}}</td>
						<td>{{o.pay_time}}</td>
						<td>{{o.time_updated}}</td>
						<td><?php echo Lang::get('slave.refunded')?></td>
						<td>{{o.player_name}}</td>
						<td>{{o.player_id}}</td>
						<td>{{o.nickname}}</td>
						<td>{{o.server_name}}</td>
					</tr>
					</tbody>
			
			</table>
		</div>
	</div>
</div>