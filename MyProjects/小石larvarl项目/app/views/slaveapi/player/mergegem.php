<script>
	function MergeGemController($scope, $http, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			$scope.items = {};
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' 	 : url,
				'data' 	 : $.param($scope.formData),
				'headers':{'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.items = data;
			}).error(function(data){
				alertService.add('danger',data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="MergeGemController">
	<div class="row">
		<div class="eb-content">

			<div class="form-group">
				<div class="col-md-6" style="padding-left:0px;">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server');?></option>
						<?php foreach($servers as $server){ ?>
							<option value="<?php echo $server->server_id;?>"><?php echo $server->server_name; ?></option>
						<?php }?>
					</select>
				</div>
				<div class="col-md-6" style="padding-right:0px;">
					<select class="form-control" name="id_or_name" ng-model="formData.id_or_name" ng-init="formData.id_or_name=1">
						<option value="1"><?php echo Lang::get('serverapi.gift_used_player_id');?></option>
						<option value="2"><?php echo Lang::get('serverapi.gift_used_player_name');?></option>
					</select>
				</div>
			</div>
			<div class="form-group" style="padding-top:10px;">
				<input type="text" name="player" ng-model="formData.player" class="form-control"
				placeholder="<?php echo Lang::get('slave.id_or_name') .' '.Lang::get('slave.player_choosable'); ?>" style="margin-top:15px"/>
			</div>
			<div class="form-group" style="height:35px;">
				<div class="col-md-5" style="padding:10px;">
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-5" style="padding:10px;">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-2" style="padding:10px;">
					<input type="button" class="btn btn-primary" value="<?php echo Lang::get('basic.btn_show')?>"
					ng-click="process('/slave-api/flsg/mergegem/log')"/>
				</div>
			</div>
		</div>
	</div>

	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type" 
				close="alert.close()">
				{{alert.msg}}
			</alert>
		</div>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id')?></b></td>
					<td><b><?php echo Lang::get('slave.main_id')?></b></td>
					<td><b><?php echo Lang::get('slave.secondary_id')?></b></td>
					<td><b><?php echo Lang::get('slave.execute_action')?></b></td>
					<td><b><?php echo Lang::get('slave.oper_time')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.main_id}}</td>
					<td>{{t.secondary_id}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.time}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>