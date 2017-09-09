<script>
	function findBossKillerNumController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.start_time = null;
	    $scope.end_time = null;
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/find-boss-killer-num',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data;
				//alertService.add('danger', data.num);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="findBossKillerNumController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/economy/find-boss-killer-num" method="get" role="form"
				ng-submit="processFrom('/slave-api/economy/find-boss-killer-num')"
				onsubmit="return false;">
				<div class="well">
					<label><?php echo Lang::get('serverapi.select_game_server') ?></label>
					<select class="form-control" name="server_id" id="select_game_server" 
					ng-model="formData.server_id" ng-init="formData.server_id=-1" multiple="multiple" ng-multiple="true" size=10>
						<option value=0 style="font-weight:bold"><?php echo Lang::get('serverapi.select_all_server');?></option>
						<?php foreach ($server as $k => $v) { ?>
							<option value=<?php echo $v->server_id?>><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height:30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<br />
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped table-hover">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.server_name');?></b></td>
					<td><b><?php echo Lang::get('slave.action_time');?></b></td>
					<td><b><?php echo Lang::get('slave.killer_num')?></b></td>
				</tr>
			</thead>
			<tbody ng-repeat="t in total">
				<tr>
					<td>{{t.server_name}}</td>
					<td>{{t.action_time}}</td>
					<td>{{t.num}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>