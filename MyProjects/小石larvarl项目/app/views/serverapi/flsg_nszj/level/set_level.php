<script>
	function AllServerLevel($scope, $http, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};

		$scope.process = function(url){
			$scope.formData.url_type = 1;
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				var len = result.length;
				for (var i = 0; i < len; i++) {
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
		$scope.look = function(url){
			$scope.formData.url_type = 2;
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				var len = result.length;
				for (var i = 0; i < len; i++) {
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
	}
</script>
<div class="col-xs-12" ng-controller="AllServerLevel">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size="10">
					<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>
			<div class="form-group">
				<input type="text" class="form-control" name="aserver_level"
					ng-model="formData.aserver_level" placeholder="<?php echo Lang::get('serverapi.enter').Lang::get('serverapi.aserver_level');?>"/>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<input type="button" class="btn btn-info" value="<?php echo Lang::get('basic.btn_show');?>"
						ng-click="look('/game-server-api/all/server/level')"/>
				</div>
				<div class="col-md-3">
					<input type="button" class="btn btn-warning" value="<?php echo Lang::get('basic.btn_set').'/'.Lang::get('basic.update');?>"
						ng-click="process('/game-server-api/all/server/level')"/>
				</div>
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