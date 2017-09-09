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
			<form action="/game-server-api/poker/dailyTfRecover" method="post" role="form" ng-submit="processFrom('/game-server-api/poker/tfRecover')" 
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
				<td><?php echo Lang::get('timeName.tf_total')?></td>
				<td><?php echo Lang::get('timeName.tf_blind10')?></td>
				<td><?php echo Lang::get('timeName.tf_blind20')?></td>
				<td><?php echo Lang::get('timeName.tf_blind50')?></td>
				<td><?php echo Lang::get('timeName.tf_blind100')?></td>
				<td><?php echo Lang::get('timeName.tf_blind200')?></td>
				<td><?php echo Lang::get('timeName.tf_blind500')?></td>
				<td><?php echo Lang::get('timeName.tf_blind1000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind2000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind2500')?></td>
				<td><?php echo Lang::get('timeName.tf_blind5000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind10000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind20000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind25000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind50000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind100000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind200000')?></td>
				<td><?php echo Lang::get('timeName.tf_blind500000')?></td>
			</tr>
		</thead>
		<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.date          }}</td>
					<td>{{t.total         }}</td>
					<td>{{t.blind10       }}</td>
					<td>{{t.blind20       }}</td>
					<td>{{t.blind50       }}</td>
					<td>{{t.blind100      }}</td>
					<td>{{t.blind200      }}</td>
					<td>{{t.blind500      }}</td>
					<td>{{t.blind1000     }}</td>
					<td>{{t.blind2000     }}</td>
					<td>{{t.blind2500     }}</td>
					<td>{{t.blind5000     }}</td>
					<td>{{t.blind10000    }}</td>
					<td>{{t.blind20000    }}</td>
					<td>{{t.blind25000    }}</td>
					<td>{{t.blind50000    }}</td>
					<td>{{t.blind100000   }}</td>
					<td>{{t.blind200000   }}</td>
					<td>{{t.blind500000   }}</td>
				</tr>
		</tbody>
		</table>
	</div>
</div>