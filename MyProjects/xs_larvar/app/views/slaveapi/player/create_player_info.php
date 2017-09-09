<script>
	function getCreatePlayerInfoController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.players = {};
		$scope.pagination = {};
		//pagination
		$scope.pagination.totalItems = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.perPage= 100;
		$scope.formData.sign = 0;

		$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
			if ($scope.formData.sign > 0 && newPage != oldPage) {
				$scope.processFrom(newPage);
			}
		});

		$scope.processFrom = function(newPage) {
			$scope.players = {};
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.page = newPage;
			$scope.formData.download = 0;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');

			$http({
				'method' : 'post',
				'url'	 : '/slave-api/server/create/players',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.totalItems = data.count;
				$scope.players = data.players;
				$scope.formData.sign = 1;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};

		$scope.DownloadFrom = function() {
			$scope.players = {};
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.download = 1;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');

			$http({
				'method' : 'post',
				'url'	 : '/slave-api/server/create/players',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				window.location.replace("/slave-api/server/create/players?filename=" + data.filename);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getCreatePlayerInfoController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/player/playerinfo" method="get" role="form"
				ng-submit="processFrom(1)"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" >
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
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
				<div class='clearfix'><br/><br/></div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" style="margin-top-10" value="<?php echo Lang::get('basic.btn_submit') ?>" />
					<input type="button" class="btn btn-primary" style="margin-top-10" value="<?php echo Lang::get('basic.download_csv') ?>" ng-click="DownloadFrom()" />
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

	<div class="row margin-top-10 col-xs-10">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
					<td><b><?php echo 'UID';?></b></td>
					<td><b><?php echo Lang::get('slave.created_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.created_ip'); ?></b></td>
					<td><b><?php echo Lang::get('slave.last_login_time'); ?></b></td>
					<td><b><?php echo Lang::get('slave.player_pay_dollar'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="p in players">
					<td>{{p.player_id}}</td>
					<td>{{p.uid}}{{p.user_id}}</td>
					<td>{{p.created_time}}</td>
					<td>{{p.created_ip}}{{p.remote_host}}</td>
					<td>{{p.last_login_time}}</td>
					<td>{{p.pay_dollar | number : 2}}</td>
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