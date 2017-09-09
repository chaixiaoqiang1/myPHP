<script>
	function openServerActivityController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
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
<div class="col-xs-12" ng-controller="openServerActivityController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<select class="form-control" name="server_id"
					id="select_game_server" ng-model="formData.server_id"
					ng-init="formData.server_id=0" multiple="multiple"
					ng-multiple="true" size=10>
					<optgroup
						label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
				</select>
			</div>
			<div class="form-group">
					<select class="form-control" name="kaifu_id" id="kaifu_id"
						ng-model="formData.kaifu_id" ng-init="formData.kaifu_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_kaifu_id') ?></option>
						<?php foreach ($kaifus as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->titleDec;?></option>
						<?php } ?>		
					</select>
			</div>

			<div class="form-group" style="height: 30px;">
				<span style = "color:red; font-size:16px"><?php echo Lang::get('serverapi.kaifu_introduce1')?></span>
			</div>
			<div class="form-group" style="height: 30px;">
				<span style = "color:red; font-size:16px"><?php echo Lang::get('serverapi.kaifu_introduce2')?></span>
			</div>


			<div class="form-group" style="height: 40px;">
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_set') ?>"
						ng-click="process('/game-server-api/promotion/open-server')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_lookup') ?>"
						ng-click="lookup('/game-server-api/promotion/open-server/lookup')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.promotion_close') ?>"
						ng-click="process('/game-server-api/promotion/open-server/close')" />
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
				<div class="panel-heading"><?php echo Lang::get('serverapi.promotion_info') ?></div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('serverapi.yuanbao_server')?></dt>
						<dd>{{t.server_name}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_name')?></dt>
						<dd>{{t.kaifu_name}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_is_open')?></dt>
						<dd>{{t.is_open}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_last_days')?></dt>
						<dd>{{t.last_days}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_left_time')?></dt>
						<dd>{{t.left_time}}</dd>
						<?php
						/*
						<dt><?php echo Lang::get('serverapi.promotion_open_left_time')?></dt>
						<dd>{{t.open_left_time}}</dd>
						*/
						?>
						
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>