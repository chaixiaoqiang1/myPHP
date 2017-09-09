<script> 
function getCreatedPlayerController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.items = {};
	$scope.start_time = null;
	$scope.end_time = null;
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/user/player',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.items = data;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getCreatedPlayerController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/user/player" method="get" role="form"
				ng-submit="processFrom('/slave-api/user/player')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0">所有服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height: 35px;">
					<select class="form-control" name="interval" id="select_interval"
						ng-model="formData.interval" ng-init="formData.interval=0">
						<option value="0"><?php echo Lang::get('slave.select_interval') ?></option>
						<option value="1"><?php echo Lang::get('slave.interval_10_min') ?></option>
						<option value="2"><?php echo Lang::get('slave.interval_1_hour') ?></option>
						<option value="3"><?php echo Lang::get('slave.interval_1_day') ?></option>
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
				<input type="submit" class="btn btn-default" style=""
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><?php echo Lang::get("slave.start_time");?></td>
					<td><?php echo Lang::get("slave.end_time");?></td>
					<td><?php echo Lang::get("slave.create_amount");?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.ctime}}</td>
					<td>{{t.end_time}}</td>
					<td>{{t.count}}</td>
				</tr>

			</tbody>
		</table>
	</div>
</div>