<script>
	function MnsgLogSummonController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};

		$scope.processFrom = function(download) {
			$scope.alerts = [];
	        alertService.alerts = $scope.alerts;
			$scope.formData.reg_start_time = $filter('date')($scope.reg_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.reg_end_time = $filter('date')($scope.reg_end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.pay_start_time = $filter('date')($scope.pay_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.pay_end_time = $filter('date')($scope.pay_end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.last_login_time = $filter('date')($scope.last_login_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.download = download;
	        $http({
	            'method': 'post',
	            'url': '/slave-api/payorder/filter',
	            'data': $.param($scope.formData),
	            'headers': {
	                'Content-Type': 'application/x-www-form-urlencoded'
	            }
	        }).success(function(data) {
	        	$scope.keys = {};
	    		$scope.values = {};
	    		if(data.now){
	    			window.location.replace("/slave-api/payorder/filter?filename=" + data.now);
	    		}else{
		            $scope.keys = data.keys;
		            $scope.values = data.values;
	            }
	        }).error(function(data) {
	        	$scope.keys = {};
	    		$scope.values = {};
	            alertService.add('danger', data.error);
	        });
	    };
	}
</script>
<div class="col-xs-12" ng-controller="MnsgLogSummonController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="" method="post" role="form"
				ng-submit="processFrom(0)" onsubmit="return false;">
				<div class="form-group col-md-5">
					<select class="form-control" name="filter_type"
						id="filter_type" ng-model="formData.filter_type" ng-init="formData.filter_type='order'">
						<option value="order"><?php echo Lang::get('slave.group_by_order') ?></option>	
						<option value="player"><?php echo Lang::get('slave.group_by_player') ?></option>	
						<option value="user"><?php echo Lang::get('slave.group_by_user') ?></option>	
						<option value="all"><?php echo Lang::get('slave.group_by_all') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="by_reg_time"
						id="by_reg_time" ng-model="formData.by_reg_time" ng-init="formData.by_reg_time=0">
						<option value="0"><?php echo Lang::get('slave.not') ?><?php echo Lang::get('slave.by_reg_time') ?></option>	
						<option value="1"><?php echo Lang::get('slave.by_reg_time') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-10" ng-show="formData.by_reg_time==1">
					<p><b><?php echo Lang::get('slave.by_reg_time'); ?></b></p>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="reg_start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="reg_end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="by_pay_time"
						id="by_pay_time" ng-model="formData.by_pay_time" ng-init="formData.by_pay_time=0">
						<option value="0"><?php echo Lang::get('slave.not') ?><?php echo Lang::get('slave.by_pay_time') ?></option>	
						<option value="1"><?php echo Lang::get('slave.by_pay_time') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-10" ng-show="formData.by_pay_time==1">
					<p><b><?php echo Lang::get('slave.by_pay_time');?></b></p>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="pay_start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="pay_end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="by_last_login_time"
						id="by_last_login_time" ng-model="formData.by_last_login_time" ng-init="formData.by_last_login_time=0">
						<option value="0"><?php echo Lang::get('slave.not') ?><?php echo Lang::get('slave.by_last_login_time') ?></option>	
						<option value="<"><?php echo Lang::get('slave.by_last_login_time_before') ?></option>	
						<option value=">"><?php echo Lang::get('slave.by_last_login_time_after') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-10" ng-show="formData.by_last_login_time">
					<p><b><?php echo Lang::get('slave.by_last_login_time');?></b></p>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="last_login_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="by_dollar_amount"
						id="by_dollar_amount" ng-model="formData.by_dollar_amount" ng-init="formData.by_dollar_amount=0">
						<option value="0"><?php echo Lang::get('slave.not') ?><?php echo Lang::get('slave.by_dollar_amount') ?></option>	
						<option value=">"><?php echo Lang::get('slave.by_dollar_amount_larger') ?></option>	
						<option value="<"><?php echo Lang::get('slave.by_dollar_amount_smaller') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-10" ng-show="formData.by_dollar_amount">
					<p><b><?php echo Lang::get('slave.by_dollar_amount');?></b></p>
					<div class="col-md-5">
						<input type="number" class="form-control" ng-model="formData.dollar_amount" placeholder="<?php echo Lang::get('slave.lower_bound_dollar'); ?>" />
					</div>
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="by_yuanbao_amount"
						id="by_yuanbao_amount" ng-model="formData.by_yuanbao_amount" ng-init="formData.by_yuanbao_amount=0">
						<option value="0"><?php echo Lang::get('slave.not') ?><?php echo Lang::get('slave.by_yuanbao_amount') ?></option>	
						<option value=">"><?php echo Lang::get('slave.by_yuanbao_amount_larger') ?></option>	
						<option value="<"><?php echo Lang::get('slave.by_yuanbao_amount_smaller') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-10" ng-show="formData.by_yuanbao_amount">
					<p><b><?php echo Lang::get('slave.by_yuanbao_amount');?></b></p>
					<div class="col-md-5">
						<input type="number" class="form-control" ng-model="formData.yuanbao_amount" placeholder="<?php echo Lang::get('slave.lower_bound_yuanbao'); ?>" />
					</div>
				</div>
				<div class="clearfix"></div>

				<p style="color:red;margin-left:20px;font-size:15px"><b><?php echo Lang::get('slave.select_your_conditions');?></b></p>
				<input type="submit" class="btn btn-primary" style="margin-left:20px;"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />

				<input type="button" class="btn btn-primary" style="margin-left:20px;"
					value="<?php echo Lang::get('slave.download') ?>" ng-click="processFrom(1)" />
			</form>
		</div>
	</div>
	<br>
	<div class="eb-content">
		<alert ng-repeat="alert in alerts" type="alert.type"
			close="alert.close()">{{alert.msg}}</alert>
	</div>

	<div class="col-xs-10">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td ng-repeat="k in keys">{{k}}</td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="s in values">
					<td ng-repeat="v in s">{{v}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>