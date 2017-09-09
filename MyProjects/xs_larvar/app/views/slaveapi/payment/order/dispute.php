<script> 
function modalDisputeOrderController($scope, $modalInstance, order, actType, typeVal, $http, alertService) {
	$scope.order = order;
	$scope.orderData = {};
	$scope.actType = actType;
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.dispute = function(url) {
		$scope.orderData.act_type = actType;
		$scope.orderData.type_val= typeVal;
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.orderData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			$modalInstance.close();
		}).error(function(data) {
			alert(data.error);
		});
	}
}

function getDisputeOrderController($scope, $http, alertService, $modal, $filter) {
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
            'url': '/slave-api/payment/order/dispute',
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
	$scope.openWin = function(order, actType, typeVal) {
		var modalInstance = $modal.open({
			templateUrl: 'dispute_order.html',
			controller: modalDisputeOrderController,
			resolve: {
				order : function () {
					return order;
				},
				actType : function() {
					return actType;
				},
				typeVal : function() {
					return typeVal;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			$scope.processFrom();	
		});
	}
} 
</script>
<div class="col-xs-12" ng-controller="getDisputeOrderController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/payment/order/dispute" method="get"
				role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">

				<div class="form-group col-md-4" style="padding:0">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.fb_name')?>"
						ng-model="formData.fb_name" name="fb_name"?>
				</div>
				<div class="form-group col-md-4" style="padding:0;margin-left:10px;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.fb_id')?>"
						ng-model="formData.fb_id" name="fb_id"?>
				</div>
				<div class="clearfix"></div>
				<div class="form-group col-md-4" style="padding:0;margin-right:10px;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_order_number')?>"
						ng-model="formData.order_sn" name="order_sn"?>
				</div>
				<div class="form-group col-md-4" style="padding:0;">
					<select class="form-control" name="status" ng-model="formData.status" ng-init="formData.status=2">
					<option value="2"><?php echo Lang::get('slave.fb_dispute_status_all') ?></option>
					<option value="0"><?php echo Lang::get('slave.fb_dispute_status_no') ?></option>
					<option value="1"><?php echo Lang::get('slave.fb_dispute_status_yes'); ?></option>
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
						<td><b><?php echo Lang::get("slave.pay_amount");?></b></td>
						<td><b><?php echo Lang::get("slave.pay_amount_dollar");?></b></td>
						<td><b><?php echo Lang::get("slave.refund_amount");?></b></td>
						<td><b><?php echo Lang::get("slave.refund_amount_dollar");?></b></td>
						<td><b><?php echo Lang::get("slave.currency_code");?></b></td>
						<td><b><?php echo Lang::get("slave.fb_name");?></b></td>
						<td><b><?php echo Lang::get("slave.fb_id");?></b></td>
						<td><b><?php echo Lang::get("slave.fb_email");?></b></td>
						<td><b><?php echo Lang::get("slave.dispute_create_time");?></b></td>
						<td><b><?php echo Lang::get("slave.player_name");?></b></td>
						<td><b><?php echo Lang::get("slave.server_name");?></b></td>
						<td><b><?php echo Lang::get("slave.execute_time");?></b></td>
						<td><b><?php echo Lang::get("slave.dispute_status");?></b></td>
						<td><b><?php echo Lang::get('slave.execute_action');?></b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="o in orders">
						<td>{{o.order_sn}}</td>
						<td>{{o.tradeseq}}</td>
						<td>{{o.pay_amount | number:2}}</td>
						<td>{{o.pay_amount_dollar | number:2}}</td>
						<td>{{o.refund_amount | number:2}}</td>
						<td>{{o.refund_amount_dollar | number:2}}</td>
						<td>{{o.currency_code}}</td>
						<td>{{o.user_name}}</td>
						<td>{{o.user_fb_id}}</td>
						<td>{{o.user_email}}</td>
						<td>{{o.create_time}}</td>
						<td>{{o.player_name}}</td>
						<td>{{o.server_name}}</td>
						<td>{{o.execute_time}}</td>
						<td>{{o.status_name}}</td>
						<td ng-if="o.status == 0">
							<button class="btn btn-primary" ng-click="openWin(o, 'refund')">
								<?php echo Lang::get('slave.act_refund');?>	
							</button>
							<button class="btn btn-default" ng-click="openWin(o, 'edit', 1)">
								<?php echo Lang::get('slave.act_close');?>
							</button>
						</td>
						<td ng-if="o.status == 1">
							<button class="btn btn-default" ng-click="openWin(o, 'edit', 2)">
								<?php echo Lang::get('slave.act_open');?>
							</button>
						</td>
					</tr>
					</tbody>
			
			</table>
		</div>
	</div>
</div>


<script type="text/ng-template" id="dispute_order.html">
        <div class="modal-header">
        </div>
		<form action="/slave-api/payment/order/dispute/act" method="post" role="form" ng-submit="dispute('/slave-api/payment/order/dispute/act')" onsubmit="return false;">
        <div class="modal-body">
			<div class="form-group">
				<label><?php echo Lang::get('slave.refund_amount')?>:</label>	
				<input type="text" class="form-control" ng-model="orderData.refund_amount" autofocus="autofocus" />
			</div>
			<div class="form-group" ng-if="actType =='refund'">
				<select class="form-control" ng-model="orderData.reason_id" ng-init="orderData.reason_id= 1">
				<option value="1"><?php echo Lang::get('slave.refund_reason_1');?></option>
				<option value="2"><?php echo Lang::get('slave.refund_reason_2');?></option>
				<option value="3"><?php echo Lang::get('slave.refund_reason_3');?></option>
				</select>
			</div>
        </div>
		<input type="hidden" ng-model="orderData.dispute_id" ng-init="orderData.dispute_id = order.dispute_id" name="dispute_id"/>
		<input type="hidden" ng-model="orderData.tradeseq" ng-init="orderData.tradeseq = order.tradeseq" name="tradeseq"/>
		<input type="hidden" ng-model="orderData.currency_code" ng-init="orderData.currency_code = order.currency_code" name="currency_code" />
        <div class="modal-footer" style="text-align:center;">
			<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
		</form>
</script>