<script> 
function getUserStatController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.end_time = null;
    $scope.formData = {};
    $scope.items = {};
    $scope.pay_statics = {};
    $scope.processFrom = function(type) {
        alertService.alerts = $scope.alerts;
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
		if(0 == type){	//不显示u1
			$scope.formData.filter_u1 = 0;
			$scope.formData.short_u1 = 0;
		}else if(1 == type){	//显示u1
			$scope.formData.filter_u1 = 1;
			$scope.formData.short_u1 = 0;
		}else if(2 == type){	//合并查询
			$scope.formData.filter_u1 = 1;
			$scope.formData.short_u1 = 1;
		}else{
			$scope.formData.filter_u1 = 1;
			$scope.formData.short_u1 = 0;
		}
        $http({
            'method': 'post',
            'url': '/slave-api/user/weekly',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.user_counts = data.user_counts;
            $scope.pay_statics = data.pay_statics;
            $scope.channel_counts = data.channel_counts;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getUserStatController">
	<div class="row">
		<div class="col-xs-12">
			<form action="/slave-api/user/weekly" method="get" role="form"
				ng-submit="processFrom(1)"
				onsubmit="return false;">
				<div class="form-group" style="height: 35px;">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="14:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>

				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
				<?php if(2 == $game_type){ ?>
					<input type="button" class="btn btn-default" style=""
					ng-click="processFrom(0)" value="<?php echo Lang::get('basic.not_u1') ?>" />
					<input type="button" class="btn btn-default" style=""
					ng-click="processFrom(2)" value="<?php echo Lang::get('basic.merge_region') ?>" />
				<?php } ?>
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
		<div class="panel panel-success">
			<div class="panel-heading"><?php echo Lang::get('slave.weekly_register_statics') ?></div>
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<?php if($game_type == 2){ ?><td><?php echo Lang::get("slave.os_type");?></td><?php } ?>
						<td><?php echo Lang::get("slave.source");?></td>
						<td><?php echo Lang::get("slave.u1");?></td>
						<td><?php echo Lang::get("slave.sum_register_player");?></td>
						<td><?php echo Lang::get("slave.sum_player_create");?></td>
						<?php if($game_type == 2){ ?><td><?php echo Lang::get("slave.count_setup");?></td><?php } ?>
						<!--<td><?php echo Lang::get("slave.sum_level10_create");?></td>-->
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in user_counts">
						<?php if($game_type == 2){ ?><td>{{t.os_type}}</td><?php } ?>
						<td>{{t.source}}</td>
						<td>{{t.u1}}</td>
						<td>{{t.count_formal}}</td>
						<td>{{t.count_player}}</td>
						<?php if($game_type == 2){ ?><td>{{t.count_device}}</td><?php } ?>
						<!--<td>{{t.count_lev10_player}}</td>-->
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php if($game_type == 2){ ?>
		<div class="col-xs-12" ng-repeat="week in channel_counts">
			<div class="panel panel-success">
				<div class="panel-heading">{{week.title}}</div>
				<table class="table table-striped">
					<thead>
						<tr class="info">
							<td><?php echo Lang::get("slave.channel");?></td>
							<td><?php echo Lang::get("slave.created_player_number");?></td>
							<td><?php echo Lang::get("slave.recharge_number");?></td>
							<td><?php echo Lang::get("slave.order_recharge_dollar");?></td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="t in week.data track by $index">
							<td>{{t.channel}}</td>
							<td>{{t.create_player_num}}</td>
							<td>{{t.pay_num}}</td>
							<td>{{t.pay_dollar | number:2}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>	
	<?php } ?>
	<div class="col-xs-12" ng-repeat="week in pay_statics">
		<div class="panel panel-success">
			<div class="panel-heading">{{week.title}}</div>
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<?php if($game_type == 2){ ?><td><?php echo Lang::get("slave.os_type");?></td><?php } ?>
						<td><?php echo Lang::get("slave.source");?></td>
						<td><?php echo Lang::get("slave.u1");?></td>
						<td><?php echo Lang::get("slave.recharge_number");?></td>
						<td><?php echo Lang::get("slave.pay_amount_dollar");?></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in week.data track by $index">
						<?php if($game_type == 2){ ?><td>{{t.os_type}}</td><?php } ?>
						<td>{{t.source}}</td>
						<td>{{t.u1}}</td>
						<td>{{t.pay_user_count}}</td>
						<td>{{t.total_dollar_amount}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>