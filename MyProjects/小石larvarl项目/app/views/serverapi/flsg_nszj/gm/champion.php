<script>
	function battleLegaueController($scope, $http, $filter, alertService) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		$scope.items = [];

		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/battle/champion' ,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.da = data;
				$scope.res1 = data.data1;
				$scope.res2 = data.data2;
				$scope.name1 = data.league_name1;
				$scope.name2 = data.league_name2;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}

		$scope.download = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'    : '/game-server-api/battle/champion/download',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				alertService.add('success', 'OK');
				window.location.replace("/game-server-api/battle/champion/download?now=" + data.now);
			}).error(function(){
				alertService.add('danger', '下载失败');
			}) 
		}
	}
</script>
<div class="col-xs-12" ng-controller="battleLegaueController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/game-server-api/gm/replied" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=10>
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($server as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding-left: 15px ;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding-left:15px;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
					<div class="form-group" style="height: 30px;">
						<br/>
						<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.battle_introduce1')?></span>
					</div>
				<br><br/>
				<div class="col-md-2" style="padding: 15">
					<input type="submit" class="btn btn-default" style=""
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
				</div>
				<div class="col-md-2" style="">
					<input type='button' class="btn btn-warning"
						value="<?php echo Lang::get('serverapi.download_csv') ?>"
						ng-click="download('/game-server-api/battle/champion/download')"  style ="margin-left:-15px"/>
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
	<div>
		<div class="row margin-top-10 col-xs-6" style="width :50%">
			<div class="panel panel-success">
				<div class="panel-heading"><?php echo Lang::get('serverapi.first_battle') ?>:   {{name1}}</div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<table class="table table-striped" >
							<thead>
								<tr class="info">
									<td><b><?php echo Lang::get('serverapi.player_name')?></b></td>
									<td><b><?php echo Lang::get('serverapi.player_id');?></b></td>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="t in res1">
									<td>{{t.name}}</td>
									<td>{{t.player_id}}</td>
								</tr>
							</tbody>
						</table>
					</dl>
				</div>
			</div>
		</div>
		<div class="row margin-top-10 col-xs-6">
			<div class="panel panel-success">
				<div class="panel-heading"><?php echo Lang::get('serverapi.second_battle') ?>:  {{name2}}</div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<table class="table table-striped" >
							<thead>
								<tr class="info">
									<td><b><?php echo Lang::get('serverapi.player_name')?></b></td>
									<td><b><?php echo Lang::get('serverapi.player_id');?></b></td>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="t in res2">
									<td>{{t.name}}</td>
									<td>{{t.player_id}}</td>
								</tr>
							</tbody>
						</table>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>