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
            'url': '/slave-api/user/devicesearch',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.allserver = data.allserver;
            $scope.servers = data.servers;
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
			<form action="/slave-api/user/device" method="get" role="form"
				ng-submit="processFrom('/slave-api/user/device')"
				onsubmit="return false;">
<!-- 				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_ids"
						ng-init="formData.server_ids=0" multiple="multiple" ng-multiple="true" size=7>
						<option value="0"><?php //echo Lang::get('slave.show_all_servers') ?></option>
						<?php //foreach ($servers as $k => $v) { ?>
							<option value="<?php //echo $v->server_id?>"><?php //echo $v->server_name;?></option>
						<?php //} ?>
					</select>
				</div> -->
				<div class="form-group col-md-8" style="padding-left:0;">
					<select class="form-control" name="server_internal_id"
						id="server_internal_id" ng-model="formData.server_internal_id" ng-init = "formData.server_internal_id = 0"
						size="15" multiple="multiple" required>
						<option value="0">全部服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_internal_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>	
					</select>
				</div>
				
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
						<select class="form-control" name="interval" id="select_interval"
							ng-model="formData.interval" ng-init="formData.interval=3">
							<option value="3"><?php echo Lang::get('slave.all_interval') ?></option>
							<option value="1"><?php echo Lang::get('slave.interval_1_hour') ?></option>
							<option value="2"><?php echo Lang::get('slave.interval_1_day') ?></option>
						</select>
					</div>
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="check_type"
							id="check_type" ng-model="formData.check_type"
							ng-init="formData.check_type=0">
							<option value="0">所有设备</option>
							<option value="1">仅安卓设备</option>
							<option value="2">仅IOS设备</option>
						</select>
					</div>
					<div class="form-group col-md-8" >
					<br><font color="#F00">安装数、注册数为全服数据，其他数据为所选服务器数据</font>
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
					<td><?php echo "服务器";?></td>
					<td><?php echo "时间";?></td>
					<td><?php echo "安装数";?></td>
					<td><?php echo "注册数";?></td>
					<td><?php echo "创建角色数";?></td>
					<td><?php echo "付费总金额";?></td>
					<td><?php echo "设备类型";?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in allserver">
					<td>{{t.server_name}}</td>
					<td>{{t.date}}</td>
					<td>{{t.usernum}}</td>
					<td>{{t.signupnum}}</td>
					<td>{{t.playernum}}</td>
					<td>{{t.payment}}</td>
					<td>{{t.os_type}}</td>
				</tr>

			</tbody>
		</table>
	</div>
	<div class="col-xs-8">
		<table class="table table-striped" ng-repeat="t in servers">
			<thead>
				<tr class="info">
					<td><?php echo "服务器";?></td>
					<td><?php echo "时间";?></td>
					<td><?php echo "创建角色数";?></td>
					<td><?php echo "付费总金额";?></td>
					<td><?php echo "设备类型";?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="d in t">
					<td>{{d.server_name}}</td>
					<td>{{d.date}}</td>
					<td>{{d.playernum}}</td>
					<td>{{d.payment}}</td>
					<td>{{d.os_type}}</td>
				</tr>

			</tbody>
		</table>
	</div>
</div>
