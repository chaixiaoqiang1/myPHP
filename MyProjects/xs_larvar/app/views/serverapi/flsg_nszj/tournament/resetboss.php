<script>
	function sesetBoss($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/reset/leagueBoss',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
		};
	}
</script>
<div class="col-xs-12" ng-controller="sesetBoss">
	<div class="row">
		<div class="eb-content">
			<div class="form-group col-md-8" style="padding:0;">
				<select class="form-control" name="type"
					id="type" ng-model="formData.type"
					ng-init="formData.type=0">
					<option value="0"><?php echo Lang::get('serverapi.reset_leagueBoss')?></option>
					<option value="1"><?php echo Lang::get('serverapi.reset_accumulate_login_times')?></option>
				</select>
			</div>
			<div class="form-group col-md-8" style="padding:0;">
				<select class="form-control" name="server_id"
					id="select_game_server" ng-model="formData.server_id"
					ng-init="formData.server_id=0">
					<option value="0"><?php echo Lang::get('serverapi.select_server')?></option>
					<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
					<?php } ?>		
				</select>
			</div>
				<div class="form-group col-md-8" style="padding:0;" ng-if="formData.type==0">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('serverapi.enter_party_id')?>"
						required ng-model="formData.league_id" name="league_id" />
				</div>
				<div class="clearfix"></div>
				<div class="form-group" ng-if="formData.type==1">
					<div class="form-group col-md-4" style="padding:0;">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.playerid')?>"
							required ng-model="formData.player_id" name="player_id" />
					</div>
					<div class="form-group col-md-4">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.add_times')?>"
							required ng-model="formData.add_times" name="add_times" />
					</div>
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="button" class="btn btn-warning" value="<?php echo Lang::get('basic.btn_submit') ?>" 
						ng-click="process()"/>
					</div>
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>