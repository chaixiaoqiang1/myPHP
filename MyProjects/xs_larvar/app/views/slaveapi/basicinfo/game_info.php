<script>
    function getBasicGameInfoController($scope, $http, alertService, $filter) {  
    	$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];

		$scope.processFrom = function(newPage) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/game/basic/info',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.dates = data;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		};
}
</script>
<div class="col-xs-12" ng-controller="getBasicGameInfoController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<div class="col-md-6">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<br><br>
				<div class="col-md-10">
					<select class="form-control" name="server_ids" required
						id="select_game_server" ng-model="formData.server_ids"
						ng-init="formData.server_ids=0" multiple="true" size="10">
						<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary"
							value="<?php echo Lang::get('basic.btn_submit') ?>" />
				</div>
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
					<td><b><?php echo Lang::get('slave.date');?></b></td>
					<td><b><?php echo Lang::get('slave.new_sign_num');?></b></td>
					<td><b><?php echo Lang::get('slave.new_create_num');?></b></td>
					<td><b>DAU</b></td>
					<td><b><?php echo Lang::get('slave.login_num_not_today');?></b></td>
					<?php if('yysg' != $game_code){ ?>
						<td><b><?php echo Lang::get('slave.max_online');?></b></td>
						<td><b><?php echo Lang::get('slave.avg_online');?></b></td>
						<td><b><?php echo Lang::get('slave.avg_online_time');?></b></td>
					<?php } ?>
					<td><b><?php echo Lang::get('slave.add_pay_user');?></b></td>
					<td><b><?php echo Lang::get('slave.old_pay_user');?></b></td>
					<td><b><?php echo Lang::get('slave.pay_user_num');?></b></td>
					<td><b><?php echo Lang::get('slave.pay_times');?></b></td>
					<td><b><?php echo Lang::get('slave.pay_dollar');?></b></td>
					<?php if(2 == $game_type){ ?>
						<td><b><?php echo Lang::get('slave.android_pay_dollar');?></b></td>
						<td><b><?php echo Lang::get('slave.iOS_pay_dollar');?></b></td>
						<td><b><?php echo Lang::get('slave.Unknown_pay_dollar');?></b></td>
					<?php } ?>
					<td><b><?php echo Lang::get('slave.pay_num_login_num_rate');?></b></td>
					<td><b>ARPU</b></td>
					<td><b>ARPPU</b></td>
					<?php 
					$days = array(2,3,4,5,6,7,14,30);
					foreach ($days as $day) { ?>
						<td><b><?php echo Lang::get("slave.days_$day");?></b></td>
					<?php } ?> 
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in dates">
					<td><nobr>{{t.date}}</nobr></td>
					<td>{{t.new_sign}}</td>
					<td>{{t.new_create}}</td>
					<td>{{t.login_num}}</td>
					<td>{{t.login_num_not_today}}</td>
					<?php if('yysg' != $game_code){ ?>
						<td>{{t.max_online}}</td>
						<td>{{t.avg_online | number : 2}}</td>
						<td>{{t.avg_online*24/t.login_num | number : 2}}</td>
					<?php } ?>
					<td>{{t.new_pay_user}}</td>
					<td>{{t.pay_user_num - t.new_pay_user}}</td>
					<td>{{t.pay_user_num}}</td>
					<td>{{t.pay_times}}</td>
					<td>{{t.pay_dollar | number : 2}}</td>
					<?php if(2 == $game_type){ ?>
						<td>{{t.android_dollar | number : 2}}</td>
						<td>{{t.iOS_dollar | number : 2}}</td>
						<td>{{t.Unknown_dollar | number : 2}}</td>
					<?php } ?>
					<td>{{t.pay_user_num*100/t.login_num | number : 2}}%</td>
					<td>{{t.pay_dollar/t.login_num | number : 2}}</td>
					<td>{{t.pay_dollar/t.pay_user_num | number : 2}}</td>
					<?php 
					$days = array(2,3,4,5,6,7,14,30);
					foreach ($days as $day) { ?>
						<td><?php echo "{{t.days_$day}}({{t.days_$day / t.created_player_number * 100 | number : 2}}%)";?></td>
					<?php } ?> 
				</tr>
			</tbody>
		</table>
	</div>
</div>