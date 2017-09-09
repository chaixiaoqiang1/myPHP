<script>
	function FreezeLogController($scope, $http, alertService, $filter){
		$scope.formData = {};
		$scope.alerts = [];
		$scope.process = function(){
			alertService = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'    : '/game-server-api/freeze/log',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}

			}).success(function(data){
				if (data.error == "没有数据") {
					alertService.add('danger', data.error);
				}else{
					$scope.items = data;
				}
			}).error(function(){
				alertService.add('danger', data.error);
			});
		}
	} 
</script>
<div class="col-xs-12" ng-controller="FreezeLogController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group ">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name ?></option>
						<?php } ?>		
					</select>
				</div>

				<div class="form-group ">
					<select class="form-control" name="type" ng-model="formData.type" ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('serverapi.select_type') ?></option>
						<option value="1"><?php echo Lang::get('serverapi.select_freeze')  ?></option>
						<option value="2"><?php echo Lang::get('serverapi.select_banner')  ?></option>
							
					</select>
				</div>

				<div class="form-group col-md-6">
					<input type="text" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id"  style="margin-left:-15px"/>
				</div>
				<div class="form-group col-md-6">
					<input type="text" name="player_name" ng-model="formData.player_name" class="form-control" placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"/>
				</div>
			<br>
			<br>
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" />
					</div>
			</div>
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('serverapi.server_name')?></b></td>
					<td><b><?php echo Lang::get('serverapi.player_name')?></b></td>
					<td><b><?php echo Lang::get('serverapi.player_id')?></b></td>
					<td><b><?php echo Lang::get('serverapi.freeze_banner');?></b></td>
					<td><b><?php echo Lang::get('serverapi.operate_days');?></b></td>
					<td><b><?php echo Lang::get('serverapi.operator');?></b></td>
					<td><b><?php echo Lang::get('serverapi.operate_time');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.type}}</td>
					<td>{{t.days}}</td>
					<td>{{t.operater}}</td>
					<td>{{t.date}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>

</div>