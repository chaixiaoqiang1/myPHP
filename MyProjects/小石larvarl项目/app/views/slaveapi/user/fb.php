<script> 
function getFBStatController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.items = {};
	$scope.start_time = null;
	$scope.end_time = null;
	$scope.formData.num = 0;
	$scope.formData.lab = 0;
    $scope.processFrom = function() {
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        alertService.alerts = $scope.alerts;
    	$http({
            'method': 'post',
            'url': '/slave-api/user/fb',
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
<div class="col-xs-12" ng-controller="getFBStatController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/user/fb" method="get" role="form"
				ng-submit="processFrom('/slave-api/user/fb')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=5>
						<optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
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
				<div class="form-group" style="height: 20px;">
					<div class="col-md-4" style="padding: 0">
						<div class="input-group">
							<input type="text" class="form-control" ng-model="formData.u1"
								name="u1"
								placeholder="<?php echo Lang::get('slave.enter_u1') ?>" />
						</div>
					</div>
					<div class="col-md-4" style="padding: 2">
						<div class="input-group">
							<input type="text" class="form-control" ng-model="formData.u2"
								name="u2"
								placeholder="<?php echo Lang::get('slave.enter_u2') ?>" />
						</div>
					</div>
					<div class="col-md-4" style="padding: 2">
						<div class="input-group">
							<input type="text" class="form-control" ng-model="formData.diff_hours"
							ng-init="formData.diff_hours=<?php echo $current_diff_hours?>"
								name="diff_hours"
								placeholder="<?php echo Lang::get('slave.enter_diff_hours') ?>" />
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

	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td>Compaign</td>
					<td>Ad</td>
					<td ng-click="predicate='click_through_rate';reverse=!reverse">Ctr</td>
					<td ng-click="predicate='spent';reverse=!reverse">Spent</td>
					<td ng-click="predicate='count_formal_user';reverse=!reverse"><?php echo Lang::get("slave.fb_user");?></td>
					<td ng-click="predicate='count_formal_player';reverse=!reverse"><?php echo Lang::get("slave.fb_player");?></td>
					<td ng-click="predicate='count_formal_lev';reverse=!reverse"><?php echo Lang::get("slave.fb_lev");?></td>
					<td ng-click="predicate='cost_formal_user';reverse=!reverse"><?php echo Lang::get("slave.fb_user_cost");?></td>
					<td ng-click="predicate='cost_formal_player';reverse=!reverse"><?php echo Lang::get("slave.fb_player_cost");?></td>
					<td ng-click="predicate='cost_formal_lev';reverse=!reverse"><?php echo Lang::get("slave.fb_lev_cost");?></td>
					<td ng-click="predicate='total_user';reverse=!reverse"><?php echo Lang::get("slave.fb_total_user");?></td>
					<td ng-click="predicate='total_player';reverse=!reverse"><?php echo Lang::get("slave.fb_total_player");?></td>
					<td ng-click="predicate='total_lev';reverse=!reverse"><?php echo Lang::get("slave.fb_total_lev");?></td>
					<td ng-click="predicate='cost_total_player';reverse=!reverse"><?php echo Lang::get("slave.fb_total_player_cost");?></td>
					<td ng-click="predicate='cost_total_lev';reverse=!reverse"><?php echo Lang::get("slave.fb_total_lev_cost");?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items | orderBy:predicate:reverse">
					<td>{{t.campaign}}</td>
					<td>{{t.fb_u2}}</td>
					<td>{{t.click_through_rate}}</td>
					<td>{{t.spent}}</td>
					<td>{{t.count_formal_user}}</td>
					<td>{{t.count_formal_player}}</td>
					<td>{{t.count_formal_lev}}</td>

					<td>{{t.cost_formal_user}}</td>
					<td>{{t.cost_formal_player}}</td>
					<td>{{t.cost_formal_lev}}</td>

					<td>{{t.total_user}}</td>
					<td>{{t.total_player}}</td>
					<td>{{t.total_lev}}</td>
					<td>{{t.cost_total_player}}</td>
					<td>{{t.cost_total_lev}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>