<script >
	function backStatCpntroller($scope, $http, alertService, $filter)
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
	var flag=false;
	function checkAll($scope)
	{
		$scope.check = function(target){
			if(!flag){
				$scope.formData.cc = true;
				$scope.formData.c0 = true;
				$scope.formData.c1 = true;
				$scope.formData.c2 = true;
				$scope.formData.c3 = true;
				$scope.formData.c4 = true;
				$scope.formData.c5 = true;
				$scope.formData.c6 = true;
				$scope.formData.c7 = true;
				$scope.formData.c8 = true;
				flag = true;
			}else{
				$scope.formData.cc = false;
				$scope.formData.c0 = false;
				$scope.formData.c1 = false;
				$scope.formData.c2 = false;
				$scope.formData.c3 = false;
				$scope.formData.c4 = false;
				$scope.formData.c5 = false;
				$scope.formData.c6 = false;
				$scope.formData.c7 = false;
				$scope.formData.c8 = false;
				flag = false;
			}
		}
	}
</script>

<div class="col-xs-12" ng-controller="backStatCpntroller">
	<div class="row" >
		<div class="eb-content" style="width: 100%; min-width: 300px; background-color:#F0FFFF ;">
			<form action="" method="post" role="form" ng-submit="processFrom('/game-server-api/poker/backStat')" 
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
				<div style="width:100px; float:left; height:auto">
					<b><input type="submit" class="btn btn-default" style="color:black;width:100px;margin-top:24px;font-weight:bold;font-size: 16px" value="<?php echo Lang::get('basic.btn_submit');?>" style="display:block"/>	</b>
				</div>
			</form>	 
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" style="width: 100%; min-width: 300px; ">
		<table class="table table-striped">
		<thead>
			<tr class="info" ng-controller="checkAll">
				<td><?php echo Lang::get('timeName.time_date')?>(全选<input id='cc' type='checkbox' name='cc' 
					ng-click="check($event.target)" ng-model="formData.cc" checked='false'/>)</td>
				<td><?php echo Lang::get('timeName.timeD1-3')?><input type='checkbox' name='c0' 
					ng-model="formData.c0"/></td>
				<td><?php echo Lang::get('timeName.timeD4-5')?><input type='checkbox' name='c1'
					ng-model="formData.c1" /></td>
				<td><?php echo Lang::get('timeName.timeD6-7')?><input type='checkbox' name='c2' 
					ng-model="formData.c2"/></td>
				<td><?php echo Lang::get('timeName.timeD8-14')?><input type='checkbox' name='c3' 
					ng-model="formData.c3"/></td>
				<td><?php echo Lang::get('timeName.timeD15-30')?><input type='checkbox' name='c4' 
					ng-model="formData.c4"/></td>
				<td><?php echo Lang::get('timeName.timeM1-2')?><input type='checkbox' name='c5' 
					ng-model="formData.c5"/></td>
				<td><?php echo Lang::get('timeName.timeM2-3')?><input type='checkbox' name='c6' 
					ng-model="formData.c6"/></td>
				<td><?php echo Lang::get('timeName.timeM3-6')?><input type='checkbox' name='c7' 
					ng-model="formData.c7"/></td>
				<td><?php echo Lang::get('timeName.timeM6-a')?><input type='checkbox' name='c8' 
					ng-model="formData.c8"/></td>
			</tr>
		</thead>
		<tbody>
				<tr ng-repeat="line in items">
					<td>{{line.date}}</td>
					<td>{{line.timeD1}}</td>
					<td>{{line.timeD4}}</td>
					<td>{{line.timeD6}}</td>
					<td>{{line.timeD8}}</td>
					<td>{{line.timeD15}}</td>
					<td>{{line.timeM1}}</td>
					<td>{{line.timeM2}}</td>
					<td>{{line.timeM3}}</td>
					<td>{{line.timeM6}}</td>
				</tr>
		</tbody>
		</table>
	</div>
</div>