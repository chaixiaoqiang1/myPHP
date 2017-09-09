<script>
	function getGMRepliedController($scope, $http, $filter, alertService) {
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
		$scope.is_watch=0; 
		$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
			if ($scope.is_watch > 0) {
				$scope.processFrom(newPage);
			}
		});
		$scope.processFrom = function(newPage) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/gm/replied?page=' + newPage,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.is_watch=1;
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
<div class="col-xs-12" ng-controller="getGMRepliedController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/game-server-api/gm/replied" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=[<?php echo $server_init; ?>]" multiple="multiple" ng-multiple="true" size=10 >
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('slave.enter_player_name') ?>"
						ng-model="formData.player_name" name="player_name" ng-init="formData.player_name='<?php echo $player_name; ?>'"/>
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
				<div class="clearfix"></div>
				<div class="form-group" style="padding-top: 15px; width: 200px;">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('serverapi.all_question_type')?></option>
						<option value="1"><?php echo Lang::get('serverapi.gm_type_bug')?></option>
						<option value="2"><?php echo Lang::get('serverapi.gm_type_complaint')?></option>
						<option value="3"><?php echo Lang::get('serverapi.gm_type_advice')?></option>
						<option value="4"><?php echo Lang::get('serverapi.gm_type_other')?></option>
					</select>
				</div>
				<input type="submit" class="btn btn-default"
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
					<td><?php echo Lang::get('serverapi.gm_id');?></td>
					<td><?php echo Lang::get('serverapi.gm_type');?></td>
					<td><?php echo Lang::get('serverapi.gm_player_name');?></td>
					<td><?php echo Lang::get('serverapi.gm_player_id');?></td>
					<td><?php echo Lang::get('serverapi.message');?></td>
					<td><?php echo Lang::get('serverapi.reply_message');?></td>
					<td><?php echo Lang::get('serverapi.reply_user_name');?></td>
					<td><?php echo Lang::get('serverapi.send_time');?></td>
					<td><?php echo Lang::get('serverapi.reply_time');?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="p in items">
					<td>{{p.gm_id}}</td>
					<td>{{p.gm_type_name}}</td>
					<td>{{p.player_name}}</td>
					<td>{{p.player_id}}</td>
					<td>{{p.message}}</td>
					<td>{{p.reply_message}}</td>
					<td>{{p.username}}</td>
					<td>{{p.send_time}}</td>
					<td>{{p.replied_time}}</td>
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