<script>
	function beautyGiftNSZJController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
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
<div class="col-xs-12" ng-controller="beautyGiftNSZJController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
						<lable>开始时间</lable>
						<quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>

						<lable style="margin-left:10%">结束时间</lable>
						<quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
			</div>
			<div class="clearfix">

			</div>
			<div class="form-group">
					<nobr>
						<lable>初始次数</lable>
						<input type="number" min="0" step="1" ng-model="formData.init_times"
						ng-init="formData.init_times=0" 
						required />
					
						<lable style="margin-left:10%">每日增加次数</lable>
						<input type="number" min="0" step="1" ng-model="formData.dayly_times"
						ng_init="formData.dayly_times=10"
						required />
					</nobr>
			</div>

			<div class="form-group">
				<select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id"
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
			<div class="form-group" style="height: 40px;">
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_set') ?>"
						ng-click="process('/game-server-api/promotion/beautyNsOpen')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_lookup') ?>"
						ng-click="lookup('/game-server-api/promotion/beautyNsLook')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.promotion_close') ?>"
						ng-click="process('/game-server-api/promotion/beautyNsClose')" />
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
						<dt><?php echo Lang::get('serverapi.server_name')?></dt>
						<dd>{{t.server_name}}</dd>
						<dt><?php echo Lang::get('serverapi.status')?></dt>
						<dd>{{t.is_open}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_open_time')?></dt>
						<dd>{{t.open_time}}</dd>
						<dt><?php echo Lang::get('serverapi.promotion_close_time')?></dt>
						<dd>{{t.close_time}}</dd>
						<dt><?php echo Lang::get('serverapi.init_times')?></dt>
						<dd>{{t.init_times}}</dd>
						<dt><?php echo Lang::get('serverapi.dayly_times')?></dt>
						<dd>{{t.dayly_times}}</dd>					
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>