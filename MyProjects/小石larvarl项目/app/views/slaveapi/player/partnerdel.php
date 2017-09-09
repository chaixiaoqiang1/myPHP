<script>
	function PartnerDelController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/yysg/player/partnerdel',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PartnerDelController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group col-md-8" style="padding:0">
					<select class="form-control" name="choice" ng-model="formData.choice" ng-init="formData.choice=0">
						<option value="0"><?php echo Lang::get('player.select_by_player_id') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_name') ?></option>
					</select>
				</div>
				<div class="form-group col-md-8" style="padding:0">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="<?php echo Lang::get('slave.id_or_name');?>"
						required ng-model="formData.id_or_name" name="id_or_name" />
				</div>
				<div class="clearfix">
                	<br/>
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
			<br>
			<div class="form-group" >
					<div class="col-md-4" style="padding-left: 0">
						<select class="form-control" name="mid" ng-model="formData.mid" ng-init="formData.mid=69">
							<option value="69">只查询分解记录</option>
							<option value="0">查询所有类型记录</option>
						</select>
					</div>
					<div class="col-md-2">
						<input type="button" class="btn btn-info" value="<?php echo Lang::get('basic.btn_show') ?>" 
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>玩家ID</b></td>
					<td><b>操作</b></td>
					<td><b>武将</b></td>
					<td><b>操作时间</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.mid}}</td>
					<td>{{t.table_id}}</td>
					<td>{{t.time}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>