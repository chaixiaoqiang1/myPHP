<script>
	function getMatchLookUp($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/match/lookup',
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
<div class="col-xs-12" ng-controller="getMatchLookUp">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/match/lookup" method="get" role="form"
				ng-submit="processFrom('/game-server-api/match/lookup')"
				onsubmit="return false;">
				<div class="form-group col-md-8" style="padding:0;">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('serverapi.select_match_type')?></option>
						<option value = "1"><?php echo Lang::get('serverapi.tournament_single_server')?></option>
						<option value = "2"><?php echo Lang::get('serverapi.tournament_cross_server')?></option>
						<option value = "6"><?php echo Lang::get('serverapi.tianxiadiyi')?></option>
						<option value = "7"><?php echo Lang::get('serverapi.tianxiadiyix')?></option>
                        <option value = "8"><?php echo Lang::get('serverapi.sanguojingrui')?></option>
                        <option value = "9"><?php echo Lang::get('serverapi.sanguopojun')?></option>
                        <option value = "10"><?php echo Lang::get('serverapi.sanguoshenwei')?></option>
                        <option value = "11"><?php echo Lang::get('serverapi.sanguotianke')?></option>
                        <option value = "12"><?php echo Lang::get('serverapi.sanguotianyuan')?></option>
                        <option value = "19"><?php echo Lang::get('serverapi.kings_of_kings')?></option>
					</select>
				</div>
				<div class="form-group col-md-8" style="padding:0;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=12>
						<optgroup>
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>	
						</optgroup>	
					</select>
				</div>
				<div class="clearfix"></div>
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

	<div class="col-xs-12" ng-repeat="t in items">
			<div class="panel panel-success" ng-if="is_show==1">
				<div class="panel-heading" ng-if="is_show==1">{{t.title}}</div>
				<table class="table table-striped" ng-if="formData.activity_type==0 && is_show==1">
					<thead>
						<tr class="info">
							<td>serverName</td>
							<td>OperatorID</td>
							<td>PlayerID</td>
							<td>playerName</td>
							<td>Rank</td>
							<td>Contribute</td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="s in t.res">
							<td>{{s.ServerID}}</td>
							<td>{{s.OperatorID}}</td>
							<td>{{s.PlayerID}}</td>
							<td>{{s.Name}}</td>	
							<td>{{s.Rank}}</td>
							<td>{{s.Contribute}}</td>						
						</tr>
					</tbody>
				</table>
				<table class="table table-striped" ng-if="formData.activity_type==1 && is_show==1">
					<thead>
						<tr class="info">
							<td>serverName</td>
							<td>OperatorID</td>
							<td>PlayerID</td>
							<td>GroupID</td>
							<td>Score</td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="s in t.res">
							<td>{{s.ServerID}}</td>
							<td>{{s.OperatorID}}</td>
							<td>{{s.PlayerID}}</td>
							<td>{{s.GroupID}}</td>	
							<td>{{s.Score}}</td>						
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>