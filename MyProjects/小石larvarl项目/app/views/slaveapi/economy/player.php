<script src="/js/auto_input.js"></script>
<script>
	function getPlayerEconomyController($scope, $http, alertService, $filter) {
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
			$scope.items = [];
			$scope.formData.action_type_num = document.getElementById("action_type_num").value;
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/player?page=' + newPage,
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
<div class="col-xs-12" ng-controller="getPlayerEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/economy/player" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				<div class="form-group col-md-6">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-6">
					<input type="text" class="form-control" style="padding: 0;height:30px;font-size:12pt;overflow-y:auto;" id="action_type_num" onkeyup="autoComplete.start(event)"
						autocomplete="off" placeholder="<?php echo Lang::get('slave.action_type_num') ?>"
						 ng-model="formData.action_type_num" name="action_type_num" />
						<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>
				</div>
				<div class="clearfix"></div>
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
						<option value="0"><?php echo Lang::get('slave.yuanbao')?></option>
						<option value="1"><?php echo Lang::get('slave.tongqian')?></option>
						<option value="2"><?php echo Lang::get('slave.shengwang')?></option>
						<option value="3"><?php echo Lang::get('slave.tili')?></option>
						<option value="4"><?php echo Lang::get('slave.jingjiedian')?></option>
						<option value="5"><?php echo Lang::get('slave.yueli')?></option>
						<option value="6"><?php echo Lang::get('slave.xianling')?></option>
						<option value="7"><?php echo Lang::get('slave.boat_book')?></option>
						<option value="8"><?php echo Lang::get('slave.lingshi')?></option>
						<?php if('flsg' == $game_code) {?>
							<option value="9"><?php echo Lang::get('slave.star_fragment')?></option>
							<option value="10"><?php echo Lang::get('slave.talent_point')?></option>
							<option value="11"><?php echo Lang::get('slave.heaven_token')?></option>
							<option value="12"><?php echo Lang::get('slave.skill_fragment')?></option>
							<option value="13"><?php echo Lang::get('slave.fight_spirit')?></option>
							<option value="14"><?php echo Lang::get('slave.rings_exp')?></option>
							<option value="15"><?php echo Lang::get('slave.power')?></option>
							<option value="16"><?php echo Lang::get('slave.mount_fragment')?></option>
							<option value="17"><?php echo Lang::get('slave.jing_po')?></option>
							<option value="18"><?php echo Lang::get('slave.follow_card')?></option>
							<option value="19"><?php echo Lang::get('slave.fruit_currency')?></option>
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
		<div class="form-group col-xs-6">
			<span style = "color:red; font-size:16px"><?php echo Lang::get('serverapi.consume_intrdouce1')?></span><br/>
			<span style = "color:red; font-size:16px"><?php echo Lang::get('serverapi.consume_intrdouce2')?></span></br>
			<span style = "color:red; font-size:16px"><?php echo Lang::get('slave.consume_intrdouce3')?></span>
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
					<td ng-if="formData.type2 == 1 && formData.type1 == 19"><?php echo Lang::get('slave.fruit_bet') ?></td>
					<td><b><?php echo Lang::get('slave.action_name');?></b></td>
					<td ng-if="formData.type2 == 0">playerID</td>
					<td ng-if="formData.type2 == 1">Message</td>
					<td ng-if="formData.type2 == 0"><b><?php echo Lang::get('slave.operation_times');?></b></td>
					<td ng-if="formData.type2 == 1"><b><?php echo Lang::get('slave.operation_time');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.spend}}</td>
					<td ng-if="formData.type2 == 1">{{t.left_number}}</td>
					<td ng-if="formData.type2 == 1 && formData.type1 == 19">{{t.fruit_bet}}</td>
					<td>{{t.action_type}}</td>
					<td ng-if="formData.type2 == 0">{{t.player_id}}</td>
					<td ng-if="formData.type2 == 1">{{t.action_name}}</td>
					<td ng-if="formData.type2 == 0">{{t.times}}</td>
					<td ng-if="formData.type2 == 1">{{t.action_time}}</td>
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
<script>
    var autoComplete=new AutoComplete('action_type_num','auto',[<?php 
    	foreach ($mids as $k => $v) {
    		echo "'".$v['mid'].':'.$v['desc']."',";
    	} ?>
    ]);
</script>