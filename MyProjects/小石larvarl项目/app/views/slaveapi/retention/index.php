<script>
	function getPlayerRetentionController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = '';
		$scope.end_time = '';
		$scope.formData = {};
		$scope.total = [];

		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/player/retention',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				
				$scope.total = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getPlayerRetentionController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/player/retention" method="get" role="form"
				ng-submit="processFrom('/slave-api/player/retention')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_all_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<?php if('1' == $ifshowanonymous){?>
				<div class="form-group">
					<input type="radio" name="is_anonymous" ng-model="formData.is_anonymous" value="0" ng-value="0" ng-init="formData.is_anonymous=0"/>
					<?php echo Lang::get('user.is_formal');?>
					<input type="radio" name="is_anonymous" ng-model="formData.is_anonymous" value="1" ng-value="1"/>
					<?php echo Lang::get('user.is_anonymous');?>
				</div>
				<?php } ?>
				<div class="form-group" style="height:10px;">
					<div class="col-md-6" style="padding:0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding:0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix"><br /></div>
				<input type="submit" class="btn btn-default" style="margin-top-10" value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
					<td><b><?php echo Lang::get('slave.date');?></b></td>
					<td><b><?php echo Lang::get('slave.created_player_number');?></b></td>
					<td><b><?php echo Lang::get('slave.days_2');?></b></td>
					<td><b><?php echo Lang::get('slave.days_3');?></b></td>
					<td><b><?php echo Lang::get('slave.days_4');?></b></td>
					<td><b><?php echo Lang::get('slave.days_5');?></b></td>
					<td><b><?php echo Lang::get('slave.days_6');?></b></td>
					<td><b><?php echo Lang::get('slave.days_7');?></b></td>
					<td><b><?php echo Lang::get('slave.days_14');?></b></td>
					<?php if('1' == $ifshow30days){ ?>
					<td><b><?php echo Lang::get('slave.days_30');?></b></td>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td>{{t.retention_time}}</td>
					<td>{{t.created_player_number}}</td>
					<td>{{t.days_2}}({{100*t.days_2/t.created_player_number|number:2}}%)</td>
					<td>{{t.days_3}}({{100*t.days_3/t.created_player_number|number:2}}%)</td>
					<td>{{t.days_4}}({{100*t.days_4/t.created_player_number|number:2}}%)</td>
					<td>{{t.days_5}}({{100*t.days_5/t.created_player_number|number:2}}%)</td>
					<td>{{t.days_6}}({{100*t.days_6/t.created_player_number|number:2}}%)</td>
					<td>{{t.days_7}}({{100*t.days_7/t.created_player_number|number:2}}%)</td>
					<td>{{t.days_14}}({{100*t.days_14/t.created_player_number|number:2}}%)</td>
					<?php if('1' == $ifshow30days){ ?>
					<td>{{t.days_30}}({{100*t.days_30/t.created_player_number|number:2}}%)</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
	</div>
</div>