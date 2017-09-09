<script>
	function CreateParterLog($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.show = 0;
		$scope.process = function() {
			$scope.items = {};
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/partner/log',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
				$scope.items = data;
			}).error(function(data) {
				$scope.show = 0;
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="CreateParterLog">
	<div class="row">
		<div class="eb-content">
				<div class="form-group" style="height:35px;">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group">
				<?php echo Lang::get('slave.select_server'); ?>
					<select class="form-control" required name="choice" id="server_ids"
						ng-model="formData.server_ids" multiple="true" size="6">
						<?php foreach ($servers as $server) { ?>
						<option value="<?php echo $server->server_id;?>"><?php echo $server->server_name;?></option>
						<?php  } ?>
					</select>
				</div>
				<div class="form-group">
				<?php echo Lang::get('slave.choose_wj'); ?>
					<select class="form-control" name="choice" id="wj_ids"
						ng-model="formData.wj_ids" required multiple="true" size="10">
						<option value="0"><?php echo Lang::get('slave.all').Lang::get('slave.partner');?></option>
						<?php foreach ($wjs as $key => $value) { ?>
						<option value="<?php echo $key;?>"><?php echo $value;?></option>
						<?php  } ?>
					</select>
				</div>
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.btn_show') ?>" 
						ng-click="process()"/>
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
	<div class="col-xs-12">
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.partner'); ?></b></td>
					<td><b><?php echo Lang::get('slave.call_wj').Lang::get('slave.count'); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.partner}}</td>
					<td>{{t.count}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>