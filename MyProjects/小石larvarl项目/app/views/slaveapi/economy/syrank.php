<script>
	function getEconomyRankController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/rank',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getEconomyRankController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/economy/rank" method="get" role="form"
				ng-submit="processFrom('/slave-api/economy/rank')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="padding-top: 15px; width: 200px;">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type=3">
						<option value="3"><?php echo Lang::get('slave.mana')?></option>
						<option value="4"><?php echo Lang::get('slave.crystalmnsg')?></option>
						<option value="5"><?php echo Lang::get('slave.energy')?></option>
						<?php if('mnsg' == $game_code){ ?>
						<option value="6"><?php echo Lang::get('slave.arena_coin')?></option>
						<option value="7"><?php echo Lang::get('slave.march_coin')?></option>
						<?php } ?>
					</select>
				</div>
				    <div class="col-md-3" style="padding: 0">
                        <div class="input-group">
                            <quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                    <div class="col-md-3" style="padding: 0">
                        <div class="input-group">
                            <quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
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
		<table class="table table-striped table-hover">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.consumption_statics');?></b></td>
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td>{{t.spend}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>