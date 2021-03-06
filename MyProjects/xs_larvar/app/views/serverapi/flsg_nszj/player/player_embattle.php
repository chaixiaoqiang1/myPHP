<script>
	function playerEmbattleController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.result = [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', 'Please Check Result:');
				$scope.result = data.result;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="playerEmbattleController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group">
					<div class="col-md-6" style="padding-left: 0;">
						<select class="form-control" name="operation_type"
							ng-model="formData.operation_type" ng-init="formData.operation_type=1">
							<option value="1"><?php echo Lang::get('serverapi.player_embattle') ?></option>	
						</select>
					</div>
					<div class="col-md-6" style="padding-right: 0;">
						<select class="form-control" name="enter_type"
							ng-model="formData.enter_type" ng-init="formData.enter_type=1">
							<option value="1"><?php echo Lang::get('serverapi.single') ?></option>
							<option value="2"><?php echo Lang::get('serverapi.batch') ?></option>	
						</select>
					</div>
				</div>
				<div class="form-group" style="padding-top:20px;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" ng-if ="formData.enter_type == 1">
					<input type="text" class="form-control"
							ng-model="formData.player_id" name="player_id"	required
							placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
				</div>
				<div class="form-group" ng-if ="formData.enter_type == 2">
					<textarea name="text_data" ng-model="formData.text_data"
						placeholder="<?php echo Lang::get('serverapi.gift_bag_batch') ?>"
						rows="12" required class="form-control"></textarea>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<br/>
					<span style = "color:red; font-size:16px;"></span>
				</div>
				<div style="padding-top:5px;">
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit') ?>"
				ng-click="processFrom('/game-server-api/player/embattle')"/>
				</div>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<div class="col-md-4">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
			</div>
		</div>
	</div>
	<div class="col-xs-12" style="min-height:300px">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.result');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="r in result">
					<td>{{r}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>