<script >
	function refluxFirstPayAndAnony($scope, $http, alertService, $filter)
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

<div class="col-xs-12" ng-controller="refluxFirstPayAndAnony">
	<div class="row" >
		<div class="eb-content" style="width: 100%; min-width: 300px; background-color:#F0FFFF ;">
			<form action="" method="post" role="form" ng-submit="processFrom('/game-server-api/poker/reFpAnony')" 
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
			<tr class="info">
				<td><?php echo Lang::get('timeName.time_date')?></td>
				<td><?php echo Lang::get('timeName.reflux')?></td>
				<td><?php echo Lang::get('timeName.firstPay')?></td>
				<td><?php echo Lang::get('timeName.anonyPlayer')?></td>
			</tr>
		</thead>
		<tbody>
				<tr ng-repeat="line in items">
					<td>{{line.date}}</td>
					<td>{{line.reflux}}</td>
					<td>{{line.firstPay}}</td>
					<td>{{line.anonyPlayer}}</td>
				</tr>
		</tbody>
		</table>
	</div>
</div>