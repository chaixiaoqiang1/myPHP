<script>
	function turnplateController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.is_timing = 0;
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
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
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
		$scope.timing = function(url) {
			if (!confirm('确定每个伺服器的活动开启时间大于上次所开与当前冲突活动的结束时间2分钟以上?')) {
				return;
			}
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.is_timing = 1;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data;
				if (result.status == 'ok') {
					alertService.add('success', result.msg);
				} else if (result['status'] == 'error') {
            		alertService.add('danger', result.msg);
				}
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="turnplateController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<br />
			</div>
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
                <select class="form-control" name="turnplate_type"
                        id="select_game_activity" ng-model="formData.turnplate_type"
                        multiple="multiple"
						ng-multiple="false"size=10>
                    	<optgroup
						label="请选择活动(一次只限开一个活动)">
                        <?php foreach ($activities as $k => $v) { ?>
                            <option value="<?php echo $v['label']; ?>"><?php echo $v['name']; ?></option>
                        <?php } ?>
                	</optgroup>
                </select>
            </div>

			<div class="form-group" style="height: 40px; padding-top: 8px;">
				<div class="col-md-3" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_set') ?>"
						ng-click="process('/game-server-api/promotion/turnplate')" />
				</div>
				<div class="col-md-3" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_lookup') ?>"
						ng-click="lookup('/game-server-api/promotion/turnplate/lookup')" />
				</div>
				<div class="col-md-3" style="padding: 0">
					<input type='button' class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.promotion_close') ?>"
						ng-click="process('/game-server-api/promotion/turnplate/close')" />
				</div>
				<div class="col-md-3" style="padding: 0">
					<input type='button' class="btn btn-warning"
						value="<?php echo Lang::get('serverapi.promotion_timing') ?>"
						ng-click="timing('/game-server-api/promotion/turnplate')" />
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
						<dd>{{t.name}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_open_time')?></dt>
						<dd>{{t.open_time}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_close_time')?></dt>
						<dd>{{t.close_time}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_is_open')?></dt>
						<dd>{{t.is_open}}</dd>
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