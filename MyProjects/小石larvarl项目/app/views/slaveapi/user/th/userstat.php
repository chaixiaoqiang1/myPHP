<script> 
function THGetUserStatController($scope, $http, alertService, $filter) {
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
            'url': '/slave-api/user/th/stat',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.items = data.items;
            $scope.sum = data.sum;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="THGetUserStatController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/user/userstat" method="get" role="form"
				ng-submit="processFrom('/slave-api/user/sxd/userstat')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=5>
						<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
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
							<option value="0"><?php echo Lang::get('slave.interval_10_min') ?></option>
							<option value="1"><?php echo Lang::get('slave.interval_1_hour') ?></option>
							<option value="2"><?php echo Lang::get('slave.interval_1_day') ?></option>
						</select>
					</div>
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="filtrate_id"
							id="select_filtrate_id" ng-model="formData.filtrate_id"
							ng-init="formData.filtrate_id=0">
							<option value="0"><?php echo Lang::get('slave.filtrate_by_source') ?></option>
							<option value="1"><?php echo Lang::get('slave.filtrate_by_u1') ?></option>
							<option value="2"><?php echo Lang::get('slave.filtrate_by_u2') ?></option>
						</select>
					</div>
				</div>
				<div class="clearfix">
					<br />
				</div>
				<div class="form-group" style="height: 20px;">
					<div class="col-md-4" style="padding: 0">
						<div class="input-group">
							<input type="text" class="form-control"
								ng-model="formData.source" name="source"
								placeholder="<?php echo Lang::get('slave.enter_source') ?>" />
						</div>
					</div>
					<div class="col-md-4" style="padding: 0">
						<div class="input-group">
							<input type="text" class="form-control" ng-model="formData.u1"
								name="u1"
								placeholder="<?php echo Lang::get('slave.enter_u1') ?>" />
						</div>
					</div>
					<div class="col-md-4" style="padding: 0">
						<div class="input-group">
							<input type="text" class="form-control" ng-model="formData.u2"
								name="u2"
								placeholder="<?php echo Lang::get('slave.enter_u2') ?>" />
						</div>
					</div>
				</div>
				<div class="clearfix">
					<br />
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

	<div class="row margin-top-10 col-xs-6">
		<div class="panel panel-success">
			<div class="panel-heading"><?php echo Lang::get('slave.statics_register') ?></div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt><?php echo Lang::get('slave.sum_formal_player')?></dt>
					<dd>{{sum.sum_formal}}</dd>
					<dt><?php echo Lang::get('slave.sum_anonymous_player')?></dt>
					<dd>{{sum.sum_anonymous}}</dd>
					<dt><?php echo Lang::get('slave.sum_anonymous_player_levelup')?></dt>
					<dd>{{sum.sum_count_anonymous_formal}}</dd>
					<dt><?php echo Lang::get('slave.sum_formal_player_create')?></dt>
					<dd>{{sum.sum_player_formal}}</dd>
					<dt><?php echo Lang::get('slave.sum_anonymous_player_create')?></dt>
					<dd>{{sum.sum_player_anonymous}}</dd>
					<dt><?php echo Lang::get('slave.sum_formal_level10_player_create')?></dt>
					<dd>{{sum.sum_lev_formal}}</dd>
					<dt><?php echo Lang::get('slave.sum_anonymous_level10_player_create')?></dt>
					<dd>{{sum.sum_lev_anonymous}}</dd>
				</dl>
			</div>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><?php echo Lang::get("slave.start_time");?></td>
					<td><?php echo Lang::get("slave.end_time");?></td>
					<td><?php echo Lang::get("slave.source");?></td>
					<td><?php echo Lang::get("slave.u1");?></td>
					<td><?php echo Lang::get("slave.u2");?></td>
					<td><?php echo Lang::get("slave.formal_player");?></td>
					<td><?php echo Lang::get("slave.anonymous_player");?></td>
					<td><?php echo Lang::get("slave.anonymous_register_levelup");?></td>
					<td><?php echo Lang::get("slave.formal_create");?></td>
					<td><?php echo Lang::get("slave.anonymous_create");?></td>
					<td><?php echo Lang::get("slave.formal_level10_create");?></td>
					<td><?php echo Lang::get("slave.anonymous_level10_create");?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.ctime}}</td>
					<td>{{t.end_time}}</td>
					<td>{{t.source}}</td>
					<td>{{t.u1}}</td>
					<td>{{t.u2}}</td>
					<td>{{t.count_formal}}</td>
					<td>{{t.count_anonymous}}</td>
					<td>{{t.count_anonymous_formal}}</td>
					<td><div ng-if="t.count_player_formal!=null">{{t.count_player_formal}}({{t.count_player_formal/t.count_formal*100
						| number:2}}%)</div></td>
					<td><div ng-if="t.count_player_anonymous!=null">{{t.count_player_anonymous}}({{t.count_player_anonymous/t.count_anonymous*100
						| number:2}}%)</div></td>
					<td><div ng-if="t.count_lev_formal!=null">{{t.count_lev_formal}}({{t.count_lev_formal/t.count_player_formal*100
						| number:2}}%)</div></td>
					<td><div ng-if="t.count_lev_anonymous!=null">{{t.count_lev_anonymous}}({{t.count_lev_anonymous/t.count_player_anonymous*100
						| number:2}}%)</div></td>
				</tr>

			</tbody>
		</table>
	</div>
</div>
