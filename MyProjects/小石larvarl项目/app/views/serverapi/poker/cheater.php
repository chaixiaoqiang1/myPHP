<script >
	function cheaterController($scope, $http, alertService, $filter)
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

<div class="col-xs-12" ng-controller="cheaterController">
	<div class="row" >
		<div class="eb-content">
			<form action="#" method="post" role="form" ng-submit="processFrom('/game-server-api/poker/cheater')" 
				onsubmit="return false;" style="margin-left:0px">
				<div style="width:300px; float:left">
					<p><b><?php echo Lang::get('timeName.start_time') ?></b></p>
					<quick-datepicker ng-model="start_time" init-value="<?php echo $time_now_s ?>"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
				</div>
				<div style="width:300px; float:left">
					<p><b><?php echo Lang::get('timeName.end_time') ?></b></p>
					<quick-datepicker ng-model="end_time" init-value="<?php echo $time_now_e ?>"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
				</div>
				<div style="width:100px; float:left; height:auto">
					<input type="submit" class="btn btn-success" style="margin-top:28px" value="<?php echo Lang::get('basic.btn_show') ?>" style="display:block"/>	
				</div>
			</form>	 
		</div>
	</div>
		<br>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped">
		<thead>
			<tr class="info">


			</tr>
		</thead>
		<tbody>
				<tr ng-repeat="t in items">

				</tr>
		</tbody>
		</table>
	</div>
</div>