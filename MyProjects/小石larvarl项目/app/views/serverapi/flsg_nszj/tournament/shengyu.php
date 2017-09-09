<script>
	function meleeTournamentController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.formData.is_all = 0;/*0代表圣域争霸*/
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
				var result = data.result;
				var len = result.length;
				for (var i=0; i < len; i++) {
					if (result[i].status == 'connected') {
						alertService.add('success', result[i].msg);
					} else if (result[i]['status'] == 'disconnected') {
	            		alertService.add('primary', result[i].msg);
					} else if (result[i]['status'] == 'error') {
	            		alertService.add('danger', result[i].msg);
					}else if (result[i]['status'] == 'connecting') {
	            		alertService.add('warning', result[i].msg);
					}
				}
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};

		$scope.close = function(url) {
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
	}
</script>
<div class="col-xs-12" ng-controller="meleeTournamentController">
	<div class="row">
		<div class="eb-content">
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
				<div class="col-md-6" style="padding: 0">
					<select class="form-control" name="server_id2"
						id="select_game_server" ng-model="formData.server_id2"
						ng-init="formData.server_id2=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
			</div>
			<div class="form-group" style="height: 30px;">
				<span style = "color:red; font-size:16px">注意：开之前确认好是要开圣域争霸还是全服圣域争霸</span>
			</div>
			<div class="form-group" style="height: 30px;">
				<span style = "color:red; font-size:16px"><?php echo Lang::get('serverapi.shengyu1')?></span>
			</div>
			<div class="form-group" style="height: 30px;">
				<span style = "color:red; font-size:16px"><?php echo Lang::get('serverapi.shengyu2')?></span>
			</div>


			<div class="form-group" style="height: 40px;">
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.shengyu') ?>"
						ng-click="process('/game-server-api/nszj/shengyu')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.shengyu_lookup') ?>"
						ng-click="lookup('/game-server-api/nszj/shengyu/lookup')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.shengyu_close') ?>"
						ng-click="close('/game-server-api/nszj/shengyu/close')" />
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