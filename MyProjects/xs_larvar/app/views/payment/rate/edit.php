<script>
function updateRateController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'put',
            'url'    : url,
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

<div class="col-xs-12" ng-controller="updateRateController">
	<div class="row">
		<div class="eb-content">
			<form action="/exchange-rate/<?php echo $rate->rate_id; ?>" method="put"
				role="form"
				ng-submit="processFrom('/exchange-rate/<?php echo $rate->rate_id; ?>')"
				onsubmit="return false;">
				<div class="form-group">
					<label for="from"><?php echo Lang::get('rate.from') ?></label> <input type="text"
						class="form-control" id="from"
						ng-init="formData.from='<?php echo Currency::where('currency_id',$rate->from)->pluck('currency_name'); ?>'"
						ng-readonly='true' ng-model="formData.from" required name="from" />
				</div>

				<div class="form-group">
					<label for="to"><?php echo Lang::get('rate.to') ?></label> <input type="text" class="form-control"
						id="to"
						ng-init="formData.to='<?php echo Currency::where('currency_id',$rate->to)->pluck('currency_name'); ?>'"
						ng-readonly='true' ng-model="formData.to" required name="to" />
				</div>

				<div class="form-group">
					<label for="multiplier_rate"><?php echo Lang::get('rate.multiplier_rate') ?></label> <input
						type="text" class="form-control" id="multiplier_rate"
						ng-init="formData.multiplier_rate='<?php echo $rate->multiplier_rate?>'"
						required ng-model="formData.multiplier_rate"
						name="multiplier_rate" />
				</div>

				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>