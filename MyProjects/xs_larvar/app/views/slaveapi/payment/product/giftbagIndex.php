<script> 
function getGiftbagMessageController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
	$scope.items = [];
	$scope.pagination = {};

    $scope.processFrom = function(newPage) {
    	$scope.items = [];
    	$scope.alerts = [];
		alertService.alerts = $scope.alerts;
		$scope.product_type = $scope.formData.product_type;
        $http({
            'method': 'post',
            'url': '/slave-api/giftbag/message',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
			$scope.items = data;
			location.hash = '#top';
		}).error(function(data) {
			alertService.add('danger', data.error);
			location.hash = '#top';
		});
    };

    $scope.$watch('formData.pay_type_id', function(newValue, oldValue){
  		$scope.formData.method_id = "0";
  	});
} 
</script>
<div class="col-xs-12" ng-controller="getGiftbagMessageController" style="overflow:auto">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/payment/order/list" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
					<div class="col-md-3">
						<select class="form-control" name="pay_type_id"
							id="select_pay_type_id" ng-model="formData.pay_type_id"
							ng-init="formData.pay_type_id=0">
							<option value="0"><?php echo Lang::get('slave.all_pay_types') ?></option>
							<?php foreach ($pay_type_id_name as $id => $name) { ?>
							<option value="<?php echo $id?>"><?php echo $name;?></option>
							<?php } ?>		
						</select>
					</div>
					<div class="col-md-3">
						<select class="form-control" 
							ng-model="formData.method_id" ng-init="formData.method_id=0">
							<option value="0"><?php echo Lang::get('slave.child_pay_type') ?></option>
						<?php foreach ($method_id_name as $pay_type_id => $methods) { ?>
							<?php foreach ($methods as $id => $name) { ?>
								<option ng-if="formData.pay_type_id==<?php echo $pay_type_id ?>" value="<?php echo $id ?>">
									<?php echo $name; ?>
								</option>	
							<?php } ?>
						<?php } ?>
						</select>
					</div>
					<div class="col-md-3">
						<select class="form-control" 
							ng-model="formData.currency_id" ng-init="formData.currency_id=0">
							<option value="0"><?php echo Lang::get('slave.currency_id') ?></option>
							<?php foreach ($currencies as $currency) { ?>
								<option value="<?php echo $currency->currency_id ?>">
									<?php echo $currency->currency_name; ?>
								</option>	
							<?php } ?>
						</select>
					</div>
					<div class="col-md-3">
						<select class="form-control" name="product_type"
							id="product_type" ng-model="formData.product_type"
							ng-init="formData.product_type='giftbag'">
							<option value="giftbag"><?php echo Lang::get('slave.giftbag') ?></option>
							<option value="yuanbao"><?php echo Lang::get('slave.yuanbao') ?></option>
						</select>
					</div>
				<div class="clearfix"></br></div>
				<div class="clearfix"></br></div>
				<div class="col-md-6">
					<input type="submit" class="btn btn-default" style=""
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
	<div class="eb-content">
		<table class="table table-striped">
			<thead>
				<tr class="info" ng-if="product_type == 'giftbag'">
					<td><b><?php echo Lang::get('slave.giftbag_id'); ?></b></td>
					<td><b><?php echo Lang::get('slave.giftbag_name'); ?></b></td>
					<td><b><?php echo Lang::get('slave.giftbag_price'); ?></b></td>
					<td><b><?php echo Lang::get('slave.currency_code'); ?></b></td>
					<td><b><?php echo Lang::get('slave.is_use'); ?></b></td>
				</tr>
				<tr class="info" ng-if="product_type == 'yuanbao'">
					<td><b><?php if($mobile_game){echo "product_id"; }else{ echo "pay_amount_id";} ?></b></td>
					<td><b><?php echo Lang::get('slave.crystal'); ?></b></td>
					<td><b><?php echo Lang::get('slave.price'); ?></b></td>
					<td><b><?php echo Lang::get('slave.currency_code'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items" ng-if="product_type == 'giftbag'">
					<td>{{t.giftbag_id}}</td>
					<td>{{t.giftbag_name}}</td>
					<td>{{t.amount}}</td>
					<td>{{t.currency_name}}</td>
					<td>{{t.is_use}}</td>
				</tr>
				<tr ng-repeat="t in items" ng-if="product_type == 'yuanbao'">
					<td>{{t.pay_amount_id}}</td>
					<td>{{t.yuanbao_huodong}}</td>
					<td>{{t.pay_amount}}</td>
					<td>{{t.currency_name}}</td>
				</tr>
				</tbody>
		</table>
	</div>
</div>