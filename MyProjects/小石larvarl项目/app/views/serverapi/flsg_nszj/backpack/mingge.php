<script>
	function mingGeLogController($scope, $http, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.items = [];
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
<div class="col-xs-12" ng-controller="mingGeLogController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="processFrom('/game-server-api/mingge/log')" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($server as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name ?></option>
						<?php } ?>		
					</select>
				</div>
				
				<div class="form-group">
					<select class="form-control" name="mingge_id" ng-model="formData.mingge_id" ng-init="formData.mingge_id=0">
						<option value="0"><?php echo Lang::get('serverapi.enter_mingge_name') ?></option>
						<?php foreach ($mingge as $k => $v) { ?>
						<option value="<?php echo $v->markid?>"><?php echo $v->markid . ':' . $v->markname;?></option>
						<?php } ?>		
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
			<br>
			<div class="form-group" style="height: 30px;">
				<br/>
				<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.mingge_introduce1')?></span>
			</div>
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
					<td><b><?php echo Lang::get('slave.server_name')?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
					<td><b><?php echo Lang::get('slave.player_id')?></b></td>
				<!-- 	<td><b><?php echo Lang::get('slave.mingge_type');?></b></td> -->
					<td><b><?php echo Lang::get('slave.action_time');?></b></td>
				<!-- 	<td><b><?php echo Lang::get('slave.mingge_id');?></b></td> -->
					<td><b><?php echo Lang::get('slave.mingge_name1');?></b></td>
					<td><b><?php echo Lang::get('slave.mingge_name2');?></b></td> 
					<td><b><?php echo Lang::get('slave.mingge_exp1');?></b></td>
					<td><b><?php echo Lang::get('slave.mingge_exp2');?></b></td>
				<!-- 	<td><b><?php echo Lang::get('slave.mingge_level');?></b></td> -->
					<td><b><?php echo Lang::get('slave.action_type');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.time}}</td>
					<td>{{t.mingge_name1}}</td>
					<td>{{t.mingge_name2}}</td>
					<td>{{t.from_exp}}</td>
					<td>{{t.to_exp}}</td>
					<td>{{t.action_type}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>

</div>