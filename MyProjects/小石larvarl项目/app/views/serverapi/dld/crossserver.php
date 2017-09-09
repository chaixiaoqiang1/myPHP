<script>
	function crossServerTournamentController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				var len = result.length;
				for (var i=0; i < len; i++) {
					if (result[i].status == 'ok') {
						alertService.add('success', result[i].msg);
					} else if (result[i]['status'] == 'error') {
	            		alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
		$scope.lookup = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
	            
	            for (var i = 0; i < data.length; i++) {
	            	alertService.add('success',data[i].server_name)
	            };
	            //alertService.add('success', data.result);
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};

		$scope.look = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
	            
	            for (var i = 0; i < data.length; i++) {
	            	alertService.add('success',data[i])
	            };
	            //alertService.add('success', data.result);
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="crossServerTournamentController">
	<div class="row">
		<div class="col-xs-8">
			<div class="form-group">
				<select class="form-control" name="server_id"
					id="select_game_server" ng-model="formData.server_id"
					ng-init="formData.server_id=0" multiple="multiple"
					ng-multiple="true" size=20>
					<optgroup
						label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
				</select>
			</div>

			<div class="form-group" style="height: 40px;">
				<div class="input-group">
					<quick-datepicker ng-model="start_time" init-value="11:00:00"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i> <span><i><?php echo Lang::get('serverapi.kingbattle_cross_server_time') ?></i></span>
				</div>

			</div>

			<div class="form-group" style="height: 40px;">
				<div class="col-md-6" style="padding: 0">
					<select class="form-control" name="server_id2"
						id="select_game_server" ng-model="formData.server_id2"
						ng-init="formData.server_id2=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_id."==".$v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
			</div>
			<div class="form-group" style="height: 40px;">
				
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.kingbattle_cross_open') ?>"
						ng-click="process('/game-server-api/kingBattle/cross-server')" />
				
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.kingbattle_cross_connect') ?>"
						ng-click="process('/game-server-api/kingBattle/cross-server/update')" style="margin-left:5px"/>
				
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.kingbattle_cross_signup') ?>"
						ng-click="process('/game-server-api/kingBattle/cross-server/signup')" style="margin-left:5px"/>
				
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.tournament_lookup') ?>"
						ng-click="lookup('/game-server-api/kingBattle/cross-server/lookup')" style="margin-left:5px"/>
				
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.tournament_look') ?>"
						ng-click="look('/game-server-api/kingBattle/cross-server/look')" style="margin-left:5px"/>
				
			</div>
			<div class="alert alert-danger">
				<b><?php echo Lang::get('serverapi.tournament_attention') ?></b>
			</div>
			<div class="form-group" style="height: 30px;">
				<input type="text" class="form-control" ng-model="formData.id"
					name="id"
					placeholder="<?php echo Lang::get('serverapi.enter_id') ?>" />
			</div>
			<div class="form-group" style="height: 30px;">
				<input type="text" class="form-control" ng-model="formData.password"
					name="password"
					placeholder="<?php echo Lang::get('serverapi.enter_password') ?>" />

			</div>
			<div class="col-md-4" style="padding: 0">
				<input type="button" class="btn btn-danger"
					value="<?php echo Lang::get('serverapi.close') ?>"
					ng-click="process('/game-server-api/kingBattle/cross-server/close')" />
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<?php foreach ($period as $k => $v) {?>
	<div class="panel <?php if ($k == 9999) {?>panel-warning<?php }else{?>panel-info<?php } ?>">
		<div class="panel-heading">
			<?php echo Lang::get('serverapi.tournament_period_' . $k) ?></div>
		<div class="panel-body">
		<?php foreach ($v as $vv) {?>
			<p>
			<?php echo $vv->server_name ?>
			<?php echo $vv->match ? date('Y-m-d H:i:s', $vv->match->start_time) : ''?>	
			Match: <?php echo $vv->match ? $vv->match->match : ''?>	
			Round: <?php echo $vv->match ? $vv->match->round: ''?>	
			</p>	
		<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>