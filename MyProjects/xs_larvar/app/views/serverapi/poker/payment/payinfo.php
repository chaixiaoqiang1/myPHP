<script>
	function getPokerEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
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
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/user-paydetail?page=' + newPage,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
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
	}
</script>
<div class="col-xs-12" ng-controller="getPokerEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/poker/user-paydetail" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				<div class="form-group">
				</div>
				<div class="form-group col-md-6">
					<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('slave.enter_player_name') ?>"
						 ng-model="formData.player_name" name="player_name" />
				</div>
				<div class="form-group col-md-6">
					<input type="text" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" />
				</div>
				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="height: 30px;">
					<select class="form-control" name="type1" ng-model="formData.type1"
						ng-init="formData.type1=0">
						<option value="0"><?php echo Lang::get('slave.chouma')?></option>
						<option value="1"><?php echo Lang::get('slave.jinbi')?></option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<select class="form-control" name="type2" ng-model="formData.type2"
						ng-init="formData.type2=0">
						<option value="0"><?php echo Lang::get('slave.player_statics')?></option>
						<option value="1"><?php echo Lang::get('slave.player_details')?></option>
					</select>
				</div>
				<div class="clearfix"></div>
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
					<td><b><?php echo Lang::get('slave.consumption_statics');?></b></td>
					<td ng-if="formData.type2 == 1"><?php echo Lang::get('slave.economy_left_number') ?></td>
					<td><b><?php echo Lang::get('slave.operation_name');?></b></td>
					<td>Message</td>
					<td><b><?php echo Lang::get('slave.operation_time');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.spend}}</td>
					<td ng-if="formData.type2 == 1">{{t.left_number}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.action_name}}</td>
					<td>{{t.action_time}}</td>
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