<script>
    function TestMidController($scope, $http, alertService, $filter) {  
    	$scope.alerts = [];
		$scope.formData = {};
		$scope.result = [];

		$scope.processFrom = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/mid/test',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', 'Please Check Result:');
				$scope.result = data.result;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="TestMidController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="col-md-10" style="padding:0">
					<select class="form-control" name="server_ids" required
						id="select_game_server" ng-model="formData.server_ids"
						ng-init="formData.server_ids=0" multiple="true" size= "10">
						<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<table class="table table-striped col-md-8">
					<thead>
						<tr class="info">
							<td><b><?php echo Lang::get('slave.key');?></b></td>
							<td><b><?php echo Lang::get('slave.value');?></b></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="number" class="form-control" required
								placeholder="<?php echo Lang::get('slave.mid')?>" ng-model="formData.mid"?></td>
							<td><input type="submit" class="btn btn-primary"
							value="<?php echo Lang::get('basic.btn_submit') ?>" /></td>
						</tr>
						<tr><td><?php echo Lang::get('slave.key')?></td><td></td></tr>
						<?php for($i = 0; $i <= 6; $i++) { ?>
						<tr><td><input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.key')?>"
						ng-model="formData.key<?php echo $i; ?>"?></td>
							<td><input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.value')?>"
						ng-model="formData.value<?php echo $i; ?>"?></td>
						</tr><?php } ?>
						<tr><td><?php echo Lang::get('slave.array_key')?></td><td></td></tr>
						<tr><td><input type="text" class="form-control"
								placeholder="<?php echo Lang::get('slave.array_key')?>"
								ng-model="formData.array_key"?></td><td>
								<textarea class="form-control" rows="5" placeholder="参数是一个数组的，每一行是数组内的一项"
								ng-model="formData.array_value"?></textarea></td></tr>
						<tr><td><?php echo Lang::get('slave.array_key_value')?></td><td></td></tr>
						<tr><td><input type="text" class="form-control"
								placeholder="<?php echo Lang::get('slave.array_key_value')?>"
								ng-model="formData.array_key_value_key"?></td><td>
								<textarea class="form-control" rows="5"  placeholder="每行一个参数名和对应的值，以空格分开"
								ng-model="formData.array_key_value_value"?></textarea></td></tr>
						<tr><td><?php echo Lang::get('slave.loop_key')?></td><td></td></tr>
						<tr><td><input type="text" class="form-control"
								placeholder="<?php echo Lang::get('slave.loop_key')?>"
								ng-model="formData.loop_key"?></td><td>
								<textarea class="form-control" rows="5" placeholder="将多次调用接口，逐行使用此处的值"
								ng-model="formData.loop_value"?></textarea></td></tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="col-xs-12" style="min-height:300px">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.result');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="r in result">
					<td>{{r}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>