<script>
function getConsumptionController($scope, $http, alertService, $filter) {
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
            'url': '/slave-api/user/consumption/rank',
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
<div class="col-xs-12" ng-controller="getConsumptionController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/user/consumption/rank" method="get" role="form"
				ng-submit="processFrom('/slave-api/user/consumption/rank')"
				onsubmit="return false;">
				<div class="form-group" style="height: 35px;">
					<div class="col-md-5" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-9" style="padding-top: 5px;padding-left: 0px;">
						<select class="form-control" name="server_id"
							id="select_game_server" ng-model="formData.server_id"
							ng-init="formData.server_id=0">
							<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
							<?php foreach ($servers as $k => $v) { ?>
								<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
							<?php } ?>		
						</select>
					</div>
					<div class="col-md-4" style="padding-top: 5px;padding-left: 0px;">
						<select class="form-control" name="interval" id="select_interval"
							ng-model="formData.interval" ng-init="formData.interval=0">
							<option value="0"><?php echo Lang::get('slave.interval_1_day')?></option>
							<option value="1"><?php echo Lang::get('slave.interval_1_week')?></option>
							<option value="2"><?php echo Lang::get('slave.interval_1_month')?></option>
							<option value="3"><?php echo Lang::get('slave.interval_all')?></option>
						</select>
					</div>
					<div class="col-md-5" style="padding-top: 5px;padding-left: 0px;">
						<input type="text" class="form-control" name="rank" 
						  ng-model="formData.rank" placeholder="<?php echo Lang::get('slave.enter_rank')?>"/>
					</div>
				</div>
				<div class="clearfix">
					<br />
				</div>
				<p><font color=red>按周和按月统计都是以自然周和月统计</font><p>
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
	<div class="col-xs-12" ng-repeat="t in items">
		<div class="panel panel-success">
			<div class="panel-heading">{{t.title}}</div>
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><?php echo Lang::get('slave.server_name') ?></td>
						<td>uid</td>
						<td><?php echo Lang::get('slave.player_id')?></td>
						<td><?php echo Lang::get('slave.player_name')?></td>
						<td><?php echo Lang::get('slave.pay_amount_dollar')?></td>
						<td><?php echo Lang::get('slave.recharge_times')?></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="s in t.res">
						<td>{{s.server_name}}</td>
						<td>{{s.uid}}</td>
						<td>{{s.player_id}}</td>
						<td>{{s.player_name}}</td>
						<td>{{s.total_dollar_amount | number:2}}</td>
						<td>{{s.times}}</td>						
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
