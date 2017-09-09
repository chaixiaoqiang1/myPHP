<script> 
function getUserDeviceController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.items = {};
    $scope.sum = {};
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/user/device<?php echo ($is_yy? "/yy" : "") ?>',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.items = data;
            //$scope.sum = data.sum;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getUserDeviceController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group" style="height: 35px;">
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
				<div class="form-group" style="height: 15px;">
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="serach_type"
							id="serach_type" ng-model="formData.serach_type"
							ng-init="formData.serach_type=0">
							<option value="0">按照设备查询</option>
							<option value="1">按照Channel查询</option>
							<?php if(!$is_yy){ ?>
							<option value="2">按照Source查询</option>
							<?php } ?>
						</select>
					</div>
					<div class="col-md-6" style="padding: 0" ng-if="formData.serach_type==0">
						<select class="form-control" name="check_type"
							id="check_type" ng-model="formData.check_type"
							ng-init="formData.check_type=0">
							<option value="0">所有设备</option>
							<option value="1">仅安卓设备</option>
							<option value="2">仅IOS设备</option>
						</select>
					</div>
					<div class="col-md-6" style="padding: 0" ng-if="formData.serach_type==1">
						<input type="text" class="form-control" name="channel_type"
							id="channel_type" ng-model="formData.channel_type"
							placeholder="Channel: empty means all">
						</select>
					</div>
					<div class="col-md-6" style="padding: 0"  ng-if="formData.serach_type==2">
						<input type="text" class="form-control" name="source"
							id="source" ng-model="formData.source"
							placeholder="Source: empty means all">
						</select>
					</div>
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="interval" id="select_interval"
							ng-model="formData.interval" ng-init="formData.interval=3">
							<option value="3"><?php echo Lang::get('slave.all_interval') ?></option>
							<option value="0"><?php echo Lang::get('slave.interval_10_min') ?></option>
							<option value="1"><?php echo Lang::get('slave.interval_1_hour') ?></option>
							<option value="2"><?php echo Lang::get('slave.interval_1_day') ?></option>
						</select>
					</div>
				</div>
 				<div class="clearfix">
					<br />
				</div>
				<div class="clearfix">
					<br />
				</div> 
				<input type="submit" class="btn btn-success" style=""
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

	<div class="col-xs-8">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><?php echo "time";?></td>
					<td><?php echo "安装数";?></td>
					<td>设备类型</td>
					<td>Channel</td>
					<td>Source</td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.time}}</td>
					<td>{{t.count}}</td>
					<td>{{t.os_type}}</td>
					<td>{{t.channel}}</td>
					<td>{{t.source}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
