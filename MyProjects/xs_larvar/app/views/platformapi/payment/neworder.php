<script>
function createNewOrderController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/platform-api/payment/neworder',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            alertService.add('success', 'New Order SN: ' + data.order_sn);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>

<div class="col-xs-12" ng-controller="createNewOrderController">
	<div class="row">
		<div class="eb-content">
			<form action="/platform-api/payment/neworder" method="post" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('slave.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="currency_code" ng-model="formData.currency_code" ng-init="formData.currency_code=0">
						<option value="0"><?php echo Lang::get('platformapi.select_currency') ?></option>
						<?php foreach ($currencies as  $k => $v) { ?>
							<option value="<?php echo $v->currency_code?>"><?php echo $v->currency_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					uid:
					<input type="text" name="pay_user_id" value="" ng-model="formData.pay_user_id" class="form-control" required/>
				</div>
				<div class="form-group">
					pay_amount:
					<input type="text" name="pay_amount" value="" ng-model="formData.pay_amount" class="form-control" required/>
				</div>
				<div class="form-group">
					basic yuanbao:
					<input type="text" name="basic_yuanbao_amount" value="" ng-model="formData.basic_yuanbao_amount" class="form-control" required/>
				</div>
				<div class="form-group">
					extra yuanbao:
					<input type="text" name="extra_yuanbao_amount" value="" ng-model="formData.extra_yuanbao_amount" class="form-control" required/>
				</div>
				<div class="form-group">
					huodong yuanbao:
					<input type="text" name="basic_yuanbao_amount" value="" ng-model="formData.huodong_yuanbao_amount" class="form-control" required/>
				</div>
				<div class="form-group">
					total yuanbao:
					<input type="text" name="yuanbao_amount" value="" ng-model="formData.yuanbao_amount" class="form-control" required/>
				</div>
				<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-default"/> 
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>