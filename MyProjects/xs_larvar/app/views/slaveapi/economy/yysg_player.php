<script>
	function getYysgPlayerEconomyController($scope, $http, alertService, $filter) {
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
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/yysg/player?page=' + newPage,
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
<div class="col-xs-12" ng-controller="getYysgPlayerEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/economy/yysg/player" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				  <div class="form-group col-md-6">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=<?php echo $server_init ?$server_init:0 ?>">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-6">
					<input type="text" class="form-control" id="action_type_num"
						placeholder="<?php echo Lang::get('slave.action_type_num') ?>"
						 ng-model="formData.action_type_num" name="action_type_num" />
				</div>
				<div class="form-group col-md-6">
					<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('slave.enter_player_name') ?>"
						 ng-model="formData.player_name" name="player_name" />
				</div>
				<div class="form-group col-md-6">
					<input type="text" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" 
						 ng-init="formData.player_id=<?php echo $player_id==0?'':$player_id; ?>"/>
				</div>
				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="height: 30px;">
					<select class="form-control" name="type1" ng-model="formData.type1"
						ng-init="formData.type1=0">
						<option value="0"><?php echo Lang::get('slave.mana')?></option>
						<option value="1"><?php echo Lang::get('slave.crystal')?></option>
						<option value="3"><?php echo Lang::get('slave.energy')?></option>
						<?php if('yysg' == $game_code){ ?>
							<option value="2"><?php echo Lang::get('slave.social')?></option>
							<option value="4"><?php echo Lang::get('slave.invitation')?></option>
							<option value="5"><?php echo Lang::get('slave.glory')?></option>
							<option value="6"><?php echo Lang::get('slave.point')?></option>
							<option value="10"><?php echo Lang::get('slave.guild_coin_yysg')?></option>
						<?php } ?>
						<?php if('mnsg' == $game_code){ ?>
							<option value="7"><?php echo Lang::get('slave.arena_coin')?></option>
							<option value="8"><?php echo Lang::get('slave.march_coin')?></option>
							<option value="9"><?php echo Lang::get('slave.top_coin')?></option>
							<option value="10"><?php echo Lang::get('slave.guild_coin')?></option>
							<option value="11"><?php echo Lang::get('slave.region_coin')?></option>
						<?php } ?>
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

				<div class="form-group">
					<input type="radio" name="look_type"
						ng-model="formData.look_type" value="1" ng-value="1"
						ng-init="formData.look_type=1" />
					<?php echo Lang::get('serverapi.get_data');?>
					<input type="radio" name="look_type"
						ng-model="formData.look_type" value="2" ng-value="2" />
					<?php echo Lang::get('serverapi.consume_data');?>
					<input type="radio" name="look_type"
						ng-model="formData.look_type" value="3" ng-value="3" />
					<?php echo Lang::get('serverapi.total_data');?>
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
					<td><b><?php echo Lang::get('slave.action_name');?></b></td>
					<td>玩家ID</td>
					<td ng-if="formData.type2 == 0"><b><?php echo Lang::get('slave.operation_times');?></b></td>
					<td ng-if="formData.type2 == 1"><b><?php echo Lang::get('slave.operation_time');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.diff}}</td>
					<td ng-if="formData.type2 == 1">{{t.left_number}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.created_at}}</td>
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