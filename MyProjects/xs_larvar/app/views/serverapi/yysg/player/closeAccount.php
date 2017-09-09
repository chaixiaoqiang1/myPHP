<script>
	function closeAccountController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.auto = 0;
		$scope.$watch($scope.auto,function() {
			if($scope.auto == 0){
				$scope.auto_process();
				$scope.auto = 1;
			}
		});
		$scope.processFrom = function(url) {
			$scope.formData.url_type = 0;
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				if(result.status == 'ok'){
					alertService.add('success', result.msg);
				}else if(result.status == 'error'){
					alertService.add('danger', result.msg);
				}
				$scope.items = data.items;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.auto_process = function() {
			$scope.items = [];
			$scope.formData.url_type = 1;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/yysg/closeAccount',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
				$scope.items = data;
			});
		};
		$scope.searchFrom = function(url) {
			$scope.formData.url_type = 2;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			alertService.alerts = $scope.alerts;
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
<div class="col-xs-12" ng-controller="closeAccountController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group">
					<div class="form-group">
						<select class="form-control" name="is_banned" ng-model="formData.is_banned" ng-init="formData.is_banned=1">
							<option value="1"><?php echo Lang::get('serverapi.select_freeze');?></option>
							<option value="0"><?php echo Lang::get('serverapi.select_freeze2');?></option>
						</select>
					</div>
					<div class="form-group" ng-if="formData.is_banned==1">
						<select class="form-control" name="ban_time" ng-model="formData.ban_time" ng-init="formData.ban_time=0">
							<option value="0">永久封禁(需要手动解封)</option>
							<option value="3600">1小时</option>
							<option value="86400">1天</option>
							<option value="259200">3天</option>
						</select>
					</div>
					<?php if('yysg' == $game_code){ ?>
					<div class="form-group">
						<div class="col-md-6" style="padding-left: 0">
							<input type="text" class="form-control" id="player_name"
								placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"
								ng-model="formData.player_name" name="player_name" />
						</div>
						<div class="col-md-6">
							<input type="text" class="form-control" id="player_id"
								placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>"
								ng-model="formData.player_id" name="player_id" />
						</div>
					</div>
					<?php }else{ ?>
						<div class="form-group">
							<input type="text" class="form-control" id="player_id"
								placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>"
								ng-model="formData.player_id" name="player_id" />
						</div>
					<?php }?>
				</div>
                <div class="form-group">
                	<div class="col-md-10" style="padding-left: 0;">
                    	<input type="text" class="form-control" id="close_reason"
                           placeholder="<?php echo Lang::get('serverapi.enter_close_reason') ?>"
                           ng-model="formData.close_reason" name="close_reason" />
                    </div>
                    <div class="col-md-2">
                    	<input type="submit" class="btn btn-warning"
                    	ng-click="processFrom('/game-server-api/yysg/closeAccount')"
                    		value="<?php echo Lang::get('basic.btn_submit') ?>" />
                    </div>
                </div>
                <div style="padding-top: 5px;">
                	<p><font size="3">--以下为查询记录部分--</p></font>
                </div>
                <div class="form-group">
                    <div class="col-md-5" style="padding: 0">
                        <div class="input-group">
                            <quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                    <div class="col-md-5" style="padding: 0">
                        <div class="input-group">
                            <quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                    <div class="col-md-2">
                    	<input type="submit" class="btn btn-info"
                    	ng-click="searchFrom('/game-server-api/yysg/closeAccount')"
                    		value="<?php echo Lang::get('basic.btn_show') ?>" />
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>time</b></td>
					<td><b>player_id</b></td>
					<td><b>player_name</b></td>
					<td><b>operator</b></td>
					<td><b>type</b></td>
					<td><b>reason</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.operate_time}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.operator}}</td>
					<td>{{t.operation_type}}</td>
					<td>{{t.reason}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>