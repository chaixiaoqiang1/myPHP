<script>
	function MnsgLogSummonController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};

		$scope.processFrom = function() {
			$scope.alerts = [];
	        alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
	        $http({
	            'method': 'post',
	            'url': '/slave-api/mnsg/summon/record',
	            'data': $.param($scope.formData),
	            'headers': {
	                'Content-Type': 'application/x-www-form-urlencoded'
	            }
	        }).success(function(data) {
	        	$scope.key = {};
	    		$scope.value = {};
	            $scope.key = data.key;
	            $scope.value = data.value;
	        }).error(function(data) {
	        	$scope.key = {};
	    		$scope.value = {};
	            alertService.add('danger', data.error);
	        });
	    };
	}
</script>
<div class="col-xs-12" ng-controller="MnsgLogSummonController">
	<div class="row" id="top">
		<div class="col-xs-5">
			<form action="" method="post" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group col-md-10" ng-if="formData.single_or_count=='single'">
					<select class="form-control" name="server_id"
						id="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0" size="10">
						<option value="0"><?php echo Lang::get('slave.select_server') ?></option>	
						<?php foreach ($servers as $value) { ?>
						<option value="<?php echo $value->server_id; ?>"><?php echo $value->server_name ?></option>	
						<?php } ?>
					</select>
				</div>
				<div class="form-group col-md-10" ng-if="formData.single_or_count=='count'">
					<select class="form-control" name="server_ids"
						id="server_ids" ng-model="formData.server_ids" ng-init="formData.server_ids=0" size="10" multiple="true">
						<option value="0"><?php echo Lang::get('slave.select_server') ?></option>	
						<?php foreach ($servers as $value) { ?>
						<option value="<?php echo $value->server_id; ?>"><?php echo $value->server_name ?></option>	
						<?php } ?>
					</select>
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="single_or_count"
						id="single_or_count" ng-model="formData.single_or_count" ng-init="formData.single_or_count='single'">
						<option value="single"><?php echo Lang::get('slave.type_single_player') ?></option>	
						<option value="count"><?php echo Lang::get('slave.type_count') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-5" ng-if="formData.single_or_count=='single'">
					<input type="number" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" required />
				</div>
				<div class="form-group col-md-5" ng-if="formData.single_or_count=='count'">
					<select class="form-control" name="summon_type"
						id="summon_type" ng-model="formData.summon_type" ng-init="formData.summon_type=0">
						<option value="0"><?php echo Lang::get('slave.all_summon_type') ?></option>	
						<?php foreach ($summon_types as $k => $v) { ?>
						<option value="<?php echo $k; ?>"><?php echo $v; ?></option>	
						<?php } ?>
					</select>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>

				<input type="submit" class="btn btn-primary" style="margin-left:20px;"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />

			</form>
		</div>
	</div>
	<br>
	<div class="col-xs-8">
		<alert ng-repeat="alert in alerts" type="alert.type"
			close="alert.close()">{{alert.msg}}</alert>
	</div>

	<div class="col-xs-8">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td ng-repeat="k in key">{{k}}</td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="s in value">
					<td ng-repeat="v in s">{{v}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>