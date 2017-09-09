<script>
	function addPromotionController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
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
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="addPromotionController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<select class="form-control" name="server_ids"
					id="select_game_server" ng-model="formData.server_ids"
					ng-init="formData.server_ids=0" multiple="multiple"
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
				<div class="col-md-6" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.set_update') ?>"
						ng-click="process('/game-server-api/dailyUpdate')" />
				</div>
				<div class="col-md-6" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.check_update') ?>"
						ng-click="lookup('/game-server-api/dailyUpdate/check')" />
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
	<div class="row margin-top-10 col-xs-6">
		<div ng-repeat="t in items">
			<div class="panel panel-info">
				<div class="panel-heading"><?php echo Lang::get('serverapi.update_info') ?></div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('serverapi.yuanbao_server')?></dt>
						<dd>{{t.server_name}}</dd>
						<dt><?php echo Lang::get('serverapi.daily_update_time')?></dt>
						<dd>{{t.daily_update_time}}</dd>
						<dt><?php echo Lang::get('serverapi.version_update_time')?></dt>
						<dd>{{t.version_update_time}}</dd>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>