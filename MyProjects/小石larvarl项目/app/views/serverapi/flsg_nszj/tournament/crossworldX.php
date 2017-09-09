<script>
	function crossWorldLordsController($scope, $http, alertService, $filter){
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data.result;
				var len = result.length;
				for (var i=0; i<len; i++){
					if (result[i].status == 'ok') {
						alertService.add('success', result[i].msg);
					}else if(result[i]['status'] == 'error'){
						alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
		$scope.lookup = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data': $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
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
		$scope.look = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time= $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                };
			}).eerror(function (data) {
                alertService.add('danger', data.error);
            });
		}
	}
</script>
<div class="col-xs-12" ng-controller="crossWorldLordsController">
	<div class="row">
		<div class="col-xs-8">
			<div class="form-group" style="height:40px">
				<div class="input-group">
					<quick-datepicker ng-model="start_time" init-value="10:00:00"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i> 
				</div>
			</div>
			<div class="form-group">
				<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size="10">
					<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>
			<div class="form-group">
				<select class="form-control" name="server_id2" ng-model="formData.server_id2" ng-init="formData.server_id2=0">
					<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>
			<div class="form-group">
					<textarea name="gift_data" ng-model="formData.gift_data"
						placeholder="<?php echo Lang::get('serverapi.server_tip') ?>"
						rows="15" required class="form-control"></textarea>
			</div>
			<div class="form-group" style="height:40px">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_open')?>"
				ng-click="process('/game-server-api/cross/worldx-open')">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_connect')?>"
				ng-click="process('/game-server-api/cross/worldx-update')">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_world_signup')?>"
				ng-click="process('/game-server-api/cross/worldx-signup')">
				<input type='button' class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_lookup') ?>"
				ng-click="lookup('/game-server-api/cross/worldx-lookup')" style="margin-left:5px"/>
				
				<input type='button' class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_look') ?>"
				ng-click="look('/game-server-api/cross/worldx-look')" style="margin-left:5px"/>
			</div>
			
			<div class="alert alert-danger">
				<b>
				温馨提示：天下第一娱乐版必须在比赛开启当日10点后设置开启。
				<br>
				<?php echo Lang::get('serverapi.tournament_attention')?>
				</b>
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.id" name="id" placeholder="<?php echo Lang::get('serverapi.enter_id')?>">
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.password" name="password" placeholder="<?php echo Lang::get('serverapi.enter_password')?>">
			</div>
			<div class="col-md-4" style="padding:0">
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('serverapi.close') ?>"
				ng-click="process('/game-server-api/cross/worldx-close')" />
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