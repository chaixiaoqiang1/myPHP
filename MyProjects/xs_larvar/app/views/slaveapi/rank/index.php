<script>
    function getPlayerRankController($scope, $http, alertService, $filter) {  
    	$scope.alerts = [];
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
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.levelup_time = $filter('date')($scope.levelup_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/player/rank?page=' + newPage,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.perPage= data.per_page;
				$scope.pagination.totalItems = data.count;
				$scope.items = data.items;
				location.hash = '#top';
			});
		};

		$scope.download = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.levelup_time = $filter('date')($scope.levelup_time, 'yyyy-MM-dd HH:mm:ss');
		    $http({
		        'method': 'post',
		        'url': url,
		        'data': $.param($scope.formData),
		        'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
	    	}).success(function(data) {
	    		alertService.add('success', 'OK');
	    		console.log(data.now);
	        	window.location.replace("/slave-api/player/rank/download?now=" + data.now);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
}
</script>
<div class="col-xs-12" ng-controller="getPlayerRankController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/player/rank" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				<div class="col-md-6" style="padding-left:0; width:35%;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="col-md-6" style="width:35%;">
					<select class="form-control" name="is_created_time"
						id="is_created_time" ng-model="formData.is_created_time"
						ng-init="formData.is_created_time=0">
						<option value="0">不限制角色创建时间</option>
						<option value="1">限制角色创建时间</option>		
					</select>
				</div>
				<br>
				<div class="form-group" ng-show="formData.is_created_time == 1">
					<div class="col-md-5" style="padding-left:0;width:35%;">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5" style="width:35%;">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<br>
				<div class="form-group col-md-4" style="padding-left:0; width:35%;">
					<input type="number" class="form-control"
						placeholder="<?php echo Lang::get('slave.min_level')?>"
						ng-model="formData.level_lower_bound" name="level_lower_bound"?>
				</div>
				<div class="form-group col-md-4" style="width:35%;">
					<input type="number" class="form-control"
						placeholder="<?php echo Lang::get('slave.max_level')?>"
						ng-model="formData.level_upper_bound" name="level_upper_bound"?>
				</div>
				<div class="form-group">
					<div class="col-md-6" style="padding-left:0; width:35%;">
					<b>角色升级截止时间：</b>
						<div class="input-group">
							<quick-datepicker ng-model="levelup_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-8" style="padding: 0 0 0 0">
						<input type="submit" class="btn btn-default"
								value="<?php echo Lang::get('basic.btn_submit') ?>" />
						<input type='button' class="btn btn-warning" style="padding-left:8px;"
							value="<?php echo Lang::get('serverapi.download_csv') ?>"
							ng-click="download('/slave-api/player/rank/download')" />
					</div>
				</div>
			</form>
		</div>
		<p><font color=red>1：默认不进行时间限制，查询全服玩家等级排行</font></p>
		<p><font color=red>2：下载操作的时候是不进行分页的，数据比较多，建议选择时间来进行限制</font></p>
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
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
					<td><b><?php echo Lang::get('slave.level');?></b></td>
					<td><b><?php echo Lang::get('slave.levelup_time');?></b></td>
					<td><b>创建角色时间</b></td>
					<td><b><?php echo Lang::get('slave.created_ip');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.rank}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.level}}</td>
					<td>{{t.levelup_time}}</td>
					<td>{{t.created_time}}</td>
					<td>{{t.created_ip}}</td>
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