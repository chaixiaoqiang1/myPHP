<script>
	function RzzwRewardController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};

		$scope.processFrom = function(type, record_id) {
	        alertService.alerts = $scope.alerts;
	        $scope.formData.type = type;
	        $scope.formData.record_id = record_id;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
	        $http({
	            'method': 'post',
	            'url': '/slave-api/rzzw/reward/record',
	            'data': $.param($scope.formData),
	            'headers': {
	                'Content-Type': 'application/x-www-form-urlencoded'
	            }
	        }).success(function(data) {
	        	if('search' == type){
		        	$scope.keywords = {};
		    		$scope.sqlresult = {};
		            $scope.keywords = data.keywords;
		            $scope.items = data.items;
	            }else if('update' == type){
	            	alertService.add('success', data.msg);
	            	$scope.processFrom('search', 0);
	            }
	        }).error(function(data) {
	        	if('search' == type){
		        	$scope.keywords = {};
		    		$scope.items = {};
		            alertService.add('danger', data.error);
		        }else if('update' == type){
		        	alertService.add('danger', data.error);
		        }
	        });
	    };
	}
</script>
<div class="col-xs-12" ng-controller="RzzwRewardController">
	<div class="row" id="top">
		<div class="col-xs-5">
			<form action="" method="post" role="form"
				ng-submit="processFrom('search', 0)" onsubmit="return false;">
				<div class="form-group col-md-5">
					<select class="form-control" name="record_type"
						id="record_type" ng-model="formData.record_type" ng-init="formData.record_type=0">
						<option value="0"><?php echo Lang::get('slave.all_type') ?></option>	
						<option value="1"><?php echo Lang::get('slave.already_reward') ?></option>	
						<option value="2"><?php echo Lang::get('slave.not_reward') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="by_time"
						id="by_time" ng-model="formData.by_time" ng-init="formData.by_time=0">
						<option value="0"><?php echo Lang::get('slave.not_by_time') ?></option>
						<option value="1"><?php echo Lang::get('slave.by_time') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-5">
					<input type="number" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" />
				</div>
				<div class="form-group col-md-5">
					<input type="number" class="form-control" id="reward_id"
						placeholder="<?php echo Lang::get('slave.reward_id') ?>"
						 ng-model="formData.reward_id" name="reward_id" />
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;" ng-show="formData.by_time==1">
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
					<td ng-repeat="k in keywords"><b>{{k}}</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.server_id}}</td>
					<td>{{t.uid}}</td>
					<td>{{t.reward_id}}</td>
					<td ng-if="t.is_done==0"><button class="btn btn-danger" ng-click="processFrom('update', t.id)"><?php echo Lang::get('slave.send_stat_done'); ?></button></td>
					<td ng-if="t.is_done==1"><?php echo Lang::get('slave.already_sent'); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>