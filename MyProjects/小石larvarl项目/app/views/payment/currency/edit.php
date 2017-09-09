<script type="text/javascript">
function updateCurrencyController($scope, $http, alertService) {
	$scope.alerts = [];
	$scope.formData = {};
	$scope.processFrom = function(url) {
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'put',
			'url'	 : url,
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

<div class="col-xs-12" ng-controller="updateCurrencyController">
	<div class="row" >
		<div class="eb-content">
			<form action="/currency/<?php echo $currency->currency_id; ?>" method="put" role="form" ng-submit="processFrom('/currency/<?php echo $currency->currency_id; ?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="currency_code"></label>
					<input type="text" class="form-control" id="currency_code" ng-init="formData.currency_code='<?php echo $currency->currency_code?>'" required ng-model="formData.currency_code" name="currency_code" /> 
				</div>				

				<div class="form-group">
					<label for="currency_symbol"></label>
					<input type="text" class="form-control" id="currency_symbol" ng-init="formData.currency_symbol='<?php echo $currency->currency_symbol?>'" required ng-model="formData.currency_symbol" name="currency_symbol" /> 
				</div>

				<div class="form-group">
					<label for="currency_name"></label>
					<input type="text" class="form-control" id="currency_name" ng-init="formData.currency_name='<?php echo $currency->currency_name?>'" required ng-model="formData.currency_name" name="currency_name" /> 
				</div>							
						
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>