<script>
	function crossWarsLordsController($scope, $http, alertService, $filter){
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
				var result = data;
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
				var result = data;
				var len = result.length;
				for (var i = 0; i < len; i++) {
					if (result[i].status == 'ok') {
						alertService.add('success', result[i].msg);
					}else if (result[i].status == 'error') {
						alertService.add('danger', result[i].msg);
					}
				};
			}).error(function(data){
				alertService.add('error', data[i].msg);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="crossWarsLordsController">
	<div class="row">
		<div class="col-xs-8">
			<div class="form-group" style="height:40px">
				<div class="input-group">
					<quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
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
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.num" name="num" placeholder="<?php echo Lang::get('serverapi.enter_nums')?>">
			</div>
			<div class="form-group" style="height:40px">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_cross_open')?>"
				ng-click="process('/game-server-api/cross/wars-open')">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_cross_connect')?>"
				ng-click="process('/game-server-api/cross/wars-update')">
				<input type="button" class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_cross_signup')?>"
				ng-click="process('/game-server-api/cross/wars-signup')">
				<input type='button' class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_lookup') ?>"
				ng-click="lookup('/game-server-api/cross/wars-lookup')" style="margin-left:5px"/>
				
				<input type='button' class="btn btn-primary" value="<?php echo Lang::get('serverapi.tournament_look') ?>"
				ng-click="look('/game-server-api/cross/wars-look')" style="margin-left:5px"/>
			</div>
			
			<div class="alert alert-danger">
				<b><?php echo Lang::get('serverapi.tournament_attention')?></b>
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.id" name="id" placeholder="<?php echo Lang::get('serverapi.enter_id')?>">
			</div>
			<div class="form-group" style="height:30px">
				<input type="text" class="form-control" ng-model="formData.password" name="password" placeholder="<?php echo Lang::get('serverapi.enter_password')?>">
			</div>
			<div class="col-md-4" style="padding:0">
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('serverapi.close') ?>"
				ng-click="process('/game-server-api/cross/wars-close')" />
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