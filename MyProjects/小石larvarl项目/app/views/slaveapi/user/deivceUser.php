<script>
	function DeviceUsersController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : '/slave-api/input/device/user',
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.keys = data.keys;
				$scope.values = data.values;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="DeviceUsersController">
	<div class="row">
		<div class="col-xs-8">
			<form class="form-group" ng-submit="process()" onsubmit="return false" style="margin:aoto">
				<div class="form-group col-md-5">
					<b>请输入设备号，每行一个</b>
					<textarea class="form-control" required ng-model="formData.device_ids" style="width:300px;height:500px">
						
					</textarea>
				</div>
				<div class="form-group col-md-5">
					<b>&nbsp;</b>
					<select class="form-control" name="data_type"
                        id="data_type" ng-model="formData.data_type"
                        ng-init="formData.data_type='create'">
                        <option value="create"><?php echo Lang::get('slave.data_create'); ?></option>
                        <option value="level"><?php echo Lang::get('slave.data_level'); ?></option>
                        <option value="order"><?php echo Lang::get('slave.data_order'); ?></option>
                	</select>
				</div>
				<div class="form-group col-md-5" ng-if="formData.data_type=='level'">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('slave.select_server'); ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-5">
					<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-primary">
				</div>
			</form>

			<div class="row margin-top-10">
				<div class="col-xs-4">
					<alert ng-repeat="alert in alerts" type="alert.type"
						close="alert.close()">{{alert.msg}}</alert>
				</div>
			</div>
			<div class="col-xs-8">
				<table class="table table-striped">
					<thead>
						<tr class="info">
							<td ng-repeat="k in keys">{{k}}</td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="s in values">
							<td ng-repeat="v in s">{{v}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>