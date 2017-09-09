<script>
	function PokerSignupInfoController($scope, $http, alertService, $filter)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.end_time = null;
		$scope.start_time = null;

		$scope.processFrom = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/signupcreate/info',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
                $scope.result = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="PokerSignupInfoController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
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
	<div class="row margin-top-10 eb-content">
		<div>
			<div class="panel panel-success">
				<div class="panel-heading">
					<?php echo Lang::get('slave.sign_info') ?>
				</div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('slave.all_sign')?>:</dt>
						<dd>{{result.all_sign}}</dd>
						<dt><?php echo Lang::get('slave.all_create')?>:</dt>
						<dd>{{result.all_create}}</dd>
						<dt><?php echo Lang::get('slave.all_device')?>:</dt>
						<dd>{{result.all_device}}</dd>
						<dt><?php echo Lang::get('slave.single_day_sign')?>:</dt>
						<dd>{{result.single_day_sign}}</dd>
						<dt><?php echo Lang::get('slave.single_day_create')?>:</dt>
						<dd>{{result.single_day_create}}</dd>
						<dt><?php echo Lang::get('slave.single_day_device')?>:</dt>
						<dd>{{result.single_day_device}}</dd>
					</dl>
				</div>
			</div>

			<div class="panel panel-success">
				<div class="panel-heading">
					<?php echo Lang::get('slave.payment_info') ?>
				</div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('slave.before_30_sum_dollar')?>:</dt>
						<dd><?php echo Lang::get('slave.pay_user_num')."\t"; ?>:{{result.payment.before_30_sum_dollar.user_num}}</dd>
						<dd><?php echo Lang::get('slave.pay_amount_dollar')."\t"; ?>:{{result.payment.before_30_sum_dollar.dollar}}</dd>
						<dt><?php echo Lang::get('slave.before_7_sum_dollar')?>:</dt>
						<dd><?php echo Lang::get('slave.pay_user_num')."\t"; ?>:{{result.payment.before_7_sum_dollar.user_num}}</dd>
						<dd><?php echo Lang::get('slave.pay_amount_dollar')."\t"; ?>:{{result.payment.before_7_sum_dollar.dollar}}</dd>
						<dt><?php echo Lang::get('slave.single_day_sum_dollar')?>:</dt>
						<dd><?php echo Lang::get('slave.pay_user_num')."\t"; ?>:{{result.payment.single_day_sum_dollar.user_num}}</dd>
						<dd><?php echo Lang::get('slave.pay_amount_dollar')."\t"; ?>:{{result.payment.single_day_sum_dollar.dollar}}</dd>
						<dt><?php echo Lang::get('slave.single_day_sign_pay_sum_dollar')?>:</dt>
						<dd><?php echo Lang::get('slave.pay_user_num')."\t"; ?>:{{result.payment.single_day_sign_pay_sum_dollar.user_num}}</dd>
						<dd><?php echo Lang::get('slave.pay_amount_dollar')."\t"; ?>:{{result.payment.single_day_sign_pay_sum_dollar.dollar}}</dd>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>