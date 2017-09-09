<script> 
function getYuanbaoRankController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.start_time = '';
    $scope.end_time = '';
    $scope.formData = {};
	$scope.items = [];
	$scope.pagination = {};
	//pagination
	$scope.pagination.totalItems = 0;
	$scope.pagination.currentPage = 1;
	$scope.pagination.perPage= 1;

	$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
		if ($scope.formData.server_id > 0) {
			$scope.processFrom(newPage);
		}
	});
    $scope.processFrom = function(newPage) {
    	$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
		alertService.alerts = $scope.alerts;
		var form_url = '<?php echo Request::url(); ?>';
        $http({
            'method': 'post',
            'url': form_url+ '?page=' + newPage,
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.pagination.currentPage = data.current_page;
			$scope.pagination.perPage= data.per_page;
			$scope.pagination.totalItems = data.count;
			$scope.items = data.items;
			location.hash = '#top';
        }).error(function(data) {
			alertService.add('danger', data.error);
		});
    };
    $scope.download = function(server_id, player_id, player_name, uid, vip_level, rank) {
        console.log(server_id);
        console.log(player_id);
    	alertService.alerts = $scope.alerts;
    	$scope.formData.server_id = server_id;
    	$scope.formData.player_id = player_id;
    	$scope.formData.player_name = player_name;
    	$scope.formData.uid = uid;
    	$scope.formData.vip_level = vip_level;
    	$scope.formData.rank = rank;
    	$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/payment/rank/download',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	alertService.add('success', 'OK');
        	console.log(data.now);
            window.location.replace("/slave-api/payment/rank/download?now=" + data.now);
        }).error(function(data) {
			alertService.add('danger', data.msg);
		});
    };
} 
</script>
<div class="col-xs-12" ng-controller="getYuanbaoRankController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/payment/order/rank"  method="post"
				role="form" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('slave.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name.' ['.Lang::get('slave.open_server_time').'] '.date("Y-m-d H:i:s",$v->open_server_time);?></option>
						<?php } ?>		
					</select>
				</div>
				<?php if (Request::is('slave-api/payment/order/rank-cehua')) {?>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="form-group">
					Per Page: <label> <input name="per_page" value="30" type="radio"
						ng-checked="true" ng-model="formData.per_page"
						ng-init="formData.per_page=30" /> 30
					</label> <label> <input name="per_page" value="100" type="radio"
						ng-model="formData.per_page" /> 100
					</label> <label> <input name="per_page" value="500" type="radio"
						ng-model="formData.per_page" /> 500
					</label> <label> <input name="per_page" value="1000" type="radio"
						ng-model="formData.per_page" /> 1000
					</label> <label> <input name="per_page" value="2000" type="radio"
						ng-model="formData.per_page" /> 2000
					</label>
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type="submit" class="btn btn-default"
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
				</div>
			</form>
		</div>
		<!-- /.col -->
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
					<td><b><?php echo Lang::get('slave.rank');?></b></td>
					<td><b><?php echo Lang::get('slave.player_uid');?></b></td>
					<td><b><?php echo Lang::get('slave.user_name');?></b></td>
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
					<td><b><?php echo Lang::get('slave.region');?></b></td>
					<?php if (Request::is('slave-api/payment/order/rank')) {?>
					<td><b>u1</b></td>
					<td><b>u2</b></td>
					<td><b>source</b></td>
					<td><b><?php echo Lang::get('slave.is_anonymous_ever');?></b></td>
					<td><b><?php echo Lang::get('slave.facebook_id');?></b></td>
					<?php }?>
					<td><b><?php echo Lang::get('slave.vip_level');?></b></td>
					<td><b><?php echo Lang::get('slave.order_recharge_yuanbao');?></b></td>
					<td><b><?php echo Lang::get('slave.order_recharge_dollar');?></b></td>
					<td><b><?php echo Lang::get('slave.order_recharge_amount');?></b></td>
					<td><b><?php echo Lang::get('slave.avg_recharge');?></b></td>
					<td><b><?php echo Lang::get('slave.recharge_times');?></b></td>
					<td><b><?php echo Lang::get('slave.first_dev');?></b></td>
					<td><b><?php echo Lang::get('slave.no_recharege_days');?></b></td>
					<td><b><?php echo Lang::get('slave.no_visit_days');?></b></td>
					<td><b><?php echo Lang::get('slave.download_player_statics');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.rank}}</td>
					<td>{{t.uid}}</td>
					<td>{{t.nickname}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.region}}</td>
					<?php if (Request::is('slave-api/payment/order/rank')) {?>
					<td>{{t.u}}</td>
					<td>{{t.u2}}</td>
					<td>{{t.source}}</td>
					<td>{{t.is_anonymous_ever}}</td>
					<td>{{t.tp_user_id}}</td>
					<?php } ?>
					<td>{{t.vip_level}}</td>
					<td>{{t.total_yuanbao_amount|number:2}}</td>
					<td>{{t.total_dollar_amount|number:2}}</td>
					<td>{{t.total_amount|number:2}}</td>
					<td>{{t.avg_amount|number:2}}</td>
					<td>{{t.count}}</td>
					<td>{{t.first_lev}}</td>
					<td>{{t.no_recharge_days}}</td>
					<td>{{t.no_visit_days}}</td>
					<td><input type='button' class="btn btn-warning"
						value="<?php echo Lang::get('slave.download') ?>"
						ng-click="download(formData.server_id, t.player_id, t.player_name, t.uid, t.vip_level, t.rank)" /></td>
				</tr>
			</tbody>
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>