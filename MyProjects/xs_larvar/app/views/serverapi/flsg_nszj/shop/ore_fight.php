<script>
	function OreFightController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.is_open = 1;
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
		$scope.look = function(url) {
			alertService.alerts = $scope.alerts;
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
					}else if (result[i].status == 'error') {
						alertService.add('danger', result[i].msg);
					}
				};
			}).error(function(data){
				alertService.add('error', data.error);
			});
		};
		$scope.close = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.is_open = 0;
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
					}else if (result[i].status == 'error') {
						alertService.add('danger', result[i].msg);
					}
				};
			}).error(function(data){
				alertService.add('error', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="OreFightController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
                <select class="form-control" name="server_id"
                        id="select_game_server" ng-model="formData.server_id"
                        ng-init="formData.server_id=0" multiple="multiple"
                        ng-multiple="true" size=15>
                    <optgroup
                        label="<?php echo Lang::get('serverapi.select_game_server') ?>(按住Ctrl可多选)">
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
                        <?php } ?>
                    </optgroup>
                </select>
            </div>
			<div class="form-group" style="height: 40px;">
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="开启活动"
						ng-click="process('/game-server-api/ore/fight')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="查看活动"
						ng-click="look('/game-server-api/ore/fight/look')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-warning"
						value="关闭活动"
						ng-click="close('/game-server-api/ore/fight')" />
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