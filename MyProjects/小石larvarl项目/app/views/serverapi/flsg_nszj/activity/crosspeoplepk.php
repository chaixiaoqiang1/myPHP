<script>
	function crossPeoplePKController($scope, $http, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			$scope.formData.url_type = 1;
			alertService.alerts = $scope.alerts;
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
		};
		$scope.update = function(url){
			$scope.formData.url_type = 2;
			alertService.alerts = $scope.alerts;
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
		};
		$scope.close = function(url){
			$scope.formData.url_type = 3;
			alertService.alerts = $scope.alerts;
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
		};
		$scope.init = function(url){
			$scope.formData.url_type = 4;
			alertService.alerts = $scope.alerts;
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
	}
</script>
<div class="col-xs-12" ng-controller="crossPeoplePKController" id="<?php  echo $game_code;?>">
	<div class="row">
		<div class="col-xs-9">
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
					<option value="0"><?php echo Lang::get('serverapi.select_main_game_server')?></option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>
			<div class="form-group">
				<textarea name="text_data" ng-model="formData.text_data" required class="form-control"
				rows="15" placeholder="<?php echo Lang::get('serverapi.cross_people_pk_tip') ?>"></textarea>
			</div>
			<div class="form-group" style="height:40px">
				<input type="button" class="btn btn-warning" value="<?php echo Lang::get('serverapi.cross_server_open')?>"
				ng-click="process('/game-server-api/cross/server/all/pk')">
				<input type="button" class="btn btn-warning" value="<?php echo Lang::get('serverapi.cross_server_connect')?>"
				ng-click="update('/game-server-api/cross/server/all/pk')">
				<input type="button" class="btn btn-warning" value="<?php echo Lang::get('serverapi.quanmin_pk_init')?>"
				ng-click="init('/game-server-api/cross/server/all/pk')">
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('serverapi.close') ?>"
				ng-click="close('/game-server-api/cross/server/all/pk')">
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-6">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>