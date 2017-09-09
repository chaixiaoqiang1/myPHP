<script>
	function LogSearchController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};

		$scope.processcheck = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/upload/advicecheck',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
				$scope.task = 0;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}

		$scope.update_task = function(target) {
			$scope.formData.change_task_id = target.getAttribute('data'); 
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/change/taskstatus',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}

		$scope.change_task = function(target) {
			$scope.formData.task_id = target.getAttribute('data'); 
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/show/task',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.task = data;
				$scope.items = 0;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});			
		}
	}
</script>
<div class="col-xs-12" ng-controller="LogSearchController">
	<div class="row">
		<div class="eb-content" style="padding-top:20px;">
			<form method="post" ng-submit="processcheck()" onsubmit="return false;">
				<div class="form-group">
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
					<div class="form-group" style="padding-top:40px">
						<select class="form-control" name="check_type"
							id="check_type" ng-model="formData.check_type"
							ng-init="formData.check_type=0">
							<option value="0">请选择查询分组</option>
							<option value="1">官网</option>
							<option value="2">安卓SDK</option>
							<option value="3">IOS-SDK</option>
							<option value="4">eastblue</option>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control" name="is_finished"
							id="is_finished" ng-model="formData.is_finished"
							ng-init="formData.is_finished=0">
							<option value="0">请选择任务状态</option>
							<option value="1">未完成</option>
							<option value="2">已完成</option>
						</select>
					</div>
					<div class="input-group">
						<input type="text" name="username_check" id="username_check" ng-model="formData.username_check" placeholder="请输入查询的花名" />
					</div>	
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="submit" class="btn btn-default" value="查询任务"/>
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
					<td><b>创建时间</b></td>
					<td><b>创建人</b></td>
					<td><b>进行状态</b></td>
					<td><b>上次更新时间</b></td>
					<td><b>任务类型</b></td>
					<td><b>任务内容</b></td>
					<td><b>修改</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.time}}</td>
					<td>{{t.username}}</td>
					<td>{{t.is_finished}}</td>
					<td>{{t.finish_time}}</td>
					<td>{{t.task_type}}</td>
					<td>{{t.task_msg}}</td>
					<td><input type="button" value="修改" ng-click="change_task($event.target)" data="{{t.id}}"/></td>
				</tr>
			</tbody>
			<tbody>
				<tr ng-repeat="t in task">
					<td>{{t.created_time}}</td>
					<td>{{t.username}}</td>
					<td>{{t.task_status}}</td>
					<td>{{t.last_update_time}}</td>
					<td>{{t.task_type}}</td>
					<td>{{t.task_msg}}</td>
					<td></td>
				</tr>
				<tr ng-repeat="single_task in task">
					<td>{{single_task.created_time}}</td>
					<td><input type="text" name="change_username" id="change_username" ng-model="formData.change_username" /></td>
					<td><select class="form-control" name="change_status"
							id="change_status" ng-model="formData.change_status">
							<option value="0">未开始</option>
							<option value="1">进行中</option>
							<option value="2">线下测试中</option>
							<option value="3">线上测试中</option>
							<option value="4">已完成</option>
						</select></td>
					<td>{{single_task.last_update_time}}</td>
					<td><select class="form-control" name="change_type"
							id="change_type" ng-model="formData.change_type">
							<option value="1">官网</option>
							<option value="2">安卓SDK</option>
							<option value="3">IOS-SDK</option>
							<option value="4">eastblue</option>
						</select></td>
					<td><textarea name="change_explain" id="change_explain" ng-model="formData.change_explain" /></textarea></td>
					<td><input type="button" value="提交" ng-click="update_task($event.target)" data="{{single_task.id}}"/></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>