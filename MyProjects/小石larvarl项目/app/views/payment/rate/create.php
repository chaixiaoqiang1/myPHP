<script type="text/javascript">
function createRateController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alertService.add('success', data.msg);
            $scope.formData.from = '';
            $scope.formData.multiplier_rate = '';
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
}
</script>
<div class="col-xs-12" ng-controller="createRateController">
	<div class="row">
		<div class="eb-content">
			<form action="/exchange-rate" method="post" role="form"
				ng-submit="processFrom('/exchange-rate')" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="from" ng-model="formData.from"
						ng-init="formData.from=0">
						<option value="0"><?php echo Lang::get('rate.select_primitive_money') ?></option>
                        <?php foreach (Currency::all() as $k => $v) { ?>
                        <option value="<?php echo $v->currency_id?>"><?php echo $v->currency_name;?></option>
                        <?php } ?>      
                    </select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('rate.to_dollar')?>"
						ng-model="formData.dollar" ng-readonly='true' required
						name="dollar"?>
				</div>

				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('rate.enter_multiplier_rate')?>"
						ng-model="formData.multiplier_rate" required
						name="multiplier_rate"?>
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
</div>