
<script type="text/javascript">
function createAppController($scope, $http, alertService) {
	$scope.alerts = [];
	$scope.formData = {};
	$scope.processFrom = function() {
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : '/currency',
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alertService.add('success', data.msg);
			$scope.formData.currency_id = '';
			$scope.formData.currency_code = '';
			$scope.formData.currency_symbol = '';
			$scope.formData.currency_name = '';
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
}
</script>

<div class="col-xs-12" ng-controller="createServerController">
	<div class="row">
		<div class="eb-content">
			<form action="/currency" method="post" role="form" ng-submit="processFrom('/currency')" onsubmit="return false;">

				<div class="form-group">
				</div>
				<div class="form-group">
					<label for="currency_code"></label>
					currency_code:
					<input type="text" class="form-control" id="currency_code" placeholder="<?php echo Lang::get('currency.currency_code') ?>" required ng-model="formData.currency_code" name="currency_code" />
				</div>
				
				<div class="form-group">
					<label for="currency_symbol"></label>
					currency_symbol:
					<input type="text" class="form-control" id="currency_symbol" placeholder="<?php echo Lang::get('currency.currency_symbol') ?>(非必须)" required ng-model="formData.currency_symbol" name="currency_symbol" ng-autofocus="true" />
				</div>
				
				<div class="form-group">
					<label for="currency_name"></label>
					currency_name:
					<input type="text" class="form-control" id="currency_name" placeholder="<?php echo Lang::get('currency.currency_name') ?>" required ng-model="formData.currency_name" name="currency_name" />
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