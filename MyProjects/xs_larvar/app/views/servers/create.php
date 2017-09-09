<script>
	function createServerController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.open_server_time = null;
		$scope.processFrom = function(url) {
			$scope.formData.open_server_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.server_track_name = '';
				$scope.formData.server_name = '';
				alert('Success! Continue...');
				window.location.href = window.location;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="createServerController">
	<div class="row">
		<div class="eb-content">
			<form action="/servers" method="post" role="form" ng-submit="processFrom('/servers')" onsubmit="return false;">
				<div class="form-group">
				<input type="text" readonly value="<?php echo $game->game_name;?>" class="form-control"/>	
				</div>
				<div class="form-group">
					open_server_time:
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="10:00:00"></quick-datepicker> 
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="form-group">
					<label for="server_track_name"></label>
					server_track_name:
					<input type="text" class="form-control" id="server_track_name" placeholder="<?php echo Lang::get('server.enter_server_track_name') ?>" required ng-model="formData.server_track_name" name="server_track_name" autofocus="autofocus" ng-autofocus="true" />
				</div>
				
				<div class="form-group">
					<label for="server_name"></label>
					server_name:
					<input type="text" class="form-control" id="server_name" placeholder="<?php echo Lang::get('server.enter_server_name') ?>" required ng-model="formData.server_name" name="server_name" />
				</div>
				
				<div class="form-group">
					<label for="version"></label>
					version:<input type="text" class="form-control" id="version" placeholder="<?php echo Lang::get('server.enter_version') ?>" required ng-model="formData.version" name="version"  ng-init="formData.version='<?php echo $server->version?>'" />
				</div>
				
				<div class="form-group">
					<label for="game_path"></label>
					game_path:<input type="text" class="form-control" id="game_path" placeholder="<?php echo Lang::get('server.enter_game_path') ?>" required ng-model="formData.game_path" name="game_path" ng-init="formData.game_path='<?php echo $server->game_path?>'"/>
				</div>
				
				<div class="form-group">
					<label for="resource_path"></label>
					resource_path:<input type="text" class="form-control" id="resource_path" placeholder="<?php echo Lang::get('server.enter_resource_path') ?>" required ng-model="formData.resource_path" name="resource_path"  ng-init="formData.resource_path='<?php echo $server->resource_path?>'"/>
				</div>
				
				<div class="form-group">
					<label for="xloader_path"></label>
					xloader_path:<input type="text" class="form-control" id="xloader_path" placeholder="<?php echo Lang::get('server.enter_xloader_path') ?>" required ng-model="formData.xloader_path" name="xloader_path" ng-init="formData.xloader_path='<?php echo $server->xloader_path ?>'"/>
				</div>
				
				<div class="form-group">
					<label for="battle_report_path"></label>
					battle_report_path:<input type="text" class="form-control" id="battle_report_path" placeholder="<?php echo Lang::get('server.enter_battle_report_path') ?>" required ng-model="formData.battle_report_path" name="battle_report_path" ng-init="formData.battle_report_path='<?php echo $server->battle_report_path?>'"/>
				</div>
				
				<div class="form-group">
					<label for="server_ip"></label>
					server_ip:<input type="text" class="form-control" id="server_ip" placeholder="<?php echo Lang::get('server.enter_server_ip') ?>" required ng-model="formData.server_ip" name="server_ip" ng-init="formData.server_ip='<?php echo $server->server_ip?>'"/>
				</div>
				
				<div class="form-group">
					<label for="server_port"></label>
					server_port:<input type="text" class="form-control" id="server_port" placeholder="<?php echo Lang::get('server.enter_server_port') ?>" required ng-model="formData.server_port" name="server_port" ng-init="formData.server_port='<?php echo $server->server_port?>'" />
				</div>
				
				<div class="form-group">
					<label for="server_internal_id"></label>
					server_internal_id:<input type="text" class="form-control" id="server_internal_id" placeholder="<?php echo Lang::get('server.enter_server_internal_id') ?>" required ng-model="formData.server_internal_id" name="server_internal_id" ng-init="formData.server_internal_id='<?php echo $server->server_internal_id?>'" />
				</div>

				<div class="form-group">
					<label for="match_port"></label>
					match_port:<input type="text"  class="form-control" id="match_port" placeholder="<?php echo Lang::get('server.enter_match_port') ?>" required ng-model="formData.match_port" name="match_port" ng-init="formData.match_port='<?php echo $server->match_port?>'" />
				</div>
			
				
				<div class="form-group">
					<label for="api_server_ip"></label>
					api_server_ip:<input type="text" class="form-control" id="api_server_ip" placeholder="<?php echo Lang::get('server.enter_api_server_ip') ?>" required ng-model="formData.api_server_ip" name="api_server_ip" ng-init="formData.api_server_ip='<?php echo $server->api_server_ip?>'"/>
				</div>
				
				<div class="form-group">
					<label for="api_server_port"></label>
					api_server_port:<input type="text" class="form-control" id="api_server_port" placeholder="<?php echo Lang::get('server.enter_api_server_port') ?>" required ng-model="formData.api_server_port" name="api_server_port" ng-init="formData.api_server_port='<?php echo $server->api_server_port?>'"/>
				</div>
				
				<div class="form-group">
					<label for="api_dir_id"></label>
					api_dir_id:<input type="text" class="form-control" id="api_dir_id" placeholder="<?php echo Lang::get('server.enter_api_dir_id') ?>" required ng-model="formData.api_dir_id" name="api_dir_id" ng-init="formData.api_dir_id='<?php echo $server->api_dir_id?>'"/>
				</div>
				<div class="form-group">
					tp_server_id:<input type="text" class="form-control" placeholder="<?php echo Lang::get('server.enter_tp_server_id') ?>" ng-model="formData.tp_server_id" name="tp_server_id" ng-init="formData.tp_server_id=<?php echo $server->tp_server_id?>"/>
				</div>
				<div class="form-group">
					qq_zone_id:<input type="text" class="form-control" placeholder="<?php echo Lang::get('server.enter_qq_zone_id') ?>" ng-model="formData.qq_zone_id" name="qq_zone_id" ng-init="formData.qq_zone_id=<?php echo $server->qq_zone_id?>"/>
				</div>
				<div class="form-group">
					<label for="use_for_month_card"></label>
					<?php echo Lang::get('server.is_use_for_month_card')?>
					<select name="use_for_month_card" ng-model="formData.use_for_month_card" ng-init="formData.use_for_month_card=<?php echo $server->use_for_month_card ?>" class="form-control">
						    <option value="1"><?php echo Lang::get('server.use_for_month_card_on') ;?></option>
						    <option value="0"><?php echo Lang::get('server.use_for_month_card_off')?></option>
						?>
					</select>
				</div>
				
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>
		</div>
	</div>
	
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>