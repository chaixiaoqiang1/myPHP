<script>
	function LoginDeviceController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.show = 0;
		$scope.processFrom = function(url, baned) {
			alertService.alerts = $scope.alerts;
			$scope.formData.baned = baned;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
			$scope.formData.baned = 0;
		};

		$scope.switchstatu = function (device_id, limit_type) {	
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.banData = {};
			$scope.banData.device_id = device_id;
			$scope.banData.limit_type = limit_type;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/yysg/logindevice',
				'data'   : $.param($scope.banData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
				$scope.banData = {};
				alertService.add('success', '修改成功');
				$scope.processFrom('/game-server-api/yysg/logindevice');
			}).error(function(data) {
				alertService.add('danger', data.error_description);
			});	
		}
	}
</script>
<div class="col-xs-12" ng-controller="LoginDeviceController">
	<div class="row">
		<div class="eb-content">
			<form method="post" role="form"
				ng-submit="processFrom('/game-server-api/yysg/logindevice', 0)"
				onsubmit="return false;">
				<b>根据UID查询(都输入的话以UID为准)</b>
				<div class="form-group">
					<input type="text" class="form-control" id="uid"
						placeholder="输入UID查询相关信息"
						 ng-model="formData.uid" name="uid"
						 <?php if($uid){ ?> ng-init="formData.uid=<?php echo $uid; ?>" <?php } ?> />
				</div>
				<b>或者根据设备号查询</b>
				<div class="form-group">
					<input type="text" class="form-control" id="device_id"
						placeholder="输入设备号查询相关信息"
						 ng-model="formData.device_id" name="device_id" />
				</div>
				<input type="submit" class="btn btn-default" style="background:#fa5; color:#333"
					value="<?php echo Lang::get('basic.btn_check') ?>" />
				<input type="button" class="btn btn-default" style="background:#fa5; color:#333"
					value="<?php echo Lang::get('slave.baned_device_id') ?>" ng-click="processFrom('/game-server-api/yysg/logindevice', 1)" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" ng-if="show==1">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>设备号</b></td>
					<td><b>UID</b></td>
					<td><b>设备类型</b></td>
					<td><b><?php echo Lang::get('slave.os_type'); ?></b></td>
					<td><b>注册时间</b></td>
					<td><b>上次登陆时间</b></td>
					<td><b><?php echo Lang::get('slave.device_statu'); ?></b></td>
					<td><b><?php echo Lang::get('slave.execute_action'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.device_id}}</td>
					<td>{{t.uid}}</td>
					<td>{{t.device_type}}</td>
					<td>{{t.os_type}}</td>
					<td>{{t.create_time}}</td>
					<td>{{t.login_time}}</td>
					<td>{{t.limit_type}}</td>
					<td ng-if="t.limit_type == '<?php echo Lang::get('slave.baned'); ?>'"><input type="button" value="<?php echo Lang::get('slave.un_ban'); ?>" ng-click="switchstatu(t.device_id,-1)" /></td>
					<td ng-if="t.limit_type == '<?php echo Lang::get('slave.normal'); ?>'"><input type="button" value="<?php echo Lang::get('slave.ban'); ?>" ng-click="switchstatu(t.device_id,1)" /></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.operation_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.execute_action'); ?></b></td>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($operations as $value) { ?>
				<tr>
					<td><?php echo $value->time; ?></td>
					<td><?php echo $value->extra_msg; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>