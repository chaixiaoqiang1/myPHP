<script>
	function activityStatus($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url,status) {
			alertService.alerts = $scope.alerts;
			$scope.formData.status = status;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
				if($scope.items.is_ok == true){
					display_alert();
				}
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	
	}
	function display_alert()
 		{
 	 		alert("操作成功");
 		}
</script>
<div id='query' class="col-xs-12" ng-controller="activityStatus">
	<div class="row">
		<div class="eb-content">
			<div class="clearfix">
				<br />
			</div>
			<div class="form-group">
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
			</div>
			<br>
			<div class="form-group">
					<select class="form-control" name="activity_id" id="form_type"
						ng-model="formData.activity_id" ng-init="formData.activity_id=0">
						<option value="0"><?php echo Lang::get('serverapi.promotion_name') ?></option>
						<?php foreach ($activity_name as $k=>$v) { ?>
						<option value="<?php echo $k ?>"><?php echo $v;?></option>
						<?php } ?>		
					</select>
				</div>
			<br>
			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-primary"
					value="<?php echo '开活动' ?>"
					ng-click="process('/game-server-api/poker/activityStatus',1)" />

					<input type='button' class="btn btn-primary"
					value="<?php echo '关活动' ?>"
					ng-click="process('/game-server-api/poker/activityStatus',0)" 
					/>
			</div>
			<br>
			<div class="col-xs-12">
				<table class="table table-striped">	
						<tr>
							<td>活动</td>
							<td>状态</td>
							<td>开始时间</td>
							<td>结束时间</td>
						</tr>
						<tr ng-repeat="t in items.data track by $index">
							<td>{{t.name}}</td>
							<td>{{t.status}}</td>
							<td>{{t.start_time}}</td>
							<td>{{t.end_time}}</td>
						</tr>
				</table>
			</div>
			

		</div>
	</div>

</div>