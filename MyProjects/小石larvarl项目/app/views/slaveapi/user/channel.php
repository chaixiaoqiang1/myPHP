<script> 
function getChannelController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.reg_start_time = null;
    $scope.reg_end_time = null;
	$scope.order_start_time = null;
	$scope.order_end_time = null;
    $scope.formData = {};
    $scope.items = {};
    $scope.sum = {};
    $scope.processFrom = function() {
    	$scope.items = {};
    	$scope.alerts = [];
        alertService.alerts = $scope.alerts;
		$scope.formData.reg_start_time = $filter('date')($scope.reg_start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.reg_end_time = $filter('date')($scope.reg_end_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.order_start_time = $filter('date')($scope.order_start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.order_end_time = $filter('date')($scope.order_end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/user/channel<?php echo ($is_yy? "/yy" : "") ?>',
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
<div class="col-xs-12" ng-controller="getChannelController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('server.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				
				<div class="form-group" style="height: 45px;">
					<div>
						<?php echo Lang::get('slave.reg_time')?>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="reg_start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="reg_end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group" style="height: 45px;">
					<div> 
						<?php echo Lang::get('slave.order_time')?>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="order_start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="order_end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php if(!$is_yy){ ?>
				<div class="form-group" style="height: 15px;">
					<div class="col-md-4" style="padding: 0">
						<select class="form-control" name="filter"
							 ng-model="formData.filter"
							ng-init="formData.filter='source'">
							<option value="source"><?php echo Lang::get('slave.filtrate_by_source') ?></option>
							<option value="u1"><?php echo Lang::get('slave.filtrate_by_u1') ?></option>
							<option value="u2"><?php echo Lang::get('slave.filtrate_by_u2') ?></option>
						</select>
					</div>
					<div class="col-md-4" style="padding: 0">
						<select class="form-control" name="is_anonymous"
							 ng-model="formData.is_anonymous"
							ng-init="formData.is_anonymous=2">
							<option value="2"><?php echo Lang::get('slave.all_user') ?></option>
							<option value="0"><?php echo Lang::get('slave.formal_user') ?></option>
							<option value="1"><?php echo Lang::get('slave.anonymous_user') ?></option>
						</select>
					</div>
					<?php if(2 == $game_type){ ?>
					<div class="col-md-4" style="padding: 0" ng-show="formData.channel_type == 'retention'">
						<select class="form-control" name="os_type"
							 ng-model="formData.os_type"
							ng-init="formData.os_type='all'">
							<option value="all"><?php echo Lang::get('slave.all_os_type') ?></option>
							<option value="android"><?php echo Lang::get('slave.android') ?></option>
							<option value="iOS"><?php echo Lang::get('slave.iOS') ?></option>
						</select>
					</div>
					<?php } ?>
				</div>
				<div class="clearfix">
					<br />
				</div>
				<div class="form-group" style="height: 30px;">
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
				</div>
				<?php } ?>
				<div class="form-group">
					<label>
						<input type="radio" name="channel_type", value="order" ng-model="formData.channel_type" ng-checked="true" ng-init="formData.channel_type = 'order'"/>
						<?php echo Lang::get('slave.channel_order')?>
					</label>
					<label>
						<input type="radio" name="channel_type" value="retention" ng-model="formData.channel_type" />
						<?php echo Lang::get('slave.channel_retention') ?>
					</label>	
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
					<?php if(2 == $game_type){ ?>
					<td ng-show="formData.channel_type == 'retention'"><?php echo Lang::get("slave.os_type");?></td>
					<?php } ?>
					<td><?php echo Lang::get("slave.source");?></td>
					<td><?php echo Lang::get("slave.u1");?></td>
					<td><?php echo Lang::get("slave.u2");?></td>
					<td ><?php echo Lang::get('slave.recharge_number') ?></td>
					<td ><?php echo Lang::get('slave.pay_amount') ?></td>
					<td><?php echo Lang::get('slave.pay_amount_dollar') ?></td>
					<td  ><?php echo Lang::get("slave.create_player_number");?></td>
					<?php foreach (array(2,3,4,5,6,7,14) as $v) {?> 
					<td ><?php echo Lang::get("slave.days_{$v}");?></td>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<?php if(2 == $game_type){ ?>
					<td ng-show="formData.channel_type == 'retention'">{{t.os_type}}</td>
					<?php } ?>
					<td>{{t.source}}</td>
					<td>{{t.u1}}</td>
					<td>{{t.u2}}</td>
					<td>{{t.pay_user_count}}</td>
					<td>{{t.total_amount}}</td>
					<td>{{t.total_dollar_amount}}</td>
					<td >{{t.create_count}}</td>
					<td >{{t.days_2}}
						({{t.days_2 / t.create_count * 100 | number:2}}%)
					</td>
					<td >{{t.days_3}}
						({{t.days_3 / t.create_count * 100 | number:2}}%)
					</td>
					<td >{{t.days_4}}
						({{t.days_4 / t.create_count * 100 | number:2}}%)
					</td>
					<td >{{t.days_5}}
						({{t.days_5 / t.create_count * 100 | number:2}}%)
					</td>
					<td >{{t.days_6}}	
						({{t.days_6 / t.create_count * 100 | number:2}}%)
					</td>
					<td >{{t.days_7}}
						({{t.days_7 / t.create_count * 100 | number:2}}%)
					</td>
					<td >{{t.days_14}}
						({{t.days_14 / t.create_count * 100 | number:2}}%)
					</td>
				</tr>

			</tbody>
		</table>
	</div>
</div>