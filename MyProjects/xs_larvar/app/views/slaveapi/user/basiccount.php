<script>
function basicCountController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.items = {};
    $scope.sum = {};
    $scope.processFrom = function() {
    	$scope.formData.items={};
        alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/user/basic/count',
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
<div class="col-xs-12" ng-controller="basicCountController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/user/basic/count" method="get" role="form"
				ng-submit="processFrom('/slave-api/user/basic/count')"
				onsubmit="return false;">
				<div class="form-group" style="height: 35px;">
					<div class="col-md-4" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-4" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-4" style="padding: 0">
						<select class="form-control" name="interval" id="select_interval"
							ng-model="formData.interval" ng-init="formData.interval=0">
							<option value="0"><?php echo Lang::get('slave.interval_1_day')?></option>
							<option value="1"><?php echo Lang::get('slave.interval_1_week')?></option>
							<option value="2"><?php echo Lang::get('slave.interval_1_month')?></option>
							<option value="3"><?php echo Lang::get('slave.interval_all')?></option>
						</select>
					</div>
				</div>
				<div class="clearfix">
					<br />
				</div>
				<p><font color=red><?php echo Lang::get('slave.count_interval_remind')?></font><p>
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
						<td><?php echo Lang::get('slave.date')?></td>
						<td><?php echo Lang::get('slave.add_user')?></td>
						<td><?php echo Lang::get('slave.add_player')?></td>
						<td><?php echo Lang::get('slave.reg_no_second_login')?></td>
						<td><?php echo Lang::get('slave.reg_player')?></td>
						<td><?php echo Lang::get('slave.reg_player_ratio')?></td>
						<td><?php echo Lang::get('slave.add_pay_user')?></td>
						<td><?php echo Lang::get('slave.add_pay_user_ratio')?></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="s in items">
						<td>{{s.title}}</td>
						<td>{{s.count_user}}</td>
						<td>{{s.create_player}}</td>
						<td>{{s.reg_no_login}}</td>
						<td>{{s.reg_create}}</td>
						<td>{{s.reg_create/s.count_user*100 | number:2}}%</td>
						<td>{{s.reg_pay_user}}</td>	
						<td>{{s.reg_pay_user/s.count_user*100 | number:2}}%</td>						
					</tr>
				</tbody>
			</table>
	</div>
</div>
