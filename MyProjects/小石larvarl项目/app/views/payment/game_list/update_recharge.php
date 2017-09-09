<script type="text/javascript">
function updateRechargeController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/platform-api/payment/sdk_recharge',
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alertService.add('success', data.msg);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
}
</script>

<div class="col-xs-12" ng-controller="updateRechargeController">
    <div class="row">
        <div class="eb-content">
            <form action="/platform-api/payment/sdk_recharge" method="post" role="form" ng-submit="processFrom('/platform-api/payment/sdk_recharge')" onsubmit="return false;">
                <div class="well">
					<div class="form-group">
						<label for="is_update_platform"></label>
					<?php echo Lang::get('payment.sdk_recharge')?>
					<select name="sdk_recharge"
							ng-model="formData.sdk_recharge"
							ng-init="formData.sdk_recharge=1" class="form-control">
							<option value="1"><?php echo Lang::get('payment.yes')?></option>
							<option value="0"><?php echo Lang::get('payment.no')?></option>
							?>
						</select>
					</div>
				</div>
               
                <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>  
            </form>
        </div>
    </div>
    
    <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>