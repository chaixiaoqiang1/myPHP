<script> 
function getPaymentOrderPlayerController($scope, $http, alertService, $modal, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.total = {};
    $scope.flag = 0;
	$scope.start_time = null;
	$scope.end_time = null;
    $scope.processFrom = function() {
    	$scope.flag = 0;
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.pid = $scope.formData.player_id ? $scope.formData.player_id : 0;
        $http({
            'method': 'post',
            'url': '/slave-api/poker/activity/data',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {        	
            $scope.items = data;
            $scope.flag = 1;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<style type="text/css">
        td
        {
            white-space: nowrap;
        }
    </style>
<div class="col-xs-12" ng-controller="getPaymentOrderPlayerController">
	<div class="row">
		<div class="eb-content">
			<form action="/poker/activity/data" method="get"
				role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">

				<div class="form-group" style="height:30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix">
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_player_id')?>"
						ng-model="formData.player_id" name="player_id"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<select class="form-control" name="type" ng-model="formData.device"
						ng-init="formData.device=-1">
						<option value="-1"><?php echo Lang::get('slave.enter_device')?></option>
						<option value="0"><?php echo Lang::get('slave.webgame_id')?></option>
						<option value="1"><?php echo Lang::get('slave.android_id')?></option>
						<option value="2"><?php echo Lang::get('slave.ios_id')?></option>
						<option value="3"><?php echo Lang::get('slave.webgame_eng')?></option>
						<option value="4"><?php echo Lang::get('slave.android_eng')?></option>
						<option value="5"><?php echo Lang::get('slave.ios_eng')?></option>
					</select>
					
				</div>
				<div class="form-group col-md-3">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.enter_activity_id')?>"
						ng-model="formData.activity_id" name="activity_id"?>
				</div>
				<div class="form-group col-md-3">
					<input type="text" class="form-control" 
						placeholder="<?php echo Lang::get('slave.enter_reward_id') ?>" 
						ng-model="formData.reward_id" name="reward_id">
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

	
	<div class="col-xs-12">
		<table ng-if='flag==1' class="table table-striped">
			<thead>
				<tr class="info" id="server">
					<td ng-if="pid!=0"><?php echo Lang::get("slave.player_id");?></td>
					<td><?php echo Lang::get("slave.device_type");?></td>
					<td><?php echo Lang::get("slave.activity_id");?></td>
					<td><?php echo Lang::get("slave.reward_id");?></td>
					<td ng-if="pid!=0"><?php echo Lang::get("slave.join_time");?></td>
					<td ng-if="pid==0"><?php echo Lang::get("slave.player_nums");?></td>
					<td ng-if="pid==0"><?php echo Lang::get("slave.activity_times");?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td ng-if="pid!=0">{{t.player_id}}</td>
					<td ng-if="t.device == 0"><?php echo Lang::get('slave.webgame_id')?></td>
					<td ng-if="t.device == 1"><?php echo Lang::get('slave.android_id')?></td>
					<td ng-if="t.device == 2"><?php echo Lang::get('slave.ios_id')?></td>
					<td ng-if="t.device == 3"><?php echo Lang::get('slave.webgame_eng')?></td>
					<td ng-if="t.device == 4"><?php echo Lang::get('slave.android_eng')?></td>
					<td ng-if="t.device == 5"><?php echo Lang::get('slave.ios_eng')?></td>
					<td>{{t.activity_id}}</td>
					<td>{{t.reward_id}}</td>
					<td ng-if="pid!=0">{{t.time}}</td>
					<td ng-if="pid==0">{{t.player_num}}</td>
					<td ng-if="pid==0">{{t.times}}</td>
				</tr>

			</tbody>
		</table>
	</div> 
</div>


