<script >
	function dailyChipsController($scope, $http, alertService, $filter)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.formData.btn = 0;
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd');		
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if (data.error == "没有数据") {
					alertService.add('danger', data.error);
				}else{
					$scope.items = data;
				}
				//alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});

		};
	}
</script>

<div class="col-xs-12" ng-controller="dailyChipsController">
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/poker/dailyGiveChips" method="post" role="form" ng-submit="processFrom('/game-server-api/poker/daily')" 
				onsubmit="return false;" style="margin-left:0px">
				<div style="width:300px; float:left">
					<p><b><?php echo Lang::get('timeName.start_time') ?></b></p>
					<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
				</div>
				<div style="width:300px; float:left">
					<p><b><?php echo Lang::get('timeName.end_time') ?></b></p>
					<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
				</div>
				<!--
				<div class="form-group" style="width:600px">
					<div class="col-md-6" style="width:600px">
						<h4><?php echo Lang::get('serverapi.write_chips') ?></h4>
						<input type="text" class="form-control ng-pristine ng-invalid ng-invalid-required" 
								ng-model="formData.chips" name="chips" placeholder="" />
					</div>
				</div>
				<div class="form-group" style="width:600px margin-top:10px">
					<div class="col-md-6" style="width:600px">
						<h4><?php echo Lang::get('serverapi.write_player_id');?></h4>
						<input class="form-control ng-pristine ng-valid" type="text" placeholder="" name="player_id" ng-model="formData.player_id">
					</div>
					<div class="col-md-6" style="width:600px">
						<h4><?php echo Lang::get('serverapi.write_player_uid');?></h4>
						<input  class="form-control ng-pristine ng-valid" type="text" placeholder="" name="player_uid" ng-model="formData.player_uid">
					</div>
					<div class="col-md-6" style="width:600px">
						<h4><?php echo Lang::get('serverapi.write_player_name');?></h4>
						<input class="form-control ng-pristine ng-valid" type="text" placeholder="" name="player_name" ng-model="formData.player_name">
					</div>
				</div>
				-->
				<div style="width:100px; float:left; height:auto">
					<input type="submit" class="btn btn-default" style="margin-top:28px" value="<?php echo Lang::get('basic.btn_submit') ?>" style="display:block"/>	
				</div>
			</form>	 
		</div>
	</div>
		<br>
		<p><?php echo Lang::get('timeName.time_ps') ?></p>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><?php echo Lang::get('timeName.time_date')?></td>
				<td><?php echo Lang::get('timeName.time_total') ?></td>
				<td><?php echo Lang::get('timeName.time_signin')?></td>
				<td><?php echo Lang::get('timeName.time_login')?></td>
				<td><?php echo Lang::get('timeName.time_sday')?></td>
				<td><?php echo Lang::get('timeName.time_smallgame')?></td>
				<td><?php echo Lang::get('timeName.time_turntable')?></td>
				<td><?php echo Lang::get('timeName.time_bankrupity')?></td>
				<td><?php echo Lang::get('timeName.time_timebox')?></td>
				<td><?php echo Lang::get('timeName.time_dailytask')?></td>
				<td><?php echo "Robot" ?></td>
			</tr>
		</thead>
		<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.date}}</td>
					<td>{{t.total}}</td>
					<td>{{t.signin}}</td>
					<td>{{t.login}}</td>
					<td>{{t.sday}}</td>
					<td>{{t.smallgame}}</td>
					<td>{{t.turntable}}</td>
					<td>{{t.bankrupity}}</td>
					<td>{{t.timebox}}</td>
					<td>{{t.dailytask}}</td>
					<td>{{t.robotlose}}</td>
				</tr>
		</tbody>
		</table>
	</div>
</div>